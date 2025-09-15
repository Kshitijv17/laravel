<?php

return [
    'supported' => [
        'USD' => [
            'name' => 'US Dollar',
            'symbol' => '$',
            'code' => 'USD',
            'rate' => 1.0, // Base currency
            'decimal_places' => 2,
            'format' => '$%s'
        ],
        'INR' => [
            'name' => 'Indian Rupee',
            'symbol' => '₹',
            'code' => 'INR',
            'rate' => 83.12, // 1 USD = 83.12 INR
            'decimal_places' => 2,
            'format' => '₹%s'
        ],
        'EUR' => [
            'name' => 'Euro',
            'symbol' => '€',
            'code' => 'EUR',
            'rate' => 0.92, // 1 USD = 0.92 EUR
            'decimal_places' => 2,
            'format' => '€%s'
        ],
        'GBP' => [
            'name' => 'British Pound',
            'symbol' => '£',
            'code' => 'GBP',
            'rate' => 0.79, // 1 USD = 0.79 GBP
            'decimal_places' => 2,
            'format' => '£%s'
        ],
        'JPY' => [
            'name' => 'Japanese Yen',
            'symbol' => '¥',
            'code' => 'JPY',
            'rate' => 149.50, // 1 USD = 149.50 JPY
            'decimal_places' => 0,
            'format' => '¥%s'
        ],
        'CAD' => [
            'name' => 'Canadian Dollar',
            'symbol' => 'C$',
            'code' => 'CAD',
            'rate' => 1.36, // 1 USD = 1.36 CAD
            'decimal_places' => 2,
            'format' => 'C$%s'
        ],
        'AUD' => [
            'name' => 'Australian Dollar',
            'symbol' => 'A$',
            'code' => 'AUD',
            'rate' => 1.53, // 1 USD = 1.53 AUD
            'decimal_places' => 2,
            'format' => 'A$%s'
        ]
    ],
    
    'default' => 'INR', // Changed to INR for Indian market
    'base' => 'USD', // Base currency for calculations
    
    // Auto-detect from user location
    'auto_detect' => true,
    
    // API for live exchange rates
    'exchange_api' => [
        'enabled' => true,
        'provider' => 'fixer', // fixer.io, exchangerate-api.com
        'api_key' => env('EXCHANGE_RATE_API_KEY'),
        'cache_duration' => 3600, // 1 hour
        'fallback_rates' => true
    ],
    
    // Country to currency mapping for auto-detection
    'country_currency' => [
        'IN' => 'INR',
        'US' => 'USD',
        'GB' => 'GBP',
        'DE' => 'EUR',
        'FR' => 'EUR',
        'JP' => 'JPY',
        'CA' => 'CAD',
        'AU' => 'AUD'
    ]
];
