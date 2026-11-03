<?php

use App\Http\Controllers\LearningController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LearningController::class, 'home'])->name('home');
Route::get('/panduan', [LearningController::class, 'guide'])->name('guide');
Route::get('/capaian', [LearningController::class, 'outcomes'])->name('outcomes');
Route::get('/peta-konsep', [LearningController::class, 'concept'])->name('concept');
Route::get('/materi/{slug}', [LearningController::class, 'lesson'])->name('lesson');
Route::get('/aktivitas', [LearningController::class, 'activities'])->name('activities');
Route::get('/evaluasi/{type?}', [LearningController::class, 'evaluation'])->whereIn('type', ['pretest', 'posttest'])->name('evaluation');
Route::get('/hasil-belajar', [LearningController::class, 'results'])->name('results');
Route::get('/rangkuman', [LearningController::class, 'summary'])->name('summary');
Route::get('/glosarium', [LearningController::class, 'glossary'])->name('glossary');
Route::get('/tentang', [LearningController::class, 'about'])->name('about');
