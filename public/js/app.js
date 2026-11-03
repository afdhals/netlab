(() => {
    'use strict';

    const app = window.NETLEARN || { lessons: [], quiz: [], pairs: [], glossary: [] };
    const storageKey = 'netlearn-tkj-progress-v1';
    const learnerIdKey = 'netlearn-tkj-client-id';
    const initialState = { completed: [], currentLesson: app.lessons[0]?.id || 'jaringan', activityDone: [], results: {} };
    const state = loadState();
    const learnerId = getLearnerId();
    let databaseReady = false;
    let progressSyncTimer = null;
    const $ = (selector, scope = document) => scope.querySelector(selector);
    const $$ = (selector, scope = document) => [...scope.querySelectorAll(selector)];

    function loadState() {
        try {
            const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
            return saved && typeof saved === 'object' ? { ...initialState, ...saved } : { ...initialState };
        } catch (_) {
            return { ...initialState };
        }
    }

    function getLearnerId() {
        try {
            let id = localStorage.getItem(learnerIdKey);
            if (!id) {
                id = globalThis.crypto?.randomUUID?.() || 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
                    const random = Math.random() * 16 | 0;
                    return (char === 'x' ? random : (random & 0x3 | 0x8)).toString(16);
                });
                localStorage.setItem(learnerIdKey, id);
            }
            return id;
        } catch (_) {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
                const random = Math.random() * 16 | 0;
                return (char === 'x' ? random : (random & 0x3 | 0x8)).toString(16);
            });
        }
    }

    function setDatabaseStatus(message, connected) {
        const node = $('#database-status');
        if (!node) return;
        node.classList.toggle('database-online', connected);
        node.classList.toggle('database-offline', !connected);
        const label = $('span', node);
        if (label) label.textContent = message;
    }

    function saveState() {
        try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch (_) { /* local storage may be disabled */ }
        updateDashboard();
        updateResults();
        if (databaseReady) scheduleProgressSync();
    }

    function scheduleProgressSync() {
        clearTimeout(progressSyncTimer);
        progressSyncTimer = setTimeout(syncProgressToDatabase, 350);
    }

    async function syncProgressToDatabase() {
        try {
            const response = await fetch(`${app.routes.api}/learner-state/progress`, {
                method: 'PUT',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    client_id: learnerId,
                    completed_lessons: state.completed,
                    completed_activities: state.activityDone,
                    current_lesson: state.currentLesson || null,
                }),
            });
            if (!response.ok) throw new Error('Progress sync failed');
            setDatabaseStatus('Database tersambung', true);
        } catch (_) {
            setDatabaseStatus('Mode lokal · database belum tersambung', false);
        }
    }

    async function syncAssessmentToDatabase(type, answers) {
        try {
            const response = await fetch(`${app.routes.api}/assessment-results`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ client_id: learnerId, quiz_type: type, answers }),
            });
            if (!response.ok) throw new Error('Assessment sync failed');
            const payload = await response.json();
            if (payload.result) {
                state.results[type] = { ...state.results[type], ...payload.result };
                try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch (_) { /* local storage may be disabled */ }
            }
            setDatabaseStatus('Database tersambung', true);
        } catch (_) {
            setDatabaseStatus('Hasil tersimpan di browser', false);
        }
    }

    async function syncFromDatabase() {
        setDatabaseStatus('Memeriksa database…', false);
        try {
            const response = await fetch(`${app.routes.api}/learner-state?client_id=${encodeURIComponent(learnerId)}`, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Database is unavailable');
            const remote = await response.json();
            const serverProgress = remote.progress || {};
            state.completed = [...new Set([...(state.completed || []), ...(serverProgress.completed || [])])];
            state.activityDone = [...new Set([...(state.activityDone || []), ...(serverProgress.activityDone || [])])];
            state.currentLesson = state.currentLesson || serverProgress.currentLesson || initialState.currentLesson;
            const remoteResults = remote.results || {};
            const resultsToUpload = [];
            Object.entries(remoteResults).forEach(([type, serverResult]) => {
                const localResult = state.results?.[type];
                if (!localResult || new Date(serverResult.submitted_at || 0) > new Date(localResult.submitted_at || 0)) {
                    state.results[type] = serverResult;
                }
            });
            Object.entries(state.results || {}).forEach(([type, localResult]) => {
                const serverResult = remoteResults[type];
                if (localResult?.answers && (!serverResult || new Date(localResult.submitted_at || 0) > new Date(serverResult.submitted_at || 0))) {
                    resultsToUpload.push([type, localResult.answers]);
                }
            });
            databaseReady = true;
            try { localStorage.setItem(storageKey, JSON.stringify(state)); } catch (_) { /* local storage may be disabled */ }
            updateDashboard();
            updateResults();
            setDatabaseStatus('Database tersambung', true);
            await syncProgressToDatabase();
            for (const [type, answers] of resultsToUpload) await syncAssessmentToDatabase(type, answers);
        } catch (_) {
            databaseReady = false;
            setDatabaseStatus('Mode lokal · database belum tersambung', false);
            window.setTimeout(() => { if (!databaseReady) syncFromDatabase(); }, 15000);
        }
    }

    function esc(value) {
        return String(value).replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char]);
    }

    function markActivityDone(id) {
        if (!state.activityDone.includes(id)) state.activityDone.push(id);
        saveState();
    }

    function updateDashboard() {
        const total = app.lessons.length || 13;
        const done = state.completed.length;
        const percent = Math.round(done / total * 100);
        const put = (selector, value) => { const node = $(selector); if (node) node.textContent = value; };
        const bar = (selector, value) => { const node = $(selector); if (node) node.style.width = `${value}%`; };
        put('#side-progress-value', `${percent}%`);
        put('#side-progress-label', `${done} dari ${total} submateri selesai`);
        bar('#side-progress-bar', percent);
        put('#home-progress', percent);
        put('#home-progress-label', `${done} dari ${total} submateri sudah dituntaskan`);
        bar('#home-progress-bar', percent);
        put('#home-activity-count', state.activityDone.length);
        put('#activity-total', state.activityDone.length);
        bar('#activity-progress-bar', state.activityDone.length * 25);
        put('#activity-count', state.activityDone.length);
        const count = $('#activity-count');
        if (count) count.hidden = state.activityDone.length === 0;

        [1, 2].forEach((unit) => {
            const all = app.lessons.filter((lesson) => lesson.unit === unit);
            const unitDone = all.filter((lesson) => state.completed.includes(lesson.id)).length;
            put(unit === 1 ? '#unit-one-count' : '#unit-two-count', `${unitDone} / ${all.length} selesai`);
            bar(unit === 1 ? '#unit-one-bar' : '#unit-two-bar', all.length ? unitDone / all.length * 100 : 0);
        });

        $$('.lesson-dot[data-lesson-dot]').forEach((dot) => {
            const id = dot.dataset.lessonDot;
            if (state.completed.includes(id)) { dot.classList.add('lesson-done'); dot.textContent = '✓'; }
        });
        const next = app.lessons.find((lesson) => !state.completed.includes(lesson.id)) || app.lessons[0];
        if (next) {
            $$('[data-continue-link]').forEach((link) => { link.href = `${app.routes.lesson}/${encodeURIComponent(next.id)}`; });
            put('#continue-title', next.title);
            put('#continue-description', next.content?.[0]?.slice(0, 100) + '…' || 'Lanjutkan materi pembelajaran.');
            put('#continue-unit', `UNIT ${next.unit}`);
        }

        const lessonId = app.currentLesson;
        if (lessonId) {
            const badge = $(`[data-completion="${lessonId}"]`);
            const button = $(`[data-mark-complete="${lessonId}"]`);
            const isDone = state.completed.includes(lessonId);
            if (badge) { badge.classList.toggle('is-complete', isDone); badge.innerHTML = `<span>${isDone ? '✓' : '○'}</span> ${isDone ? 'Selesai' : 'Belum selesai'}`; }
            if (button) { button.classList.toggle('btn-secondary', isDone); button.classList.toggle('btn-primary', !isDone); button.innerHTML = isDone ? '✓ &nbsp; Sudah ditandai selesai' : 'Tandai materi selesai &nbsp; ✓'; }
        }
    }

    function updateResults() {
        const total = app.lessons.length || 13;
        const progress = Math.round(state.completed.length / total * 100);
        const put = (selector, value) => { const node = $(selector); if (node) node.textContent = value; };
        put('#result-lesson-count', state.completed.length);
        put('#result-activity-count', state.activityDone.length);
        put('#result-progress', progress);
        put('#results-count', state.completed.length);
        $$('.result-lesson[data-result-lesson]').forEach((row) => {
            const done = state.completed.includes(row.dataset.resultLesson);
            const marker = $('.result-lesson-number', row);
            const status = $('.state-pending, .state-done', row);
            if (marker) { marker.classList.toggle('done', done); marker.textContent = done ? '✓' : marker.dataset.original || marker.textContent; marker.dataset.original ||= marker.textContent; }
            if (status) { status.className = done ? 'state-done' : 'state-pending'; status.textContent = done ? 'Selesai' : 'Mulai'; }
        });
        ['pretest', 'posttest'].forEach((type) => {
            const card = $(`[data-result-card="${type}"]`);
            const result = state.results?.[type];
            if (!card || !result) return;
            const value = $('.result-value', card);
            const paragraph = $('p', card);
            const link = $('a', card);
            if (value) value.innerHTML = `<strong class="result-score">${result.score}<small> / ${result.total}</small></strong>`;
            if (paragraph) paragraph.textContent = `Terakhir dikerjakan ${result.date || 'tersimpan di perangkat ini'}.`;
            if (link) link.textContent = 'Ulangi evaluasi →';
        });
    }

    function svgDiagram(id) {
        const ink = '#17365d', blue = '#3e8cd4', cyan = '#56c8cf', muted = '#7790a6';
        const node = (x, y, label, core = false) => `<g><circle cx="${x}" cy="${y}" r="${core ? 20 : 15}" fill="${core ? '#e0f5f6' : '#fff'}" stroke="${core ? cyan : '#b7cee2'}" stroke-width="2"/>${core ? `<path d="M${x-8} ${y-4}h16M${x-8} ${y+2}h16M${x-4} ${y-10}v20" fill="none" stroke="${blue}" stroke-width="2" stroke-linecap="round"/>` : `<rect x="${x-8}" y="${y-6}" width="16" height="12" rx="2" fill="none" stroke="${blue}" stroke-width="1.8"/><path d="M${x-4} ${y+9}h8" stroke="${blue}" stroke-width="1.8" stroke-linecap="round"/>`}<text x="${x}" y="${y+30}" text-anchor="middle" font-size="10" fill="${muted}">${label}</text></g>`;
        const lines = (items) => `<g>${items.map(([x1,y1,x2,y2]) => `<line x1="${x1}" y1="${y1}" x2="${x2}" y2="${y2}" stroke="#a9bfd1" stroke-width="2"/>`).join('')}</g>`;
        let label = 'Diagram konsep jaringan'; let body = '';
        switch (id) {
            case 'jaringan':
                label = 'Diagram perangkat klien, switch, router, dan internet'; body = `${lines([[88,120,175,120],[210,120,285,120],[324,120,365,120],[175,120,175,65],[175,120,175,175]])}${node(70,120,'PC')}${node(195,120,'Switch',true)}${node(304,120,'Router',true)}${node(175,50,'Laptop')}${node(175,190,'Printer')}<circle cx="382" cy="120" r="17" fill="#f1f7fc" stroke="#b7cee2" stroke-width="2"/><text x="382" y="124" text-anchor="middle" font-size="16" fill="${blue}">⌁</text><text x="382" y="152" text-anchor="middle" font-size="9" fill="${muted}">Internet</text>`; break;
            case 'bus':
                label = 'Diagram topologi Bus dengan backbone dan terminator'; body = `<line x1="44" y1="112" x2="376" y2="112" stroke="${blue}" stroke-width="4"/><path d="M44 101v22M376 101v22" stroke="${cyan}" stroke-width="5"/>${[91,170,249,328].map((x,i)=>`<line x1="${x}" y1="112" x2="${x}" y2="71" stroke="#a9bfd1" stroke-width="2"/>${node(x,55,`Node ${i+1}`)}`).join('')}<text x="44" y="145" text-anchor="middle" font-size="9" fill="${muted}">terminator</text><text x="376" y="145" text-anchor="middle" font-size="9" fill="${muted}">terminator</text>`; break;
            case 'star':
                label = 'Diagram topologi Star dengan switch pusat'; body = `${lines([[210,115,210,48],[210,115,100,60],[210,115,320,60],[210,115,105,184],[210,115,315,184],[210,115,210,198]])}${node(210,115,'Switch',true)}${[[210,35,'PC 1'],[85,48,'PC 2'],[335,48,'PC 3'],[88,192,'PC 4'],[333,192,'PC 5'],[210,207,'Printer']].map(([x,y,t])=>node(x,y,t)).join('')}`; break;
            case 'ring':
                label = 'Diagram topologi Ring berbentuk lintasan tertutup'; body = `<path d="M112 56H308V174H112Z" fill="none" stroke="#a9bfd1" stroke-width="2"/>${[[112,56,'Node 1'],[308,56,'Node 2'],[308,174,'Node 3'],[112,174,'Node 4']].map(([x,y,t])=>node(x,y,t)).join('')}<path d="M160 47h93m-8-5 8 5-8 5" fill="none" stroke="${cyan}" stroke-width="3"/>`; break;
            case 'mesh':
                label = 'Diagram topologi Mesh dengan banyak jalur antar-node'; body = `${lines([[100,62,210,48],[210,48,320,62],[100,62,120,166],[100,62,320,168],[210,48,120,166],[210,48,320,168],[320,62,120,166],[320,62,320,168],[120,166,320,168]])}${[[100,62,'A'],[210,48,'B'],[320,62,'C'],[120,166,'D'],[320,168,'E']].map(([x,y,t])=>node(x,y,t)).join('')}`; break;
            case 'tree':
                label = 'Diagram topologi Tree yang tersusun hierarkis'; body = `${lines([[210,45,105,107],[210,45,315,107],[105,107,55,180],[105,107,155,180],[315,107,265,180],[315,107,365,180]])}${node(210,42,'Core',true)}${node(105,107,'Distribusi',true)}${node(315,107,'Distribusi',true)}${[[55,180,'PC'],[155,180,'PC'],[265,180,'PC'],[365,180,'PC']].map(([x,y,t])=>node(x,y,t)).join('')}`; break;
            case 'hybrid':
                label = 'Diagram Hybrid dengan backbone dan dua segmen Star'; body = `<text x="210" y="28" text-anchor="middle" font-size="10" fill="${muted}">BACKBONE ANTARSEGMEN</text><line x1="73" y1="48" x2="347" y2="48" stroke="${blue}" stroke-width="3"/><rect x="35" y="81" width="156" height="147" rx="12" fill="#f4f9fb" stroke="#d8e5ed"/><rect x="229" y="81" width="156" height="147" rx="12" fill="#f4f9fb" stroke="#d8e5ed"/>${lines([[112,48,112,119],[308,48,308,119],[112,119,70,169],[112,119,154,169],[308,119,266,169],[308,119,350,169]])}${node(112,119,'Switch A',true)}${node(308,119,'Switch B',true)}${[[70,169,'PC'],[154,169,'PC'],[266,169,'PC'],[350,169,'PC']].map(([x,y,t])=>node(x,y,t)).join('')}<text x="113" y="218" text-anchor="middle" font-size="9" fill="${muted}">SEGMENT STAR A</text><text x="307" y="218" text-anchor="middle" font-size="9" fill="${muted}">SEGMENT STAR B</text>`; break;
            case 'ipv4':
                label = 'IPv4 terdiri dari empat oktet, masing-masing delapan bit'; body = `<text x="210" y="36" text-anchor="middle" fill="${muted}" font-size="11">192.168.10.25</text>${['192','168','10','25'].map((v,i)=>`<g><rect x="${24+i*98}" y="61" width="82" height="62" rx="10" fill="${i===3?'#e2f6f5':'#eaf2fb'}" stroke="${i===3?cyan:'#c6d8e9'}"/><text x="${65+i*98}" y="89" text-anchor="middle" fill="${ink}" font-size="20" font-weight="700">${v}</text><text x="${65+i*98}" y="108" text-anchor="middle" fill="${muted}" font-size="10">Oktet ${i+1}</text><text x="${65+i*98}" y="152" text-anchor="middle" fill="${blue}" font-size="12">8 bit</text></g>`).join('')}<text x="210" y="198" text-anchor="middle" fill="${muted}" font-size="11">4 × 8 bit = 32 bit · rentang tiap oktet 0–255</text>`; break;
            case 'network-host':
                label = 'Diagram Network ID dan Host ID pada alamat IPv4'; body = `<rect x="38" y="76" width="205" height="60" rx="9" fill="#dff4f4" stroke="${cyan}"/><rect x="243" y="76" width="139" height="60" rx="9" fill="#eaf2fb" stroke="#b7cee2"/><text x="140" y="102" text-anchor="middle" fill="${ink}" font-size="16" font-weight="700">192.168.10</text><text x="140" y="121" text-anchor="middle" fill="${muted}" font-size="10">Network · /24</text><text x="312" y="102" text-anchor="middle" fill="${ink}" font-size="16" font-weight="700">25</text><text x="312" y="121" text-anchor="middle" fill="${muted}" font-size="10">Host · 8 bit</text><path d="M243 62v90" stroke="${blue}" stroke-dasharray="4 4" stroke-width="2"/><text x="210" y="185" text-anchor="middle" fill="${muted}" font-size="11">Batas prefix menentukan pemisahan bit</text>`; break;
            case 'kelas':
                label = 'Rentang oktet pertama pada kelas alamat IPv4'; body = `<path d="M52 122H368" stroke="#c4d5e4" stroke-width="8" stroke-linecap="round"/>${[['A','1–126',74],['B','128–191',151],['C','192–223',228],['D','224–239',305],['E','240–255',365]].map(([letter,range,x],i)=>`<g><circle cx="${x}" cy="122" r="15" fill="${i<3?'#dff4f4':'#eaf2fb'}" stroke="${i<3?cyan:'#9eb9d0'}" stroke-width="2"/><text x="${x}" y="84" text-anchor="middle" fill="${ink}" font-size="14" font-weight="700">${letter}</text><text x="${x}" y="160" text-anchor="middle" fill="${muted}" font-size="9">${range}</text></g>`).join('')}<text x="210" y="200" text-anchor="middle" fill="${muted}" font-size="10">A–C unicast · D multicast · E dicadangkan</text>`; break;
            case 'publik-privat':
                label = 'Jaringan privat menuju internet melalui router NAT'; body = `<rect x="35" y="55" width="185" height="125" rx="14" fill="#f1f7fc" stroke="#c6d8e9"/><text x="128" y="76" text-anchor="middle" fill="${muted}" font-size="10">Jaringan privat RFC 1918</text>${node(85,118,'Laptop')}${node(170,118,'PC')}${node(265,118,'Router / NAT',true)}<path d="M220 118h23m-7-5 7 5-7 5M287 118h58m-7-5 7 5-7 5" fill="none" stroke="${blue}" stroke-width="2"/><circle cx="368" cy="118" r="18" fill="#e0f5f6" stroke="${cyan}" stroke-width="2"/><text x="368" y="122" text-anchor="middle" fill="${blue}" font-size="15">⌁</text><text x="368" y="156" text-anchor="middle" fill="${muted}" font-size="9">Internet</text><text x="128" y="165" text-anchor="middle" fill="${blue}" font-size="9">10/8 · 172.16/12 · 192.168/16</text>`; break;
            case 'mask-cidr':
                label = 'Subnet mask dengan prefix 24 memisahkan bit jaringan dan host'; body = `<text x="210" y="50" text-anchor="middle" fill="${ink}" font-size="15" font-weight="700">/24 = 255.255.255.0</text>${Array.from({length:32},(_,i)=>`<g><rect x="${38+(i%16)*22}" y="${76+Math.floor(i/16)*46}" width="18" height="30" rx="4" fill="${i<24?'#dff4f4':'#eaf2fb'}" stroke="${i<24?cyan:'#b7cee2'}"/><text x="${47+(i%16)*22}" y="${96+Math.floor(i/16)*46}" text-anchor="middle" fill="${ink}" font-size="12">${i<24?'1':'0'}</text></g>`).join('')}<text x="210" y="184" text-anchor="middle" fill="${cyan}" font-size="11">24 bit jaringan</text><text x="210" y="212" text-anchor="middle" fill="${blue}" font-size="11">8 bit host</text>`; break;
            case 'subnetting':
                label = 'Satu jaringan /24 dibagi menjadi empat subnet /26'; body = `<text x="210" y="40" text-anchor="middle" fill="${ink}" font-size="14" font-weight="700">192.168.10.0/24 → 4 subnet /26</text>${[['.0','.63'],['.64','.127'],['.128','.191'],['.192','.255']].map(([a,b],i)=>`<g><rect x="${32+i*91}" y="76" width="79" height="86" rx="9" fill="${i%2===0?'#e0f5f6':'#edf4fa'}" stroke="${i%2===0?cyan:'#b7cee2'}"/><text x="${71+i*91}" y="102" text-anchor="middle" fill="${ink}" font-size="13" font-weight="700">${a}–${b}</text><text x="${71+i*91}" y="122" text-anchor="middle" fill="${muted}" font-size="10">64 alamat</text><text x="${71+i*91}" y="143" text-anchor="middle" fill="${blue}" font-size="10">/26</text></g>`).join('')}<text x="210" y="198" text-anchor="middle" fill="${muted}" font-size="11">Network: 0 · 64 · 128 · 192</text>`; break;
        }
        return `<svg class="diagram-svg" viewBox="0 0 420 230" role="img" aria-label="${esc(label)}">${body}</svg>`;
    }

    function initDiagrams() {
        $$('[data-diagram]').forEach((element) => { element.innerHTML = svgDiagram(element.dataset.diagram); });
    }

    function initNavigation() {
        const sidebar = $('#sidebar');
        const shell = $('.app-shell');
        $('[data-open-menu]')?.addEventListener('click', () => { sidebar?.classList.add('sidebar-open'); shell?.classList.add('mobile-open'); });
        $$('[data-close-menu]').forEach((button) => button.addEventListener('click', () => { sidebar?.classList.remove('sidebar-open'); shell?.classList.remove('mobile-open'); }));
    }

    function initSearch() {
        const input = $('#lesson-search'); const results = $('#search-results');
        if (!input || !results) return;
        input.addEventListener('input', () => {
            const query = input.value.trim().toLocaleLowerCase('id');
            if (!query) { results.hidden = true; results.innerHTML = ''; return; }
            const found = app.lessons.filter((lesson) => `${lesson.title} ${lesson.eyebrow} ${lesson.content.join(' ')}`.toLocaleLowerCase('id').includes(query)).slice(0, 6);
            results.innerHTML = found.length ? found.map((lesson) => `<a href="${app.routes.lesson}/${encodeURIComponent(lesson.id)}"><small>UNIT ${lesson.unit}</small><span>${esc(lesson.title)}</span><b>→</b></a>`).join('') : '<p>Tidak ada materi yang cocok.</p>';
            results.hidden = false;
        });
        document.addEventListener('click', (event) => { if (!event.target.closest('.search-wrap')) results.hidden = true; });
        input.addEventListener('keydown', (event) => { if (event.key === 'Escape') { results.hidden = true; input.blur(); } });
    }

    function initLesson() {
        const id = app.currentLesson;
        if (!id) return;
        state.currentLesson = id; saveState();
        $(`[data-mark-complete="${id}"]`)?.addEventListener('click', () => {
            if (!state.completed.includes(id)) state.completed.push(id);
            saveState();
        });
    }

    function feedback(node, type, message) {
        if (!node) return;
        node.hidden = false;
        node.className = `activity-feedback feedback-box feedback-${type}`;
        node.innerHTML = `<span>${type === 'correct' ? '✓' : type === 'wrong' ? '!' : '✧'}</span><span>${message}</span>`;
    }

    function initActivities() {
        const views = $$('[data-activity-view]');
        if (!views.length) return;
        const tabs = $$('[data-activity-tab]');
        function openActivity(id) {
            tabs.forEach((tab) => tab.classList.toggle('activity-tab-active', tab.dataset.activityTab === id));
            views.forEach((view) => { view.hidden = view.dataset.activityView !== id; });
        }
        tabs.forEach((tab) => tab.addEventListener('click', () => openActivity(tab.dataset.activityTab)));

        $$('[data-identify]').forEach((button) => button.addEventListener('click', () => {
            const correct = button.dataset.identify === 'Ring';
            $$('[data-identify]').forEach((option) => option.classList.toggle('option-selected', option === button));
            feedback($('[data-feedback="identify"]'), correct ? 'correct' : 'wrong', correct ? 'Tepat! Node membentuk lintasan tertutup dan setiap node memiliki dua tetangga.' : 'Belum tepat. Perhatikan lintasan melingkar dan dua tetangga pada setiap node.');
            if (correct) markActivityDone('identify');
        }));

        const rightOptions = $('#match-right-options');
        const matchHint = $('#match-hint');
        const matchOrder = [2, 0, 3, 1];
        let leftSelection = null; let rightSelection = null; let matched = [];
        if (rightOptions) {
            rightOptions.innerHTML = matchOrder.map((index, order) => `<button class="match-option" type="button" data-match-right="${index}"><span>${String(order + 1).padStart(2, '0')}</span>${esc(app.pairs[index].meaning)}</button>`).join('');
            const selectPair = (side, index) => {
                const selected = side === 'left' ? leftSelection : rightSelection;
                if (matched.includes(index)) return;
                if (side === 'left') leftSelection = index; else rightSelection = index;
                if (leftSelection !== null && rightSelection !== null) {
                    if (leftSelection === rightSelection) {
                        matched.push(leftSelection);
                        matchHint.classList.remove('feedback-text-wrong');
                        matchHint.innerHTML = `<span>✓</span>Tepat! ${esc(app.pairs[index].term)}: ${esc(app.pairs[index].meaning.toLocaleLowerCase('id'))}.<b id="match-count">${matched.length}/4</b>`;
                        if (matched.length === app.pairs.length) markActivityDone('match');
                    } else {
                        matchHint.classList.add('feedback-text-wrong');
                        matchHint.innerHTML = `<span>✧</span>Pasangan belum tepat. Coba pilih istilah dan fungsi yang sesuai.<b id="match-count">${matched.length}/4</b>`;
                    }
                    leftSelection = null; rightSelection = null;
                }
                $$('[data-match-left], [data-match-right]').forEach((option) => {
                    const optionIndex = Number(option.dataset.matchLeft ?? option.dataset.matchRight);
                    option.classList.toggle('match-selected', optionIndex === leftSelection || optionIndex === rightSelection);
                    option.disabled = matched.includes(optionIndex);
                    option.classList.toggle('match-done', matched.includes(optionIndex));
                    if (matched.includes(optionIndex) && !$('.match-check', option)) option.insertAdjacentHTML('beforeend', '<b class="match-check">✓</b>');
                });
                const count = $('#match-count'); if (count && matched.length === 0) count.textContent = `${matched.length}/4`;
            };
            $$('[data-match-left]').forEach((button) => button.addEventListener('click', () => selectPair('left', Number(button.dataset.matchLeft))));
            $$('[data-match-right]', rightOptions).forEach((button) => button.addEventListener('click', () => selectPair('right', Number(button.dataset.matchRight))));
        }

        const bitGrid = $('#bit-grid');
        if (bitGrid) {
            bitGrid.innerHTML = Array.from({ length: 8 }, (_, index) => `<button class="bit-toggle" type="button" data-bit="${index}" aria-label="Ubah bit ${7-index}"><small>2<sup>${7-index}</sup></small><b>0</b><span>${2 ** (7-index)}</span></button>`).join('');
            const bits = Array(8).fill(0);
            const refreshBits = () => {
                const value = bits.reduce((sum, bit, index) => sum + bit * 2 ** (7-index), 0);
                $$('.bit-toggle', bitGrid).forEach((button, index) => { button.classList.toggle('bit-on', !!bits[index]); $('b', button).textContent = String(bits[index]); });
                $('#binary-output').textContent = bits.join(''); $('#decimal-output').textContent = String(value);
                feedback($('[data-feedback="binary"]'), value === 173 ? 'correct' : 'neutral', value === 173 ? 'Benar! 128 + 32 + 8 + 4 + 1 = 173. Semua delapan bit membentuk satu oktet IPv4.' : `Nilai sekarang ${value}. Coba gabungkan 128 + 32 + 8 + 4 + 1.`);
                if (value === 173) markActivityDone('binary');
            };
            $$('.bit-toggle', bitGrid).forEach((button) => button.addEventListener('click', () => { const index = Number(button.dataset.bit); bits[index] = bits[index] ? 0 : 1; refreshBits(); }));
            refreshBits();
        }

        $('#ip-form')?.addEventListener('submit', (event) => {
            event.preventDefault();
            const correct = $('#ip-answer').value.trim() === '192.168.10.0';
            feedback($('[data-feedback="ip"]'), correct ? 'correct' : 'wrong', correct ? 'Betul. Nilai 25 masuk rentang blok 0–63, jadi network address-nya 192.168.10.0.' : 'Coba lagi: subnet /26 berukuran 64. Awal bloknya 0, 64, 128, dan 192.');
            if (correct) markActivityDone('ip');
        });

        updateActivityTabs();
    }

    function updateActivityTabs() {
        const ids = ['identify', 'match', 'binary', 'ip'];
        $$('[data-activity-tab]').forEach((tab) => {
            const id = tab.dataset.activityTab;
            const base = ids.indexOf(id) + 1;
            if (state.activityDone.includes(id)) {
                if (!$('i', tab)) tab.insertAdjacentHTML('beforeend', '<i>✓</i>');
            } else $('i', tab)?.remove();
            const number = $('span', tab); if (number) number.textContent = `0${base}`;
        });
        const count = $('#activity-total'); if (count) count.textContent = String(state.activityDone.length);
        const progress = $('#activity-progress-bar'); if (progress) progress.style.width = `${state.activityDone.length * 25}%`;
    }

    function initQuiz() {
        const choice = $('#evaluation-choice'); const quizRoot = $('#quiz-app');
        if (!choice || !quizRoot) return;
        const summary = $('#saved-quiz-summary');
        if (summary) {
            summary.innerHTML = ['pretest', 'posttest'].filter((type) => state.results?.[type]).map((type) => `<span>${type.toUpperCase()} terakhir: <b>${state.results[type].score}/${state.results[type].total}</b></span>`).join('');
        }
        const type = quizRoot.dataset.quizType;
        if (!type) return;
        choice.hidden = true;
        const root = $('#quiz-content');
        const questions = app.quiz;
        const answers = Array(questions.length).fill(-1);
        root.innerHTML = `<div class="quiz-heading"><div class="eyebrow">EVALUASI PEMBELAJARAN</div><h1>${type === 'pretest' ? 'Kenali pemahaman awalmu' : 'Uji pemahaman setelah belajar'}</h1><p>Pilih satu jawaban paling tepat pada setiap soal. Jawaban dapat diubah sebelum dikumpulkan.</p></div><div class="quiz-progress"><span id="quiz-count">0 dari ${questions.length} soal dijawab</span><div class="progress-track"><span id="quiz-progress-bar" style="width:0"></span></div></div><div class="quiz-list">${questions.map((question,index)=>`<article class="quiz-question"><div class="quiz-q-number">${String(index+1).padStart(2,'0')}</div><div class="quiz-q-content"><h3>${esc(question.q)}</h3><div class="quiz-options">${question.options.map((option,optionIndex)=>`<label class="quiz-option"><input type="radio" name="question-${index}" value="${optionIndex}"><span>${String.fromCharCode(65+optionIndex)}</span>${esc(option)}</label>`).join('')}</div></div></article>`).join('')}</div><div class="quiz-submit"><span>Jawaban wajib diisi untuk semua soal.</span><button class="btn btn-primary" id="submit-quiz" type="button" disabled>Kumpulkan jawaban →</button></div>`;
        $$('input[type="radio"]', root).forEach((input) => input.addEventListener('change', () => {
            const questionIndex = Number(input.name.split('-')[1]); answers[questionIndex] = Number(input.value);
            $$(`input[name="question-${questionIndex}"]`, root).forEach((option) => option.closest('.quiz-option')?.classList.toggle('quiz-option-selected', option.checked));
            const answered = answers.filter((answer) => answer >= 0).length;
            $('#quiz-count').textContent = `${answered} dari ${questions.length} soal dijawab`;
            $('#quiz-progress-bar').style.width = `${answered / questions.length * 100}%`;
            $('#submit-quiz').disabled = answered !== questions.length;
        }));
        $('#submit-quiz').addEventListener('click', () => {
            const score = answers.filter((answer,index) => answer === questions[index].answer).length;
            const recommendations = [...new Set(questions.filter((question,index) => answers[index] !== question.answer).map((question) => question.lesson))];
            const submittedAt = new Date().toISOString();
            const result = { score, total: questions.length, answers, recommendations, date: new Intl.DateTimeFormat('id-ID',{day:'numeric',month:'short',year:'numeric'}).format(new Date()), submitted_at: submittedAt };
            state.results[type] = result; saveState();
            syncAssessmentToDatabase(type, answers);
            showQuizResult(root, type, result, questions);
        });
    }

    function showQuizResult(root, type, result, questions) {
        const percentage = Math.round(result.score / result.total * 100);
        root.innerHTML = `<div class="page-heading"><div><div class="eyebrow">HASIL ${type.toUpperCase()}</div><h1>Evaluasi selesai</h1><p>Tinjau jawaban dan rekomendasi belajarmu di bawah ini.</p></div></div><div class="score-banner"><div class="score-ring" style="--score:${percentage}%"><div><strong>${percentage}</strong><span>SKOR</span></div></div><div><span class="eyebrow">NILAI ${type.toUpperCase()}</span><h2>${result.score} dari ${result.total} jawaban benar</h2><p>${result.score>=7?'Pemahamanmu sudah kuat. Lanjutkan dengan mengulang konsep penting.':result.score>=4?'Awal yang baik. Pelajari kembali bagian yang direkomendasikan.':'Gunakan rekomendasi di bawah sebagai peta untuk belajar kembali.'}</p><div class="score-actions"><a class="btn btn-secondary" href="${app.routes.evaluation}/${type}">Ulangi evaluasi</a><a class="btn btn-ghost" href="${app.routes.evaluation}/${type==='pretest'?'posttest':'pretest'}">Ke ${type==='pretest'?'posttest':'pretest'} →</a></div></div></div><div class="section-row eval-review-title"><div><div class="eyebrow">PEMBAHASAN</div><h2>Umpan balik per soal</h2></div></div><div class="review-list">${questions.map((question,index)=>{const correct=result.answers[index]===question.answer;const lesson=app.lessons.find((item)=>item.id===question.lesson);return `<div class="review-item ${correct?'review-good':'review-missed'}"><div class="review-mark">${correct?'✓':'!'}</div><div><span class="eyebrow">SOAL ${String(index+1).padStart(2,'0')} · ${correct?'BENAR':'TINJAU KEMBALI'}</span><h3>${esc(question.q)}</h3><p>${esc(question.explain)}</p>${!correct&&lesson?`<a class="text-button" href="${app.routes.lesson}/${encodeURIComponent(lesson.id)}">Pelajari ${esc(lesson.title)} →</a>`:''}</div></div>`;}).join('')}</div>`;
    }

    function initResults() {
        updateResults();
    }

    function initGlossary() {
        const input = $('#glossary-search'); const entries = $$('.glossary-item'); const count = $('#glossary-count'); const empty = $('#glossary-empty');
        if (!input) return;
        input.addEventListener('input', () => {
            const query = input.value.trim().toLocaleLowerCase('id'); let visible = 0;
            entries.forEach((entry) => { const show = entry.dataset.term.includes(query); entry.hidden = !show; if (show) visible++; });
            count.textContent = String(visible); empty.hidden = visible !== 0;
        });
    }

    initNavigation();
    initDiagrams();
    initLesson();
    initActivities();
    initQuiz();
    initResults();
    initGlossary();
    initSearch();
    updateDashboard();
    syncFromDatabase();
})();
