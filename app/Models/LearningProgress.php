<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningProgress extends Model
{
    protected $fillable = ['learner_id', 'completed_lessons', 'completed_activities', 'current_lesson'];

    protected function casts(): array
    {
        return [
            'completed_lessons' => 'array',
            'completed_activities' => 'array',
        ];
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }
}
