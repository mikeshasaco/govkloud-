<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, Billable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_admin',
        'google_id',
        'avatar',
        'k8s_namespace',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Check if user can access Filament admin panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Get the user's lab sessions
     */
    public function labSessions(): HasMany
    {
        return $this->hasMany(LabSession::class);
    }

    /**
     * Get the user's saved/bookmarked modules
     */
    public function savedModules()
    {
        return $this->belongsToMany(Module::class, 'user_saved_modules')
            ->withTimestamps();
    }

    /**
     * Check if user has saved a module
     */
    public function hasSavedModule(Module $module): bool
    {
        return $this->savedModules()->where('module_id', $module->id)->exists();
    }

    /**
     * Toggle save/unsave a module
     */
    public function toggleSaveModule(Module $module): bool
    {
        if ($this->hasSavedModule($module)) {
            $this->savedModules()->detach($module->id);
            return false;
        } else {
            $this->savedModules()->attach($module->id);
            return true;
        }
    }

    /**
     * Get all lesson progress records
     */
    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get progress for a specific lesson
     */
    public function getLessonProgress(Lesson $lesson): ?LessonProgress
    {
        return $this->lessonProgress()->where('lesson_id', $lesson->id)->first();
    }

    /**
     * Check if user has completed a lesson
     */
    public function hasCompletedLesson(Lesson $lesson): bool
    {
        $progress = $this->getLessonProgress($lesson);
        return $progress && $progress->completed;
    }

    /**
     * Get or create progress record for a lesson
     */
    public function getOrCreateLessonProgress(Lesson $lesson): LessonProgress
    {
        return LessonProgress::firstOrCreate(
            ['user_id' => $this->id, 'lesson_id' => $lesson->id],
            ['completed' => false, 'quiz_attempts' => 0]
        );
    }

    /**
     * Mark a lesson as completed
     */
    public function completeLesson(Lesson $lesson, int $quizScore = null): LessonProgress
    {
        $progress = $this->getOrCreateLessonProgress($lesson);
        $progress->markComplete($quizScore);
        return $progress;
    }

    /**
     * Get completion percentage for a module
     */
    public function getModuleProgress(Module $module): array
    {
        $lessons = $module->lessons;
        $totalLessons = $lessons->count();

        if ($totalLessons === 0) {
            return ['completed' => 0, 'total' => 0, 'percentage' => 0];
        }

        $completedCount = 0;
        foreach ($lessons as $lesson) {
            if ($this->hasCompletedLesson($lesson)) {
                $completedCount++;
            }
        }

        return [
            'completed' => $completedCount,
            'total' => $totalLessons,
            'percentage' => round(($completedCount / $totalLessons) * 100)
        ];
    }

    /**
     * Check if user has an active subscription
     */
    public function isSubscribed(): bool
    {
        return $this->subscribed(config('stripe-plans.subscription_name'));
    }

    /**
     * Check if user is on Pro plan
     */
    public function isPro(): bool
    {
        $subscription = $this->subscription(config('stripe-plans.subscription_name'));
        if (!$subscription) {
            return false;
        }

        $proPrices = [
            config('stripe-plans.plans.pro.monthly.price_id'),
            config('stripe-plans.plans.pro.yearly.price_id'),
        ];

        return $subscription->items->contains(function ($item) use ($proPrices) {
            return in_array($item->stripe_price, $proPrices);
        });
    }

    /**
     * Check if user is on Standard plan
     */
    public function isStandard(): bool
    {
        return $this->isSubscribed() && !$this->isPro();
    }

    /**
     * Get the user's current plan name
     */
    public function getPlanName(): ?string
    {
        if (!$this->isSubscribed()) {
            return null;
        }
        return $this->isPro() ? 'Pro' : 'Standard';
    }

    /**
     * Check if user has access to labs (subscribed or on trial)
     */
    public function hasLabAccess(): bool
    {
        return $this->isSubscribed() || $this->onTrial();
    }

    // ── Challenge Methods ──────────────────────────────────────

    /**
     * Get the user's challenge attempts
     */
    public function challengeAttempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class);
    }

    /**
     * Check if user has completed a challenge
     */
    public function hasCompletedChallenge(Challenge $challenge): bool
    {
        return $this->challengeAttempts()
            ->where('challenge_id', $challenge->id)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get or create an attempt for a challenge
     */
    public function getOrCreateChallengeAttempt(Challenge $challenge): ChallengeAttempt
    {
        $attempt = $this->challengeAttempts()
            ->where('challenge_id', $challenge->id)
            ->whereIn('status', ['started', 'completed'])
            ->first();

        if ($attempt) {
            return $attempt;
        }

        return ChallengeAttempt::create([
            'user_id' => $this->id,
            'challenge_id' => $challenge->id,
            'status' => 'started',
            'hints_used' => 0,
        ]);
    }

    /**
     * Get challenge statistics for this user
     */
    public function getChallengeStats(): array
    {
        $attempts = $this->challengeAttempts()->with('challenge')->get();

        $completed = $attempts->where('status', 'completed');

        return [
            'total_started' => $attempts->count(),
            'total_completed' => $completed->count(),
            'by_difficulty' => [
                'beginner' => $completed->filter(fn($a) => $a->challenge->difficulty === 'beginner')->count(),
                'medium' => $completed->filter(fn($a) => $a->challenge->difficulty === 'medium')->count(),
                'hard' => $completed->filter(fn($a) => $a->challenge->difficulty === 'hard')->count(),
            ],
            'by_category' => [
                'kubernetes' => $completed->filter(fn($a) => $a->challenge->category === 'kubernetes')->count(),
                'terraform' => $completed->filter(fn($a) => $a->challenge->category === 'terraform')->count(),
                'docker' => $completed->filter(fn($a) => $a->challenge->category === 'docker')->count(),
                'linux' => $completed->filter(fn($a) => $a->challenge->category === 'linux')->count(),
            ],
        ];
    }
}
