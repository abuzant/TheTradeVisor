<?php

return [
    'cache' => [
        'performance_ttl' => env('ANALYTICS_PERFORMANCE_TTL', 300),
        'broker_ttl' => env('ANALYTICS_BROKER_TTL', 1800),
    ],
];
