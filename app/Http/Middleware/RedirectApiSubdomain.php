<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect non-API traffic from api.thetradevisor.com to main site
 * 
 * This middleware prevents SEO duplicate content issues by redirecting
 * browser/bot traffic to the main site while allowing legitimate EA requests.
 */
class RedirectApiSubdomain
{
    /**
     * List of paths that are legitimate API endpoints
     */
    private const API_PATHS = [
        '/api/v1/data/collect',
        '/api/v1/accounts',
        '/api/v1/trades',
        '/api/v1/analytics',
        '/api/health',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to api.thetradevisor.com subdomain
        if (!$this->isApiSubdomain($request)) {
            return $next($request);
        }

        // Allow health check endpoint (for monitoring)
        if ($request->is('api/health')) {
            return $next($request);
        }

        // Check if this is a legitimate API request
        if ($this->isLegitimateApiRequest($request)) {
            return $next($request);
        }

        // Redirect all other traffic to main site
        $mainSiteUrl = 'https://thetradevisor.com' . $request->getRequestUri();
        
        \Log::info('Redirecting non-API traffic from api subdomain', [
            'from' => $request->fullUrl(),
            'to' => $mainSiteUrl,
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ]);

        return redirect($mainSiteUrl, 301);
    }

    /**
     * Check if request is to api.thetradevisor.com subdomain
     */
    private function isApiSubdomain(Request $request): bool
    {
        $host = $request->getHost();
        return $host === 'api.thetradevisor.com';
    }

    /**
     * Determine if this is a legitimate API request from EA
     */
    private function isLegitimateApiRequest(Request $request): bool
    {
        // Must be a POST request to an API endpoint
        if (!$request->isMethod('POST')) {
            return false;
        }

        // Must be to a valid API path
        $isApiPath = false;
        foreach (self::API_PATHS as $path) {
            if ($request->is(ltrim($path, '/'))) {
                $isApiPath = true;
                break;
            }
        }

        if (!$isApiPath) {
            return false;
        }

        // Must have Authorization header (API key)
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return false;
        }

        // Must accept JSON (EA sends this)
        $acceptHeader = $request->header('Accept', '');
        if (stripos($acceptHeader, 'application/json') === false && 
            stripos($acceptHeader, '*/*') === false) {
            return false;
        }

        // Check for typical browser user agents (redirect these)
        $userAgent = $request->userAgent() ?? '';
        $browserPatterns = [
            'Mozilla/',
            'Chrome/',
            'Safari/',
            'Edge/',
            'Opera/',
            'Firefox/',
            'MSIE',
            'Trident/',
        ];

        foreach ($browserPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                // This looks like a browser, not an EA
                return false;
            }
        }

        // Looks like a legitimate API request
        return true;
    }
}
