<?php

use App\Http\Controllers\Api\AssessmentResultController;
use App\Http\Controllers\Api\LearnerStateController;
use Illuminate\Support\Facades\Route;

Route::get('/learner-state', [LearnerStateController::class, 'show']);
Route::put('/learner-state/progress', [LearnerStateController::class, 'updateProgress']);
Route::post('/assessment-results', [AssessmentResultController::class, 'store']);
