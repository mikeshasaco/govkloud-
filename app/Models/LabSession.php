<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabSession extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'module_id',
        'lab_id',
        'status',
        'host_namespace',
        'vcluster_release_name',
        'workbench_release_name',
        'session_token',
        'code_url',
        'expires_at',
        'last_activity_at',
        'error_message',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PROVISIONING = 'provisioning';
    const STATUS_RUNNING = 'running';
    const STATUS_VALIDATING = 'validating';
    const STATUS_PASSED = 'passed';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_DESTROYED = 'destroyed';
    const STATUS_ERROR = 'error';

    /**
     * Active statuses that count against concurrency limit
     */
    const ACTIVE_STATUSES = [
        self::STATUS_PROVISIONING,
        self::STATUS_RUNNING,
        self::STATUS_VALIDATING,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    /**
     * Check if session is in an active state
     */
    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    /**
     * Check if session is running and ready
     */
    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * Check if session has expired based on TTL
     */
    public function isExpiredByTtl(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if session has expired due to idle timeout
     */
    public function isExpiredByIdle(): bool
    {
        if (!$this->last_activity_at) {
            return false;
        }

        $idleTimeout = config('govkloud.session.idle_timeout_minutes');
        return $this->last_activity_at->addMinutes($idleTimeout)->isPast();
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', self::ACTIVE_STATUSES);
    }

    /**
     * Scope for expired sessions that need cleanup
     */
    public function scopeNeedsCleanup($query)
    {
        $idleTimeout = config('govkloud.session.idle_timeout_minutes');

        return $query->whereIn('status', self::ACTIVE_STATUSES)
            ->where(function ($q) use ($idleTimeout) {
                // TTL expired
                $q->where('expires_at', '<', now())
                    // OR idle timeout expired
                    ->orWhere(function ($q2) use ($idleTimeout) {
                    $q2->where('status', self::STATUS_RUNNING)
                        ->whereNotNull('last_activity_at')
                        ->where('last_activity_at', '<', now()->subMinutes($idleTimeout));
                });
            });
    }

    /**
     * Update last activity timestamp
     */
    public function recordActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Mark session as error with message
     */
    public function markError(string $message): void
    {
        $this->update([
            'status' => self::STATUS_ERROR,
            'error_message' => $message,
        ]);
    }
}
