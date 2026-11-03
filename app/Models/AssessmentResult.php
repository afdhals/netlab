<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentResult extends Model
{
    protected $fillable = ['learner_id', 'quiz_type', 'score', 'total', 'answers', 'recommendations', 'submitted_at'];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'recommendations' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }
}
