<?php

return [
    'supported' => [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇺🇸',
            'direction' => 'ltr'
        ],
        'hi' => [
            'name' => 'Hindi',
            'native' => 'हिंदी',
            'flag' => '🇮🇳',
            'direction' => 'ltr'
        ],
        'es' => [
            'name' => 'Spanish',
            'native' => 'Español',
            'flag' => '🇪🇸',
            'direction' => 'ltr'
        ],
        'fr' => [
            'name' => 'French',
            'native' => 'Français',
            'flag' => '🇫🇷',
            'direction' => 'ltr'
        ],
        'ar' => [
            'name' => 'Arabic',
            'native' => 'العربية',
            'flag' => '🇸🇦',
            'direction' => 'rtl'
        ]
    ],
    
    'default' => 'en',
    'fallback' => 'en',
    
    // Auto-detect from browser
    'auto_detect' => true,
    
    // Store user preference in session/database
    'remember_choice' => true
];
