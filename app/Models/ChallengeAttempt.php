<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'challenge_id',
        'status',
        'user_files_json',
        'commands_executed',
        'time_spent_seconds',
        'hints_used',
        'completed_at',
    ];

    protected $casts = [
        'user_files_json' => 'array',
        'commands_executed' => 'array',
        'time_spent_seconds' => 'integer',
        'hints_used' => 'integer',
        'completed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    // ── Methods ────────────────────────────────────────────────

    /**
     * Mark this attempt as completed.
     */
    public function markCompleted(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Mark this attempt as skipped.
     */
    public function markSkipped(): void
    {
        $this->status = 'skipped';
        $this->save();
    }

    /**
     * Save user's progress (files and commands).
     */
    public function saveProgress(array $files = null, array $commands = null, int $timeSpent = null): void
    {
        if ($files !== null) {
            $this->user_files_json = $files;
        }
        if ($commands !== null) {
            $this->commands_executed = $commands;
        }
        if ($timeSpent !== null) {
            $this->time_spent_seconds = $timeSpent;
        }
        $this->save();
    }

    /**
     * Increment hints used counter.
     */
    public function revealHint(): int
    {
        $this->hints_used = ($this->hints_used ?? 0) + 1;
        $this->save();
        return $this->hints_used;
    }
}
