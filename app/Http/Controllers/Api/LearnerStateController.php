<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Support\LearningContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LearnerStateController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $validated = $request->validate(['client_id' => ['required', 'uuid']]);
        $learner = Learner::firstOrCreate(['client_id' => $validated['client_id']]);
        $progress = $learner->progress;

        $results = $learner->assessmentResults()
            ->orderByDesc('submitted_at')
            ->orderByDesc('id')
            ->get()
            ->unique('quiz_type')
            ->mapWithKeys(fn ($result): array => [$result->quiz_type => [
                'score' => $result->score,
                'total' => $result->total,
                'answers' => $result->answers,
                'recommendations' => $result->recommendations ?? [],
                'date' => $result->submitted_at->locale('id')->translatedFormat('j M Y'),
                'submitted_at' => $result->submitted_at->toIso8601String(),
            ]]);

        return response()->json([
            'progress' => [
                'completed' => $progress?->completed_lessons ?? [],
                'activityDone' => $progress?->completed_activities ?? [],
                'currentLesson' => $progress?->current_lesson,
            ],
            'results' => $results,
        ]);
    }

    public function updateProgress(Request $request): JsonResponse
    {
        $lessonIds = array_column(LearningContent::lessons(), 'id');
        $validated = $request->validate([
            'client_id' => ['required', 'uuid'],
            'completed_lessons' => ['required', 'array'],
            'completed_lessons.*' => ['string', Rule::in($lessonIds)],
            'completed_activities' => ['required', 'array'],
            'completed_activities.*' => ['string', Rule::in(['identify', 'match', 'binary', 'ip'])],
            'current_lesson' => ['nullable', 'string', Rule::in($lessonIds)],
        ]);

        $learner = Learner::firstOrCreate(['client_id' => $validated['client_id']]);
        $progress = $learner->progress()->updateOrCreate(
            ['learner_id' => $learner->id],
            [
                'completed_lessons' => array_values(array_unique($validated['completed_lessons'])),
                'completed_activities' => array_values(array_unique($validated['completed_activities'])),
                'current_lesson' => $validated['current_lesson'] ?? null,
            ],
        );

        return response()->json([
            'saved' => true,
            'updated_at' => $progress->updated_at?->toIso8601String(),
        ]);
    }
}
