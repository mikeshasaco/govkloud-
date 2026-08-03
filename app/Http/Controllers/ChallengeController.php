<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChallengeController extends Controller
{
    /**
     * Display the problems hub — challenge listing with filters.
     * GET /problems
     */
    public function index(Request $request)
    {
        $query = Challenge::published()->ordered();

        // Category filter
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Difficulty filter
        if ($request->filled('difficulty')) {
            $query->difficulty($request->difficulty);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search);
            });
        }

        $challenges = $query->get();

        // Get user stats if authenticated
        $stats = null;
        $completedIds = [];
        if (Auth::check()) {
            $stats = Auth::user()->getChallengeStats();
            $completedIds = Auth::user()->challengeAttempts()
                ->where('status', 'completed')
                ->pluck('challenge_id')
                ->toArray();
        }

        // Get counts for the category tabs
        $categoryCounts = [
            'all' => Challenge::published()->count(),
            'kubernetes' => Challenge::published()->category('kubernetes')->count(),
            'terraform' => Challenge::published()->category('terraform')->count(),
            'docker' => Challenge::published()->category('docker')->count(),
        ];

        return view('problems.index', compact(
            'challenges',
            'stats',
            'completedIds',
            'categoryCounts'
        ));
    }

    /**
     * Display the challenge workspace.
     * GET /problems/{slug}
     */
    public function show(string $slug)
    {
        $challenge = Challenge::where('slug', $slug)
            ->published()
            ->firstOrFail();

        $user = Auth::user();

        // Check subscription for medium/hard challenges
        if ($challenge->requiresSubscription()) {
            if (!$user->isSubscribed() && !$user->onTrial()) {
                return redirect()->route('pricing')
                    ->with('message', 'Medium and Hard problems require a subscription.');
            }
        }

        // Get or create attempt
        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        // Get navigation context (prev/next challenges in same category+difficulty)
        $siblings = Challenge::published()
            ->ordered()
            ->where('category', $challenge->category)
            ->where('difficulty', $challenge->difficulty)
            ->get();

        $currentIndex = $siblings->search(fn($c) => $c->id === $challenge->id);
        $prevChallenge = $currentIndex > 0 ? $siblings[$currentIndex - 1] : null;
        $nextChallenge = $currentIndex < $siblings->count() - 1 ? $siblings[$currentIndex + 1] : null;

        return view('problems.show', compact(
            'challenge',
            'attempt',
            'prevChallenge',
            'nextChallenge'
        ));
    }

    /**
     * Save user progress (auto-save files and commands).
     * POST /problems/{slug}/save
     */
    public function saveProgress(Request $request, string $slug)
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $attempt = Auth::user()->getOrCreateChallengeAttempt($challenge);

        $attempt->saveProgress(
            files: $request->input('files'),
            commands: $request->input('commands'),
            timeSpent: $request->input('time_spent_seconds')
        );

        return response()->json(['saved' => true]);
    }

    /**
     * Mark challenge as completed.
     * POST /problems/{slug}/complete
     */
    public function complete(Request $request, string $slug)
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $attempt = Auth::user()->getOrCreateChallengeAttempt($challenge);

        // Save final state
        $attempt->saveProgress(
            files: $request->input('files'),
            commands: $request->input('commands'),
            timeSpent: $request->input('time_spent_seconds')
        );

        $attempt->markCompleted();

        return response()->json([
            'completed' => true,
            'completed_at' => $attempt->completed_at->toISOString(),
            'solution_files' => $challenge->getSolutionFiles(),
            'solution_explanation' => $challenge->solution_explanation,
        ]);
    }

    /**
     * Reveal next hint.
     * POST /problems/{slug}/hint
     */
    public function hint(string $slug)
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $attempt = Auth::user()->getOrCreateChallengeAttempt($challenge);

        $hints = $challenge->getHints();
        $hintsUsed = $attempt->revealHint();

        // Return the newly revealed hint (1-indexed)
        $hintIndex = $hintsUsed - 1;
        $hint = $hints[$hintIndex] ?? null;

        return response()->json([
            'hint' => $hint,
            'hints_used' => $hintsUsed,
            'total_hints' => count($hints),
        ]);
    }
}
