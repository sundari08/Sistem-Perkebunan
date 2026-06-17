<?php

declare(strict_types=1);

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            // ✅ GUNAKAN JSON STRING LANGSUNG
            'credentials' => env('FIREBASE_CREDENTIALS'),
            
            'auth' => [
                'tenant_id' => env('FIREBASE_AUTH_TENANT_ID'),
            ],

            'firestore' => [
                'project_id' => env('FIREBASE_PROJECT_ID', 'projek-4615c'),
            ],

            'database' => [
                'url' => env('FIREBASE_DATABASE_URL', 'https://projek-4615c.firebaseio.com'),
            ],

            'storage' => [
                'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),
            ],

            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],

            'http_client_options' => [
                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),
                'verify' => env('FIREBASE_HTTP_CLIENT_VERIFY', false),
                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT', 30),
                'guzzle_middlewares' => [],
            ],
        ],
    ],
];