<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $fillable = [
        'module_id',
        'lab_id',
        'slug',
        'title',
        'video_url',
        'reading_md',
        'quiz_json',
        'order_index',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order_index' => 'integer',
        'quiz_json' => 'array',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    /**
     * Check if this lesson has an associated lab
     */
    public function hasLab(): bool
    {
        return $this->lab_id !== null;
    }

    /**
     * Check if this lesson has a quiz
     */
    public function hasQuiz(): bool
    {
        return !empty($this->quiz_json);
    }

    /**
     * Get quiz questions
     */
    public function getQuizQuestions(): array
    {
        return $this->quiz_json ?? [];
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }
}

