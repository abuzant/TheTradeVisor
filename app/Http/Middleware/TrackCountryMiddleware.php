<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use App\Services\GeoIPService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackCountryMiddleware
{
    protected GeoIPService $geoIPService;

    public function __construct(GeoIPService $geoIPService)
    {
        $this->geoIPService = $geoIPService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track API requests
        if ($request->is('api/*') && Auth::check()) {
            $this->logRequest($request);
        }

        return $response;
    }

    /**
     * Log the API request with country information
     */
    protected function logRequest(Request $request): void
    {
        try {
            $ip = $request->ip();
            $countryData = $this->geoIPService->getCountryFromIP($ip);

            // Get trading account ID if present in request
            $tradingAccountId = $request->input('trading_account_id') 
                ?? $request->route('trading_account_id')
                ?? $request->route('account');

            ApiRequestLog::create([
                'trading_account_id' => $tradingAccountId,
                'user_id' => Auth::id(),
                'ip_address' => $ip,
                'country_code' => $countryData['country_code'] ?? null,
                'country_name' => $countryData['country_name'] ?? null,
                'endpoint' => $request->path(),
                'method' => $request->method(),
            ]);

            // Update trading account country if we have the data
            if ($tradingAccountId && $countryData) {
                $this->updateAccountCountry($tradingAccountId, $ip, $countryData);
            }
        } catch (\Exception $e) {
            // Silently fail - don't break the request
            \Log::debug('Failed to log country tracking', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update trading account with country information
     */
    protected function updateAccountCountry(int $accountId, string $ip, array $countryData): void
    {
        try {
            \App\Models\TradingAccount::where('id', $accountId)
                ->where('user_id', Auth::id())
                ->update([
                    'country_code' => $countryData['country_code'],
                    'country_name' => $countryData['country_name'],
                    'last_ip' => $ip,
                    'last_seen_at' => now(),
                ]);
        } catch (\Exception $e) {
            \Log::debug('Failed to update account country', ['error' => $e->getMessage()]);
        }
    }
}
