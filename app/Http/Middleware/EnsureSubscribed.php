<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Redirect unsubscribed users to pricing page.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->subscribed()) {
            return redirect()->route('pricing');
        }

        return $next($request);
    }
}
