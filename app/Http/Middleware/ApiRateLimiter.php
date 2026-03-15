<?php

namespace App\Http\Middleware;

use App\Services\RateLimiterService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    protected RateLimiterService $rateLimiter;

    public function __construct(RateLimiterService $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $user = $request->get('authenticated_user') ?? $request->user();
        $apiKey = $request->bearerToken() ?? $request->input('api_key');

        // Check IP-based rate limit first
        if (!$this->rateLimiter->isWhitelisted($ip, 'ip')) {
            $ipCheck = $this->rateLimiter->check($ip, 'ip', $user);
            
            if (!$ipCheck['allowed']) {
                return $this->buildRateLimitResponse($ipCheck, 'IP');
            }
        }

        // Check API key-based rate limit if authenticated
        if ($apiKey && $user) {
            if (!$this->rateLimiter->isWhitelisted($apiKey, 'api_key')) {
                $apiKeyCheck = $this->rateLimiter->check($apiKey, 'api_key', $user);
                
                if (!$apiKeyCheck['allowed']) {
                    return $this->buildRateLimitResponse($apiKeyCheck, 'API Key');
                }
                
                // Use API key limits for headers (more generous)
                $response = $next($request);
                return $this->addRateLimitHeaders($response, $apiKeyCheck);
            }
        }

        // Continue with IP limits for headers
        $response = $next($request);
        return $this->addRateLimitHeaders($response, $ipCheck ?? ['limit' => 0, 'remaining' => 0, 'reset' => time()]);
    }

    /**
     * Build rate limit exceeded response
     */
    protected function buildRateLimitResponse(array $limitInfo, string $limitType): Response
    {
        $retryAfter = $limitInfo['reset'] - time();
        
        return response()->json([
            'success' => false,
            'error' => 'Rate limit exceeded',
            'message' => "Too many requests. Please try again in {$retryAfter} seconds.",
            'limit_type' => $limitType,
            'limit' => $limitInfo['limit'],
            'retry_after' => $retryAfter,
        ], 429)
        ->header('X-RateLimit-Limit', $limitInfo['limit'])
        ->header('X-RateLimit-Remaining', 0)
        ->header('X-RateLimit-Reset', $limitInfo['reset'])
        ->header('Retry-After', $retryAfter);
    }

    /**
     * Add rate limit headers to response
     */
    protected function addRateLimitHeaders(Response $response, array $limitInfo): Response
    {
        return $response
            ->header('X-RateLimit-Limit', $limitInfo['limit'])
            ->header('X-RateLimit-Remaining', $limitInfo['remaining'])
            ->header('X-RateLimit-Reset', $limitInfo['reset']);
    }
}
