<?php

namespace App\Http\Middleware;

use App\Services\GeoIPService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackWebCountryMiddleware
{
    protected GeoIPService $geoIPService;

    public function __construct(GeoIPService $geoIPService)
    {
        $this->geoIPService = $geoIPService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track authenticated users on web requests
        if (Auth::check() && !$request->is('api/*')) {
            $this->updateUserCountry($request);
        }

        return $response;
    }

    /**
     * Update user's trading account with country information
     */
    protected function updateUserCountry(Request $request): void
    {
        try {
            $user = Auth::user();
            $ip = $request->ip();
            
            // Skip if IP is private
            if ($this->isPrivateIP($ip)) {
                return;
            }

            // Get country data
            $countryData = $this->geoIPService->getCountryFromIP($ip);
            
            if ($countryData) {
                // Update all user's trading accounts that don't have country data
                $user->tradingAccounts()
                    ->where(function($query) {
                        $query->whereNull('country_code')
                              ->orWhereNull('country_name');
                    })
                    ->update([
                        'country_code' => $countryData['country_code'],
                        'country_name' => $countryData['country_name'],
                        'last_ip' => $ip,
                        'last_seen_at' => now(),
                    ]);
            }
        } catch (\Exception $e) {
            // Silently fail - don't break the request
            \Log::debug('Failed to track web country', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Check if IP is private/local
     */
    protected function isPrivateIP(string $ip): bool
    {
        // Local IPs
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }

        // Private IP ranges
        $privateRanges = [
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
            '169.254.0.0/16',
            'fc00::/7',
        ];

        foreach ($privateRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    protected function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($subnet, $mask) = explode('/', $range);
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return $this->ipv6InRange($ip, $subnet, (int)$mask);
        }

        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - (int)$mask);
        
        return ($ip & $mask) === ($subnet & $mask);
    }

    /**
     * Check if IPv6 is in range
     */
    protected function ipv6InRange(string $ip, string $subnet, int $mask): bool
    {
        $ip = inet_pton($ip);
        $subnet = inet_pton($subnet);
        
        $binaryIp = $this->inet6ToBits($ip);
        $binarySubnet = $this->inet6ToBits($subnet);
        
        return substr($binaryIp, 0, $mask) === substr($binarySubnet, 0, $mask);
    }

    /**
     * Convert IPv6 to binary string
     */
    protected function inet6ToBits(string $inet): string
    {
        $unpacked = unpack('A16', $inet);
        $unpacked = str_split($unpacked[1]);
        $binary = '';
        
        foreach ($unpacked as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        
        return $binary;
    }
}
