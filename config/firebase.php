<?php

declare(strict_types=1);

// Decode credentials langsung di sini
$credentials = env('FIREBASE_CREDENTIALS');
$decodedCredentials = null;

if ($credentials && is_string($credentials) && str_starts_with(trim($credentials), '{')) {
    $decoded = json_decode($credentials, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $decodedCredentials = $decoded;
    }
}

return [
    'default' => env('FIREBASE_PROJECT', 'app'),

    'projects' => [
        'app' => [
            'credentials' => $decodedCredentials, // Langsung pakai array hasil decode
            
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