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
        'has_lab',
        'workbench_image',
        'ttl_minutes',
        'estimated_minutes',
        'lab_config_json',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'has_lab' => 'boolean',
        'order_index' => 'integer',
        'ttl_minutes' => 'integer',
        'estimated_minutes' => 'integer',
        'quiz_json' => 'array',
        'lab_config_json' => 'array',
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
     * Check if this lesson has an associated lab environment
     */
    public function hasLab(): bool
    {
        // New inline lab config takes priority, fall back to legacy lab_id
        return $this->has_lab || $this->lab_id !== null;
    }

    /**
     * Get resource limits from lab_config_json or defaults
     */
    public function getResourceLimits(): array
    {
        $config = $this->lab_config_json ?? [];

        return [
            'cpu' => $config['resources']['cpu'] ?? config('govkloud.resources.default_cpu_limit'),
            'memory' => $config['resources']['memory'] ?? config('govkloud.resources.default_memory_limit'),
            'storage' => $config['resources']['storage'] ?? config('govkloud.resources.default_storage_limit'),
        ];
    }

    /**
     * Get the workbench image, falling back to legacy lab or default
     */
    public function getWorkbenchImage(): string
    {
        if (!empty($this->workbench_image)) {
            return $this->workbench_image;
        }

        // Fall back to legacy linked lab
        if ($this->lab) {
            return $this->lab->workbench_image ?? 'govkloudacr.azurecr.io/code-server-k8s:latest';
        }

        return 'govkloudacr.azurecr.io/code-server-k8s:latest';
    }

    /**
     * Get TTL minutes, falling back to legacy lab or config default
     */
    public function getTtlMinutes(): int
    {
        return $this->ttl_minutes
            ?? $this->lab?->ttl_minutes
            ?? config('govkloud.session.ttl_default_minutes', 180);
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
            return \Storage::disk('azure')->url($this->video_file);
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

