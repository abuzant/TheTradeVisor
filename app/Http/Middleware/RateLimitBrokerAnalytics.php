<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitBrokerAnalytics
{
    /**
     * Handle an incoming request.
     * Limit broker analytics to prevent abuse.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return $next($request);
        }

        // Rate limit: 20 requests per minute per user
        $key = 'broker_analytics_rate_limit:' . $user->id;
        $maxAttempts = 20;
        $decayMinutes = 1;

        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'Too many broker analytics requests. Please wait a moment.',
                'retry_after' => 60
            ], 429);
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
