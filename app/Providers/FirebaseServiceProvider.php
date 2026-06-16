<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tidak melakukan apa-apa di register
        // Karena Firebase ServiceProvider sudah dijalankan
    }

    public function boot(): void
    {
        // 🔥 GUNAKAN BOOT, BUKAN REGISTER
        $credentials = env('FIREBASE_CREDENTIALS');
        
        if ($credentials && is_string($credentials) && str_starts_with($credentials, '{')) {
            $decoded = json_decode($credentials, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                // Set credentials ke config
                Config::set('firebase.projects.app.credentials', $decoded);
                
                // Jika project_id tidak ada di credentials, ambil dari env
                if (!isset($decoded['project_id']) && env('FIREBASE_PROJECT_ID')) {
                    Config::set('firebase.projects.app.credentials.project_id', env('FIREBASE_PROJECT_ID'));
                }
            }
        }
    }
}