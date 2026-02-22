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
     * Convert any YouTube URL to embed format for iframe usage.
     * Supports: youtube.com/watch?v=ID, youtu.be/ID, youtube.com/embed/ID
     */
    public function getEmbedVideoUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = $this->video_url;

        // Already an embed URL — return as-is
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        // Extract video ID from youtube.com/watch?v=ID or youtu.be/ID
        $videoId = null;

        if (preg_match('/[?&]v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}";
        }

        // Not a recognized YouTube URL — return as-is (might be Vimeo, etc.)
        return $url;
    }

    /**
     * Get the video source (URL or uploaded file path)
     */
    public function getVideoSource(): ?string
    {
        if (!empty($this->video_url)) {
            return $this->embed_video_url;
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

