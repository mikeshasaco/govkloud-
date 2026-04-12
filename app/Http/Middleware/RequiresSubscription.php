<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequiresSubscription
{
    /**
     * Handle an incoming request.
     * Redirect to pricing page if user is not subscribed or on trial.
     * Existing users created before the mandatory subscription cutoff are grandfathered in.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Grandfather existing users (created before mandatory subscription was enforced)
        $cutoffDate = '2026-04-12';
        if ($user->created_at < $cutoffDate) {
            return $next($request);
        }

        // Allow if subscribed or on trial
        if ($user->subscribed() || $user->onTrial()) {
            return $next($request);
        }

        // Redirect to pricing page with a message
        return redirect()->route('pricing')
            ->with('message', 'Choose a plan to get started — 3-day free trial included!');
    }
}
