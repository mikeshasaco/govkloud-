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
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow if subscribed or on trial
        if ($user->hasLabAccess()) {
            return $next($request);
        }

        // Redirect to pricing page with a message
        return redirect()->route('pricing')
            ->with('message', 'A subscription is required to access the labs. Start your free trial today!');
    }
}
