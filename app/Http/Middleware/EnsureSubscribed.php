<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Redirect unsubscribed users to pricing page.
     * Allows subscribed users, trial users, and grandfathered users.
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

        return redirect()->route('pricing');
    }
}
