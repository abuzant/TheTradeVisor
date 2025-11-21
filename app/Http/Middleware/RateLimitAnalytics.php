<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\RateLimitSetting;

class RateLimitAnalytics
{
    /**
     * Default maximum analytics requests per user per minute (fallback)
     */
    private const DEFAULT_MAX_REQUESTS = 10;
    
    /**
     * Cache duration for analytics data (5 minutes)
     */
    private const CACHE_DURATION = 300;
    
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return $next($request);
        }
        
        // Get limit from database settings, fallback to default
        $maxRequests = RateLimitSetting::get('analytics_limit', self::DEFAULT_MAX_REQUESTS);
        
        $key = "analytics_rate_limit:{$userId}";
        $requests = Cache::get($key, 0);
        
        if ($requests >= $maxRequests) {
            Log::warning('Analytics rate limit exceeded', [
                'user_id' => $userId,
                'requests' => $requests,
                'limit' => $maxRequests,
                'url' => $request->fullUrl(),
            ]);
            
            return response()->json([
                'error' => 'Too many requests. Please wait a moment before refreshing analytics.',
                'retry_after' => 60,
            ], 429);
        }
        
        // Increment counter
        Cache::put($key, $requests + 1, now()->addMinute());
        
        return $next($request);
    }
}
