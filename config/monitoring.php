<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the monitoring system including
    | alert thresholds, notification settings, and retention periods.
    |
    */

    'alert_email' => env('ALERT_EMAIL', env('SUPPORT_EMAIL', 'hello@thetradevisor.com')),
    'slack_webhook_url' => env('SLACK_WEBHOOK_URL'),
    'support_email' => env('SUPPORT_EMAIL', 'hello@thetradevisor.com'),

    'thresholds' => [
        'error_rate' => env('MONITORING_ERROR_RATE_THRESHOLD', 5.0), // percentage
        'memory_usage' => env('MONITORING_MEMORY_THRESHOLD', 80.0), // percentage
        'queue_wait_time' => env('MONITORING_QUEUE_WAIT_THRESHOLD', 30.0), // seconds
        'database_connections' => env('MONITORING_DB_CONNECTIONS_THRESHOLD', 80), // count
        'disk_usage' => env('MONITORING_DISK_USAGE_THRESHOLD', 85), // percentage
        'system_load' => env('MONITORING_SYSTEM_LOAD_THRESHOLD', 2.0), // load average
    ],

    'notifications' => [
        'email' => env('MONITORING_EMAIL_NOTIFICATIONS', true),
        'slack' => env('MONITORING_SLACK_NOTIFICATIONS', false), // Future feature
        'sms' => env('MONITORING_SMS_NOTIFICATIONS', false), // Future feature
        'alert_channel' => env('MONITORING_ALERT_CHANNEL', 'email'), // email, slack, or none
    ],

    'retention' => [
        'alerts' => env('MONITORING_ALERTS_RETENTION_DAYS', 30), // days
        'metrics' => env('MONITORING_METRICS_RETENTION_DAYS', 30), // days
        'telescope' => env('MONITORING_TELESCOPE_RETENTION_HOURS', 720), // hours (30 days)
    ],

    'cooldowns' => [
        'warning_alerts' => env('MONITORING_WARNING_COOLDOWN_MINUTES', 15), // minutes
        'critical_alerts' => env('MONITORING_CRITICAL_COOLDOWN_MINUTES', 5), // minutes
    ],

    'new_relic' => [
        'enabled' => env('NEW_RELIC_ENABLED', true),
        'app_name' => env('NEW_RELIC_APP_NAME', 'TheTradeVisor'),
        'license' => env('NEW_RELIC_LICENSE'),
    ],
];
