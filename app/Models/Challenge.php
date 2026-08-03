<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Challenge $challenge) {
            if (empty($challenge->order_index)) {
                $challenge->order_index = static::where('category', $challenge->category)
                    ->where('difficulty', $challenge->difficulty)
                    ->max('order_index') + 1;
            }
        });
    }

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'difficulty',
        'estimated_minutes',
        'order_index',
        'is_published',
        'initial_files_json',
        'file_language_map',
        'command_flows_json',
        'initial_state_json',
        'solution_files_json',
        'solution_explanation',
        'hints_json',
        'video_url',
        'video_file',
        'tags',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order_index' => 'integer',
        'estimated_minutes' => 'integer',
        'initial_files_json' => 'array',
        'file_language_map' => 'array',
        'command_flows_json' => 'array',
        'initial_state_json' => 'array',
        'solution_files_json' => 'array',
        'hints_json' => 'array',
        'tags' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function attempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index');
    }

    public function scopeDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Accessors ──────────────────────────────────────────────

    /**
     * Get initial files for the code editor.
     */
    public function getInitialFiles(): array
    {
        return $this->initial_files_json ?? [];
    }

    /**
     * Get the language map for syntax highlighting.
     */
    public function getFileLanguageMap(): array
    {
        return $this->file_language_map ?? [];
    }

    /**
     * Get command flows for the terminal simulator.
     */
    public function getCommandFlows(): array
    {
        return $this->command_flows_json ?? [];
    }

    /**
     * Get initial cluster state for the terminal simulator.
     */
    public function getInitialState(): array
    {
        return $this->initial_state_json ?? [];
    }

    /**
     * Get solution files (correct answers).
     */
    public function getSolutionFiles(): array
    {
        return $this->solution_files_json ?? [];
    }

    /**
     * Get progressive hints.
     */
    public function getHints(): array
    {
        return $this->hints_json ?? [];
    }

    /**
     * Check if this challenge requires a subscription (medium/hard).
     */
    public function requiresSubscription(): bool
    {
        return in_array($this->difficulty, ['medium', 'hard']);
    }

    /**
     * Check if this challenge has a tutorial video.
     */
    public function hasVideo(): bool
    {
        return !empty($this->video_url) || !empty($this->video_file);
    }

    /**
     * Convert any YouTube URL to embed format for iframe usage.
     */
    public function getEmbedVideoUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = $this->video_url;

        // Already an embed URL
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

        return $url;
    }

    /**
     * Get the video source (URL or uploaded file path).
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
     * Check if video is an uploaded file (not embed URL).
     */
    public function isUploadedVideo(): bool
    {
        return empty($this->video_url) && !empty($this->video_file);
    }

    /**
     * Get the difficulty color for badges.
     */
    public function getDifficultyColor(): string
    {
        return match ($this->difficulty) {
            'beginner' => '#22c55e', // green
            'medium' => '#f97316',   // orange
            'hard' => '#ef4444',     // red
            default => '#94a3b8',
        };
    }

    /**
     * Get the category icon class.
     */
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'kubernetes' => '☸️ Kubernetes',
            'terraform' => '🏗️ Terraform',
            'docker' => '🐳 Docker',
            default => ucfirst($this->category),
        };
    }
}
