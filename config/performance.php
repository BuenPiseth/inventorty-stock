<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains performance optimization settings for the inventory
    | management system including caching, asset optimization, and more.
    |
    */

    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Asset Caching
        |--------------------------------------------------------------------------
        |
        | Configure asset caching strategies for better performance.
        |
        */
        'assets' => [
            'enabled' => env('ASSET_CACHE_ENABLED', true),
            'ttl' => env('ASSET_CACHE_TTL', 31536000), // 1 year
            'version' => env('ASSET_VERSION', '2.0'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Database Query Caching
        |--------------------------------------------------------------------------
        |
        | Cache frequently accessed database queries.
        |
        */
        'queries' => [
            'enabled' => env('QUERY_CACHE_ENABLED', true),
            'ttl' => env('QUERY_CACHE_TTL', 3600), // 1 hour
            'tags' => [
                'products' => 'products_cache',
                'categories' => 'categories_cache',
                'stock_movements' => 'stock_movements_cache',
                'reports' => 'reports_cache',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | View Caching
        |--------------------------------------------------------------------------
        |
        | Cache compiled views for better performance.
        |
        */
        'views' => [
            'enabled' => env('VIEW_CACHE_ENABLED', true),
            'ttl' => env('VIEW_CACHE_TTL', 86400), // 24 hours
        ],
    ],

    'optimization' => [
        /*
        |--------------------------------------------------------------------------
        | Asset Optimization
        |--------------------------------------------------------------------------
        |
        | Configure asset minification and compression.
        |
        */
        'assets' => [
            'minify_css' => env('MINIFY_CSS', true),
            'minify_js' => env('MINIFY_JS', true),
            'compress_images' => env('COMPRESS_IMAGES', true),
            'lazy_load_images' => env('LAZY_LOAD_IMAGES', true),
        ],

        /*
        |--------------------------------------------------------------------------
        | Database Optimization
        |--------------------------------------------------------------------------
        |
        | Database performance optimization settings.
        |
        */
        'database' => [
            'eager_loading' => env('EAGER_LOADING_ENABLED', true),
            'query_optimization' => env('QUERY_OPTIMIZATION', true),
            'index_hints' => env('INDEX_HINTS_ENABLED', true),
        ],

        /*
        |--------------------------------------------------------------------------
        | Response Optimization
        |--------------------------------------------------------------------------
        |
        | HTTP response optimization settings.
        |
        */
        'response' => [
            'gzip_compression' => env('GZIP_COMPRESSION', true),
            'etag_enabled' => env('ETAG_ENABLED', true),
            'browser_caching' => env('BROWSER_CACHING', true),
        ],
    ],

    'monitoring' => [
        /*
        |--------------------------------------------------------------------------
        | Performance Monitoring
        |--------------------------------------------------------------------------
        |
        | Monitor application performance and identify bottlenecks.
        |
        */
        'enabled' => env('PERFORMANCE_MONITORING', false),
        'slow_query_threshold' => env('SLOW_QUERY_THRESHOLD', 1000), // milliseconds
        'memory_threshold' => env('MEMORY_THRESHOLD', 128), // MB
        'response_time_threshold' => env('RESPONSE_TIME_THRESHOLD', 2000), // milliseconds
    ],

    'lazy_loading' => [
        /*
        |--------------------------------------------------------------------------
        | Lazy Loading Configuration
        |--------------------------------------------------------------------------
        |
        | Configure lazy loading for various components.
        |
        */
        'images' => [
            'enabled' => env('LAZY_LOAD_IMAGES', true),
            'threshold' => env('LAZY_LOAD_THRESHOLD', 300), // pixels
            'placeholder' => env('LAZY_LOAD_PLACEHOLDER', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjE4MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PC9zdmc+'),
        ],

        'components' => [
            'datatables' => env('LAZY_LOAD_DATATABLES', true),
            'select2' => env('LAZY_LOAD_SELECT2', true),
            'charts' => env('LAZY_LOAD_CHARTS', true),
        ],
    ],

    'cdn' => [
        /*
        |--------------------------------------------------------------------------
        | CDN Configuration
        |--------------------------------------------------------------------------
        |
        | Configure CDN usage for static assets.
        |
        */
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL', ''),
        'assets' => [
            'css' => env('CDN_CSS_ENABLED', false),
            'js' => env('CDN_JS_ENABLED', false),
            'images' => env('CDN_IMAGES_ENABLED', false),
        ],
    ],

    'preloading' => [
        /*
        |--------------------------------------------------------------------------
        | Resource Preloading
        |--------------------------------------------------------------------------
        |
        | Configure resource preloading for critical assets.
        |
        */
        'critical_css' => [
            'modern-optimized.css',
        ],
        'critical_js' => [
            'theme-manager.js',
        ],
        'dns_prefetch' => [
            'fonts.googleapis.com',
            'cdn.jsdelivr.net',
        ],
    ],
];
