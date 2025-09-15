<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for different API endpoints and user types.
    |
    */

    'rate_limits' => [
        'default' => [
            'max_attempts' => 60,
            'decay_minutes' => 1,
        ],
        'auth' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
        ],
        'search' => [
            'max_attempts' => 30,
            'decay_minutes' => 1,
        ],
        'recommendations' => [
            'max_attempts' => 100,
            'decay_minutes' => 1,
        ],
        'analytics' => [
            'max_attempts' => 200,
            'decay_minutes' => 1,
        ],
        'admin' => [
            'max_attempts' => 200,
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Response Configuration
    |--------------------------------------------------------------------------
    |
    | Configure default API response formats and pagination.
    |
    */

    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    'response' => [
        'include_debug_info' => env('APP_DEBUG', false),
        'include_execution_time' => env('API_INCLUDE_EXECUTION_TIME', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Versioning
    |--------------------------------------------------------------------------
    |
    | Configure API versioning settings.
    |
    */

    'versioning' => [
        'default_version' => 'v1',
        'header_name' => 'Accept-Version',
        'supported_versions' => ['v1'],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    |
    | Configure API security settings.
    |
    */

    'security' => [
        'require_https' => env('API_REQUIRE_HTTPS', false),
        'cors_enabled' => env('API_CORS_ENABLED', true),
        'allowed_origins' => env('API_ALLOWED_ORIGINS', '*'),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'Accept', 'Accept-Version'],
    ],
];
