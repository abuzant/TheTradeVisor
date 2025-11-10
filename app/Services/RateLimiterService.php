<?php

namespace App\Services;

use App\Models\RateLimitSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RateLimiterService
{
    /**
     * Check if request should be rate limited
     *
     * @param string $identifier (IP or API key)
     * @param string $type ('ip' or 'api_key')
     * @param User|null $user
     * @return array ['allowed' => bool, 'remaining' => int, 'reset' => int, 'limit' => int]
     */
    public function check(string $identifier, string $type = 'ip', ?User $user = null): array
    {
        $limit = $this->getLimit($type, $user);
        $key = $this->getCacheKey($identifier, $type);
        
        // Get current count
        $attempts = Cache::get($key, 0);
        $remaining = max(0, $limit - $attempts);
        $resetTime = $this->getResetTime($key);
        
        // Check if limit exceeded
        $allowed = $attempts < $limit;
        
        if ($allowed) {
            // Increment counter
            $this->incrementAttempts($key);
        } else {
            // Log rate limit hit
            $this->logRateLimitHit($identifier, $type, $limit);
        }
        
        return [
            'allowed' => $allowed,
            'remaining' => $remaining,
            'reset' => $resetTime,
            'limit' => $limit,
            'attempts' => $attempts,
        ];
    }
    
    /**
     * Get the rate limit for a given type and user
     */
    protected function getLimit(string $type, ?User $user = null): int
    {
        // Check for user-specific limit first
        if ($user && $user->rate_limit) {
            return $user->rate_limit;
        }
        
        // Check for premium users
        if ($user && $user->is_premium) {
            return RateLimitSetting::get('premium_api_key_limit', 300);
        }
        
        // Get default limit based on type
        $key = $type === 'ip' ? 'global_ip_limit' : 'global_api_key_limit';
        
        // Check if this is a data collection request (more generous limit)
        $request = request();
        if ($request && $request->is('api/v1/data/collect')) {
            return RateLimitSetting::get('data_collection_api_key_limit', 100);
        }
        
        return RateLimitSetting::get($key, $type === 'ip' ? 60 : 120);
    }
    
    /**
     * Increment attempt counter
     */
    protected function incrementAttempts(string $key): void
    {
        $attempts = Cache::get($key, 0);
        
        if ($attempts === 0) {
            // First attempt, set with 60 second TTL
            Cache::put($key, 1, 60);
        } else {
            // Increment existing counter
            Cache::increment($key);
        }
    }
    
    /**
     * Get cache key for rate limiting
     */
    protected function getCacheKey(string $identifier, string $type): string
    {
        return "rate_limit:{$type}:{$identifier}";
    }
    
    /**
     * Get reset time (Unix timestamp)
     */
    protected function getResetTime(string $key): int
    {
        // Get TTL from cache
        $ttl = Cache::getStore()->getRedis()->ttl($key);
        
        if ($ttl > 0) {
            return time() + $ttl;
        }
        
        return time() + 60; // Default 60 seconds
    }
    
    /**
     * Log rate limit hit
     */
    protected function logRateLimitHit(string $identifier, string $type, int $limit): void
    {
        Log::warning('Rate limit exceeded', [
            'identifier' => $identifier,
            'type' => $type,
            'limit' => $limit,
            'timestamp' => now(),
        ]);
    }
    
    /**
     * Clear rate limit for an identifier
     */
    public function clear(string $identifier, string $type = 'ip'): void
    {
        $key = $this->getCacheKey($identifier, $type);
        Cache::forget($key);
    }
    
    /**
     * Get current attempts for an identifier
     */
    public function getAttempts(string $identifier, string $type = 'ip'): int
    {
        $key = $this->getCacheKey($identifier, $type);
        return Cache::get($key, 0);
    }
    
    /**
     * Check if identifier is whitelisted
     */
    public function isWhitelisted(string $identifier, string $type = 'ip'): bool
    {
        $whitelistKey = "rate_limit_whitelist_{$type}";
        $whitelist = RateLimitSetting::where('key', $whitelistKey)->first();
        
        if (!$whitelist) {
            return false;
        }
        
        $whitelistedItems = explode(',', $whitelist->description ?? '');
        return in_array($identifier, array_map('trim', $whitelistedItems));
    }
}
