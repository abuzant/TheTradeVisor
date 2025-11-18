<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query Result Limits
    |--------------------------------------------------------------------------
    |
    | These limits prevent unbounded database queries that can cause
    | system crashes. All queries should use these standardized limits.
    |
    */

    'query' => [
        // Dashboard and overview pages
        'dashboard_recent_trades' => env('LIMIT_DASHBOARD_TRADES', 20),
        'dashboard_open_positions' => env('LIMIT_DASHBOARD_POSITIONS', 10),
        
        // List pages (with pagination)
        'list_per_page' => env('LIMIT_LIST_PER_PAGE', 50),
        'list_max_per_page' => env('LIMIT_LIST_MAX_PER_PAGE', 100),
        
        // Analytics queries
        'analytics_max_records' => env('LIMIT_ANALYTICS_MAX', 10000),
        'analytics_top_symbols' => env('LIMIT_ANALYTICS_TOP_SYMBOLS', 25),
        'analytics_best_worst_trades' => env('LIMIT_ANALYTICS_BEST_WORST', 10),
        
        // Export limits
        'export_max_records' => env('LIMIT_EXPORT_MAX', 10000),
        
        // Admin queries
        'admin_list_per_page' => env('LIMIT_ADMIN_PER_PAGE', 50),
        'admin_logs_per_page' => env('LIMIT_ADMIN_LOGS', 100),
        
        // API responses
        'api_default_limit' => env('LIMIT_API_DEFAULT', 100),
        'api_max_limit' => env('LIMIT_API_MAX', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Cache durations in seconds for different types of data.
    |
    */

    'cache' => [
        // Short-lived cache (15 minutes)
        'short' => env('CACHE_TTL_SHORT', 900),
        
        // Medium cache (1 hour)
        'medium' => env('CACHE_TTL_MEDIUM', 3600),
        
        // Long cache (4 hours)
        'long' => env('CACHE_TTL_LONG', 14400),
        
        // Very long cache (24 hours)
        'very_long' => env('CACHE_TTL_VERY_LONG', 86400),
        
        // Specific cache durations
        'analytics' => env('CACHE_TTL_ANALYTICS', 900), // 15 min
        'broker_public' => env('CACHE_TTL_BROKER_PUBLIC', 14400), // 4 hours
        'currency_rates' => env('CACHE_TTL_CURRENCY_RATES', 3600), // 1 hour
        'symbol_mappings' => env('CACHE_TTL_SYMBOL_MAPPINGS', 86400), // 24 hours
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Default rate limits for various endpoints.
    | These can be overridden in the rate_limit_settings table.
    |
    */

    'rate_limit' => [
        // API endpoints
        'api_default' => env('RATE_LIMIT_API', 60), // per minute
        
        // Analytics endpoints
        'analytics' => env('RATE_LIMIT_ANALYTICS', 10), // per minute
        
        // Export endpoints
        'exports' => env('RATE_LIMIT_EXPORTS', 5), // per minute
        
        // Broker analytics
        'broker_analytics' => env('RATE_LIMIT_BROKER', 10), // per minute
        
        // Authentication endpoints
        'login' => env('RATE_LIMIT_LOGIN', 5), // per minute
        'register' => env('RATE_LIMIT_REGISTER', 3), // per minute
        'password_reset' => env('RATE_LIMIT_PASSWORD_RESET', 3), // per hour
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Limits
    |--------------------------------------------------------------------------
    |
    | Maximum sizes for file uploads.
    |
    */

    'upload' => [
        'max_file_size' => env('UPLOAD_MAX_FILE_SIZE', 10485760), // 10MB in bytes
        'max_json_size' => env('UPLOAD_MAX_JSON_SIZE', 5242880), // 5MB in bytes
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Defaults
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for list views.
    |
    */

    'pagination' => [
        'default_per_page' => 50,
        'options' => [25, 50, 100],
    ],

    /*
    |--------------------------------------------------------------------------
    | Historical Data Limits
    |--------------------------------------------------------------------------
    |
    | Limits for historical data processing and display.
    |
    */

    'historical' => [
        'max_days_analytics' => env('LIMIT_MAX_DAYS_ANALYTICS', 365),
        'default_days_analytics' => env('LIMIT_DEFAULT_DAYS_ANALYTICS', 30),
        'max_days_export' => env('LIMIT_MAX_DAYS_EXPORT', 365),
    ],

];
