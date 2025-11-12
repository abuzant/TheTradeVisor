<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitExports
{
    /**
     * Handle an incoming request.
     * Limit exports to prevent abuse and server overload.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return $next($request);
        }

        // Rate limit: 5 exports per minute per user
        $key = 'export_rate_limit:' . $user->id;
        $maxAttempts = 5;
        $decayMinutes = 1;

        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'Too many export requests. Please wait a moment before trying again.',
                'retry_after' => 60
            ], 429);
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
