<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreakerService
{
    private const CIRCUIT_KEY = 'circuit_breaker_state';
    private const METRICS_KEY = 'circuit_breaker_metrics';
    
    /**
     * Check if circuit breaker is open (system under stress)
     */
    public function isOpen(): bool
    {
        if (!config('database_limits.circuit_breaker.enabled')) {
            return false;
        }
        
        return Cache::get(self::CIRCUIT_KEY, false);
    }
    
    /**
     * Open the circuit breaker (disable expensive operations)
     */
    public function open(string $reason): void
    {
        Cache::put(self::CIRCUIT_KEY, true, now()->addSeconds(
            config('database_limits.circuit_breaker.recovery_time', 300)
        ));
        
        Log::critical('Circuit breaker opened', [
            'reason' => $reason,
            'recovery_time' => config('database_limits.circuit_breaker.recovery_time'),
        ]);
    }
    
    /**
     * Close the circuit breaker (resume normal operations)
     */
    public function close(): void
    {
        Cache::forget(self::CIRCUIT_KEY);
        
        Log::info('Circuit breaker closed - resuming normal operations');
    }
    
    /**
     * Record system metrics and check if circuit should open
     */
    public function checkAndRecord(array $metrics): void
    {
        // Store current metrics
        Cache::put(self::METRICS_KEY, $metrics, now()->addMinutes(5));
        
        $config = config('database_limits.circuit_breaker');
        
        // Check CPU threshold
        if (isset($metrics['cpu_usage']) && $metrics['cpu_usage'] > $config['cpu_threshold']) {
            $this->open("High CPU usage: {$metrics['cpu_usage']}%");
            return;
        }
        
        // Check memory threshold
        if (isset($metrics['memory_usage']) && $metrics['memory_usage'] > $config['memory_threshold']) {
            $this->open("High memory usage: {$metrics['memory_usage']}%");
            return;
        }
        
        // Check slow queries
        if (isset($metrics['slow_queries']) && $metrics['slow_queries'] > $config['slow_query_threshold']) {
            $this->open("Too many slow queries: {$metrics['slow_queries']}");
            return;
        }
    }
    
    /**
     * Get current system metrics
     */
    public function getMetrics(): array
    {
        return Cache::get(self::METRICS_KEY, [
            'cpu_usage' => 0,
            'memory_usage' => 0,
            'slow_queries' => 0,
            'last_check' => null,
        ]);
    }
    
    /**
     * Check if a specific feature should be disabled
     */
    public function shouldDisableFeature(string $feature): bool
    {
        if (!$this->isOpen()) {
            return false;
        }
        
        $config = config('database_limits.circuit_breaker');
        
        return match($feature) {
            'analytics' => $config['disable_analytics'] ?? true,
            'exports' => $config['disable_exports'] ?? true,
            'cached_only' => $config['serve_cached_only'] ?? true,
            default => false,
        };
    }
}
