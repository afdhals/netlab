<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#112f57">
    <meta name="description" content="E-modul interaktif Topologi Jaringan dan Pengalamatan IPv4 untuk kelas XI TKJ.">
    <title>{{ $page === 'lesson' ? $lesson['title'] : $pageTitle }} · NETLEARN TKJ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="app-shell" data-page="{{ $page }}">
    <button class="mobile-scrim" type="button" aria-label="Tutup menu" data-close-menu></button>
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-mark"><span></span><span></span><span></span><span></span></div>
            <div><strong>NETLEARN<span> TKJ</span></strong><small>E-MODUL INTERAKTIF</small></div>
            <button class="mobile-close" type="button" aria-label="Tutup menu" data-close-menu>×</button>
        </div>
        <div class="student-chip"><div class="avatar">XI</div><div><strong>Peserta didik</strong><span>Kelas XI · TKJ</span></div><i class="status-dot"></i></div>
        <nav class="sidebar-nav" aria-label="Navigasi utama">
            <section class="nav-group"><p class="nav-heading">MULAI DI SINI</p>
                <a class="nav-item {{ $page === 'home' ? 'nav-active' : '' }}" href="{{ route('home') }}"><span class="nav-symbol">⌂</span><span>Beranda</span></a>
                <a class="nav-item {{ $page === 'guide' ? 'nav-active' : '' }}" href="{{ route('guide') }}"><span class="nav-symbol">▤</span><span>Petunjuk penggunaan</span></a>
                <a class="nav-item {{ $page === 'outcomes' ? 'nav-active' : '' }}" href="{{ route('outcomes') }}"><span class="nav-symbol">◎</span><span>Capaian belajar</span></a>
                <a class="nav-item {{ $page === 'concept' ? 'nav-active' : '' }}" href="{{ route('concept') }}"><span class="nav-symbol">⌘</span><span>Peta konsep</span></a>
            </section>
            <section class="nav-group"><p class="nav-heading">MATERI PEMBELAJARAN</p>
                <a class="nav-item {{ $page === 'lesson' && $lesson['unit'] === 1 ? 'nav-active' : '' }}" href="{{ route('lesson', 'jaringan') }}"><span class="nav-symbol">▦</span><span>Unit 1 · Topologi jaringan</span></a>
                <a class="nav-item {{ $page === 'lesson' && $lesson['unit'] === 2 ? 'nav-active' : '' }}" href="{{ route('lesson', 'ipv4') }}"><span class="nav-symbol">⌘</span><span>Unit 2 · Pengalamatan IP</span></a>
                <div class="lesson-nav">
                    @foreach ($lessons as $index => $item)
                        <a class="lesson-nav-item {{ $page === 'lesson' && $lesson['id'] === $item['id'] ? 'lesson-nav-active' : '' }}" href="{{ route('lesson', $item['id']) }}"><span class="lesson-dot" data-lesson-dot="{{ $item['id'] }}">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><span>{{ $item['title'] }}</span></a>
                    @endforeach
                </div>
            </section>
            <section class="nav-group"><p class="nav-heading">LATIHAN & REFERENSI</p>
                <a class="nav-item {{ $page === 'activities' ? 'nav-active' : '' }}" href="{{ route('activities') }}"><span class="nav-symbol">⌁</span><span>Aktivitas interaktif</span><i class="nav-count" id="activity-count" hidden>0</i></a>
                <a class="nav-item {{ $page === 'evaluation' ? 'nav-active' : '' }}" href="{{ route('evaluation') }}"><span class="nav-symbol">✓</span><span>Pretest & posttest</span></a>
                <a class="nav-item {{ $page === 'results' ? 'nav-active' : '' }}" href="{{ route('results') }}"><span class="nav-symbol">▤</span><span>Hasil belajar</span></a>
                <a class="nav-item {{ $page === 'summary' ? 'nav-active' : '' }}" href="{{ route('summary') }}"><span class="nav-symbol">▣</span><span>Rangkuman</span></a>
                <a class="nav-item {{ $page === 'glossary' ? 'nav-active' : '' }}" href="{{ route('glossary') }}"><span class="nav-symbol">Aa</span><span>Glosarium</span></a>
                <a class="nav-item {{ $page === 'about' ? 'nav-active' : '' }}" href="{{ route('about') }}"><span class="nav-symbol">◉</span><span>Tentang pengembang</span></a>
            </section>
        </nav>
        <div class="sidebar-bottom">
            <div class="side-progress"><div class="side-progress-top"><span>Progres belajarmu</span><strong id="side-progress-value">0%</strong></div><div class="progress-track"><span id="side-progress-bar" style="width:0"></span></div><small id="side-progress-label">0 dari {{ count($lessons) }} submateri selesai</small><a class="small-link" href="{{ route('results') }}">Lihat progres <span>→</span></a></div>
            <div class="side-footer"><i class="status-dot"></i> Belajar dengan ritmemu sendiri</div>
        </div>
    </aside>

    <div class="main-column">
        <header class="topbar"><div class="topbar-left"><button class="menu-toggle" type="button" aria-label="Buka menu" data-open-menu>☰</button><div class="breadcrumbs"><span>NETLEARN TKJ</span><b>/</b><strong>{{ $page === 'lesson' ? $lesson['title'] : $pageTitle }}</strong></div></div><div class="topbar-tools"><div class="database-status" id="database-status" aria-live="polite"><i></i><span>Memeriksa database…</span></div><div class="search-wrap"><span aria-hidden="true">⌕</span><input id="lesson-search" type="search" placeholder="Cari materi..." aria-label="Cari materi"><div class="search-results" id="search-results" hidden></div></div><a class="help-button" href="{{ route('guide') }}"><span>?</span><span>Panduan</span></a><div class="top-avatar">XI</div></div></header>
        <main class="page-content">
            @include('pages.'.$page)
        </main>
        <footer class="page-footer"><span>NETLEARN TKJ <i>•</i> E-Modul interaktif kelas XI</span><span>Belajar selangkah demi selangkah <b>↗</b></span></footer>
    </div>
</div>
<script>
window.NETLEARN = {
    page: @json($page),
    currentLesson: @json($page === 'lesson' ? $lesson['id'] : null),
    lessons: @json($lessons),
    quiz: @json($quiz),
    pairs: @json($pairs),
    glossary: @json($glossary),
    quizType: @json($quizType ?? null),
    routes: { lesson: @json(url('/materi')), activities: @json(route('activities')), evaluation: @json(url('/evaluasi')), results: @json(route('results')), api: @json(url('/api')) }
};
</script>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
