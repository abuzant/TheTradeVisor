<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Query Limits
    |--------------------------------------------------------------------------
    |
    | These limits prevent runaway queries from consuming system resources
    |
    */
    
    'query_timeout' => env('DB_QUERY_TIMEOUT', 30), // seconds
    
    'max_rows_without_limit' => 1000,
    
    'analytics_cache_duration' => env('ANALYTICS_CACHE_DURATION', 300), // 5 minutes
    
    'max_concurrent_analytics_requests' => env('MAX_ANALYTICS_REQUESTS', 5),
    
    /*
    |--------------------------------------------------------------------------
    | Circuit Breaker Settings
    |--------------------------------------------------------------------------
    |
    | Automatically disable expensive operations under high load
    |
    */
    
    'circuit_breaker' => [
        'enabled' => env('CIRCUIT_BREAKER_ENABLED', true),
        
        // Thresholds
        'cpu_threshold' => 80, // percentage
        'memory_threshold' => 85, // percentage
        'slow_query_threshold' => 5, // count per minute
        
        // Actions when circuit is open
        'disable_analytics' => true,
        'disable_exports' => true,
        'serve_cached_only' => true,
        
        // Recovery
        'recovery_time' => 300, // seconds before attempting to close circuit
    ],
];
