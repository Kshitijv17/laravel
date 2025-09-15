<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for performance optimization
    | features including caching, image optimization, and CDN settings.
    |
    */

    'cache' => [
        'default_ttl' => env('CACHE_DEFAULT_TTL', 3600), // 1 hour
        'long_ttl' => env('CACHE_LONG_TTL', 86400), // 24 hours
        'page_cache_ttl' => env('PAGE_CACHE_TTL', 1800), // 30 minutes
        
        'tags' => [
            'products' => 'products',
            'categories' => 'categories',
            'brands' => 'brands',
            'banners' => 'banners',
            'settings' => 'settings',
        ],
        
        'keys' => [
            'popular_products' => 'popular_products',
            'featured_products' => 'featured_products',
            'latest_products' => 'latest_products',
            'categories_with_counts' => 'categories_with_counts',
            'brands_with_counts' => 'brands_with_counts',
            'navigation_menu' => 'navigation_menu',
            'site_settings' => 'site_settings',
        ]
    ],

    'images' => [
        'optimization' => [
            'enabled' => env('IMAGE_OPTIMIZATION_ENABLED', true),
            'quality' => env('IMAGE_QUALITY', 85),
            'webp_enabled' => env('WEBP_ENABLED', true),
        ],
        
        'sizes' => [
            'thumbnail' => env('IMAGE_THUMBNAIL_SIZE', 300),
            'medium' => env('IMAGE_MEDIUM_SIZE', 600),
            'large' => env('IMAGE_LARGE_SIZE', 1200),
        ],
        
        'formats' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'max_size' => env('IMAGE_MAX_SIZE', 5120), // 5MB in KB
    ],

    'cdn' => [
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL', ''),
        'assets_url' => env('CDN_ASSETS_URL', ''),
        'images_url' => env('CDN_IMAGES_URL', ''),
        
        'cloudflare' => [
            'zone_id' => env('CLOUDFLARE_ZONE_ID', ''),
            'api_token' => env('CLOUDFLARE_API_TOKEN', ''),
            'purge_cache_on_update' => env('CLOUDFLARE_PURGE_ON_UPDATE', true),
        ],
        
        'aws_cloudfront' => [
            'distribution_id' => env('AWS_CLOUDFRONT_DISTRIBUTION_ID', ''),
            'invalidation_paths' => ['/*'],
        ]
    ],

    'database' => [
        'query_cache' => env('DB_QUERY_CACHE', true),
        'slow_query_log' => env('DB_SLOW_QUERY_LOG', false),
        'slow_query_time' => env('DB_SLOW_QUERY_TIME', 2), // seconds
        
        'optimization' => [
            'auto_optimize' => env('DB_AUTO_OPTIMIZE', false),
            'optimize_schedule' => env('DB_OPTIMIZE_SCHEDULE', 'daily'),
        ]
    ],

    'compression' => [
        'gzip' => env('GZIP_COMPRESSION', true),
        'brotli' => env('BROTLI_COMPRESSION', false),
        'level' => env('COMPRESSION_LEVEL', 6),
    ],

    'minification' => [
        'css' => env('MINIFY_CSS', true),
        'js' => env('MINIFY_JS', true),
        'html' => env('MINIFY_HTML', false),
    ],

    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING', true),
        'log_slow_requests' => env('LOG_SLOW_REQUESTS', true),
        'slow_request_threshold' => env('SLOW_REQUEST_THRESHOLD', 1000), // milliseconds
        
        'metrics' => [
            'memory_usage' => true,
            'database_queries' => true,
            'cache_hit_rate' => true,
            'response_time' => true,
        ]
    ],

    'lazy_loading' => [
        'images' => env('LAZY_LOAD_IMAGES', true),
        'threshold' => env('LAZY_LOAD_THRESHOLD', '200px'),
    ],

    'preloading' => [
        'critical_resources' => [
            '/css/app.css',
            '/js/app.js',
        ],
        'dns_prefetch' => [
            '//fonts.googleapis.com',
            '//cdnjs.cloudflare.com',
        ]
    ]
];
