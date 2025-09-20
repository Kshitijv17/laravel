<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Image Storage Disk
    |--------------------------------------------------------------------------
    |
    | This option controls the default disk that will be used to store images.
    | Supported: "local", "public", "s3", "cloudinary"
    |
    */
    'default' => env('IMAGE_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Image Storage Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage disks for different image storage
    | backends. You may configure multiple disks for the same driver.
    |
    */
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'url' => '/storage',
            'visibility' => 'private',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'root' => 'images',
        ],

        'cloudinary' => [
            'driver' => 'cloudinary',
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
            'secure' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing
    |--------------------------------------------------------------------------
    |
    | Configure the default image processing settings.
    |
    */
    'processing' => [
        'default' => [
            'quality' => 90,
            'width' => null,
            'height' => null,
            'constraint' => true, // Maintain aspect ratio
        ],
        
        'thumbnails' => [
            'small' => [
                'width' => 150,
                'height' => 150,
                'quality' => 80,
            ],
            'medium' => [
                'width' => 300,
                'height' => 300,
                'quality' => 85,
            ],
            'large' => [
                'width' => 800,
                'height' => 800,
                'quality' => 90,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Validation Rules
    |--------------------------------------------------------------------------
    |
    | Define the validation rules for uploaded images.
    |
    */
    'validation' => [
        'mimetypes' => [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ],
        'max_size' => 5120, // KB
        'min_dimensions' => [
            'width' => 10,
            'height' => 10,
        ],
        'max_dimensions' => [
            'width' => 5000,
            'height' => 5000,
        ],
    ],
];
