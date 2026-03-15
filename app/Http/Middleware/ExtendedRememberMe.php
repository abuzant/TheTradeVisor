<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExtendedRememberMe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If user is authenticated and has remember token
        if (Auth::check() && Auth::viaRemember()) {
            // Update last activity timestamp
            Auth::user()->update([
                'last_activity_at' => now(),
            ]);

            // Refresh the remember token cookie for another 72 hours
            $cookie = cookie(
                Auth::guard()->getRecallerName(),
                Auth::user()->getRememberToken(),
                60 * 24 * 3, // 72 hours in minutes (3 days)
                '/',
                null,
                true, // secure
                true, // httpOnly
                false,
                'lax'
            );

            $response->withCookie($cookie);
        }

        return $response;
    }
}
