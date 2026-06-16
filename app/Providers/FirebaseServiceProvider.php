<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $credentials = env('FIREBASE_CREDENTIALS');
        
        if ($credentials && is_string($credentials) && str_starts_with($credentials, '{')) {
            $decoded = json_decode($credentials, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                // Set credentials langsung ke config
                Config::set('firebase.projects.app.credentials', $decoded);
            }
        }
    }

    public function boot(): void
    {
        //
    }
}