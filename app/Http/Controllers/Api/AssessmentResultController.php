<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Support\LearningContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssessmentResultController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'uuid'],
            'quiz_type' => ['required', Rule::in(['pretest', 'posttest'])],
            'answers' => ['required', 'array', 'list', 'size:8'],
            'answers.*' => ['required', 'integer', 'between:0,3'],
        ]);

        $questions = LearningContent::quiz();
        $score = 0;
        $recommendations = [];
        foreach ($questions as $index => $question) {
            if ((int) $validated['answers'][$index] === $question['answer']) {
                $score++;
            } else {
                $recommendations[] = $question['lesson'];
            }
        }

        $learner = Learner::firstOrCreate(['client_id' => $validated['client_id']]);
        $result = $learner->assessmentResults()->create([
            'quiz_type' => $validated['quiz_type'],
            'score' => $score,
            'total' => count($questions),
            'answers' => array_values($validated['answers']),
            'recommendations' => array_values(array_unique($recommendations)),
            'submitted_at' => now(),
        ]);

        return response()->json([
            'saved' => true,
            'result' => [
                'score' => $result->score,
                'total' => $result->total,
                'answers' => $result->answers,
                'recommendations' => $result->recommendations,
                'date' => $result->submitted_at->locale('id')->translatedFormat('j M Y'),
                'submitted_at' => $result->submitted_at->toIso8601String(),
            ],
        ], 201);
    }
}
