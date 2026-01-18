<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed',
        'completed_at',
        'quiz_score',
        'quiz_attempts',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'quiz_score' => 'integer',
        'quiz_attempts' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Mark this lesson as completed
     */
    public function markComplete(int $quizScore = null): void
    {
        $this->completed = true;
        $this->completed_at = now();
        if ($quizScore !== null) {
            $this->quiz_score = $quizScore;
        }
        $this->save();
    }
}
