<?php

namespace App\Http\Controllers;

use App\Support\LearningContent;
use Illuminate\View\View;

class LearningController extends Controller
{
    private array $titles = [
        'home' => 'Beranda', 'guide' => 'Petunjuk penggunaan', 'outcomes' => 'Capaian belajar',
        'concept' => 'Peta konsep', 'lesson' => 'Materi pembelajaran', 'activities' => 'Aktivitas interaktif',
        'evaluation' => 'Evaluasi pembelajaran', 'results' => 'Hasil belajar', 'summary' => 'Rangkuman',
        'glossary' => 'Glosarium', 'about' => 'Tentang pengembang',
    ];

    public function home(): View { return $this->render('home'); }
    public function guide(): View { return $this->render('guide'); }
    public function outcomes(): View { return $this->render('outcomes'); }
    public function concept(): View { return $this->render('concept'); }
    public function activities(): View { return $this->render('activities'); }
    public function results(): View { return $this->render('results'); }
    public function summary(): View { return $this->render('summary'); }
    public function glossary(): View { return $this->render('glossary'); }
    public function about(): View { return $this->render('about'); }

    public function lesson(string $slug): View
    {
        $lesson = collect(LearningContent::lessons())->firstWhere('id', $slug);
        abort_if($lesson === null, 404);

        return $this->render('lesson', ['lesson' => $lesson]);
    }

    public function evaluation(?string $type = null): View
    {
        abort_if($type !== null && ! in_array($type, ['pretest', 'posttest'], true), 404);

        return $this->render('evaluation', ['quizType' => $type]);
    }

    private function render(string $page, array $extra = []): View
    {
        return view('learning', array_merge([
            'page' => $page,
            'pageTitle' => $this->titles[$page] ?? 'NETLEARN TKJ',
            'lessons' => LearningContent::lessons(),
            'quiz' => LearningContent::quiz(),
            'pairs' => LearningContent::pairs(),
            'glossary' => LearningContent::glossary(),
        ], $extra));
    }
}
