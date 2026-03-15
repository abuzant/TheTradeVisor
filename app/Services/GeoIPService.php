<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class GeoIPService
{
    protected ?Reader $reader = null;
    protected string $databasePath;

    public function __construct()
    {
        $this->databasePath = storage_path('app/geoip/GeoLite2-Country.mmdb');
    }

    /**
     * Get country information from IP address
     */
    public function getCountryFromIP(string $ip): ?array
    {
        // Skip local/private IPs
        if ($this->isPrivateIP($ip)) {
            return null;
        }

        // Check cache first (24 hour TTL)
        $cacheKey = 'geoip:' . $ip;
        
        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                $reader = $this->getReader();
                
                if (!$reader) {
                    return null;
                }

                $record = $reader->country($ip);
                
                return [
                    'country_code' => $record->country->isoCode,
                    'country_name' => $record->country->name,
                ];
            } catch (Exception $e) {
                Log::warning('GeoIP lookup failed', [
                    'ip' => $ip,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    /**
     * Get or initialize the GeoIP reader
     */
    protected function getReader(): ?Reader
    {
        if ($this->reader !== null) {
            return $this->reader;
        }

        if (!file_exists($this->databasePath)) {
            Log::warning('GeoIP database not found', ['path' => $this->databasePath]);
            return null;
        }

        try {
            $this->reader = new Reader($this->databasePath);
            return $this->reader;
        } catch (Exception $e) {
            Log::error('Failed to initialize GeoIP reader', ['error' => $e->getMessage()]);
            return null;
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

    /**
     * Check if GeoIP database exists
     */
    public function isDatabaseAvailable(): bool
    {
        return file_exists($this->databasePath);
    }

    /**
     * Get database path
     */
    public function getDatabasePath(): string
    {
        return $this->databasePath;
    }
}
