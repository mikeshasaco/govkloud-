<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if (empty($lesson->order_index)) {
                $lesson->order_index = static::where('module_id', $lesson->module_id)->max('order_index') + 1;
            }
        });
    }

    protected $fillable = [
        'module_id',
        'lab_id',
        'title',
        'subcategory',
        'video_url',
        'video_file',
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

    /**
     * Check if this lesson has video content
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url) || !empty($this->video_file);
    }

    /**
     * Get the video source (URL or uploaded file path)
     */
    public function getVideoSource(): ?string
    {
        if (!empty($this->video_url)) {
            return $this->video_url;
        }
        if (!empty($this->video_file)) {
            return asset('storage/' . $this->video_file);
        }
        return null;
    }

    /**
     * Check if video is uploaded file (not embed URL)
     */
    public function isUploadedVideo(): bool
    {
        return empty($this->video_url) && !empty($this->video_file);
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

