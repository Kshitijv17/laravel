<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Multi-language Configuration
    |--------------------------------------------------------------------------
    |
    | Configure supported languages and localization settings.
    |
    */

    'supported_locales' => [
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇺🇸',
            'rtl' => false,
        ],
        'es' => [
            'name' => 'Spanish',
            'native_name' => 'Español',
            'flag' => '🇪🇸',
            'rtl' => false,
        ],
        'fr' => [
            'name' => 'French',
            'native_name' => 'Français',
            'flag' => '🇫🇷',
            'rtl' => false,
        ],
        'de' => [
            'name' => 'German',
            'native_name' => 'Deutsch',
            'flag' => '🇩🇪',
            'rtl' => false,
        ],
        'ar' => [
            'name' => 'Arabic',
            'native_name' => 'العربية',
            'flag' => '🇸🇦',
            'rtl' => true,
        ],
        'zh' => [
            'name' => 'Chinese',
            'native_name' => '中文',
            'flag' => '🇨🇳',
            'rtl' => false,
        ],
        'ja' => [
            'name' => 'Japanese',
            'native_name' => '日本語',
            'flag' => '🇯🇵',
            'rtl' => false,
        ],
        'hi' => [
            'name' => 'Hindi',
            'native_name' => 'हिन्दी',
            'flag' => '🇮🇳',
            'rtl' => false,
        ],
    ],

    'default_locale' => 'en',
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Currency Configuration
    |--------------------------------------------------------------------------
    |
    | Configure currencies for different locales.
    |
    */

    'currencies' => [
        'en' => 'USD',
        'es' => 'EUR',
        'fr' => 'EUR',
        'de' => 'EUR',
        'ar' => 'SAR',
        'zh' => 'CNY',
        'ja' => 'JPY',
        'hi' => 'INR',
    ],

    'currency_symbols' => [
        'USD' => '$',
        'EUR' => '€',
        'SAR' => 'ر.س',
        'CNY' => '¥',
        'JPY' => '¥',
        'INR' => '₹',
    ],

    /*
    |--------------------------------------------------------------------------
    | Date and Time Formats
    |--------------------------------------------------------------------------
    |
    | Configure date and time formats for different locales.
    |
    */

    'date_formats' => [
        'en' => 'M j, Y',
        'es' => 'd/m/Y',
        'fr' => 'd/m/Y',
        'de' => 'd.m.Y',
        'ar' => 'Y/m/d',
        'zh' => 'Y年m月d日',
        'ja' => 'Y年m月d日',
        'hi' => 'd/m/Y',
    ],

    'time_formats' => [
        'en' => 'g:i A',
        'es' => 'H:i',
        'fr' => 'H:i',
        'de' => 'H:i',
        'ar' => 'H:i',
        'zh' => 'H:i',
        'ja' => 'H:i',
        'hi' => 'H:i',
    ],
];
