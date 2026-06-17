<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Decode credentials di register agar tersedia sebelum Firebase ServiceProvider digunakan
        $this->decodeFirebaseCredentials();
    }

    // Di FirebaseServiceProvider.php, tambahkan di boot():
    public function boot(): void
    {
        $this->decodeFirebaseCredentials();
        
        // Debug: cek apakah credentials sudah terisi
        $creds = config('firebase.projects.app.credentials');
        if (is_array($creds)) {
            // \Log::info('Firebase credentials is array, project_id: ' . ($creds['project_id'] ?? 'unknown'));
        } else {
            // \Log::warning('Firebase credentials is NOT set properly');
        }
    }

    protected function decodeFirebaseCredentials(): void
    {
        $credentials = env('FIREBASE_CREDENTIALS');
        
        if ($credentials && is_string($credentials) && str_starts_with(trim($credentials), '{')) {
            $decoded = json_decode($credentials, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Set credentials sebagai array
                Config::set('firebase.projects.app.credentials', $decoded);
                
                // Log untuk debugging (opsional)
                // \Log::info('Firebase credentials decoded successfully');
            }
        }
    }
}