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

    // Di ServiceProvider, tambahkan fallback
    protected function decodeFirebaseCredentials(): void
    {
        $credentials = env('FIREBASE_CREDENTIALS');
        
        // Jika credentials adalah file path yang valid
        if ($credentials && is_string($credentials) && file_exists($credentials)) {
            $content = file_get_contents($credentials);
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Config::set('firebase.projects.app.credentials', $decoded);
                return;
            }
        }
        
        // Jika credentials adalah JSON string
        if ($credentials && is_string($credentials) && str_starts_with(trim($credentials), '{')) {
            $decoded = json_decode($credentials, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                Config::set('firebase.projects.app.credentials', $decoded);
                return;
            }
        }
        
        // Fallback: coba GOOGLE_APPLICATION_CREDENTIALS
        $googleCreds = env('GOOGLE_APPLICATION_CREDENTIALS');
        if ($googleCreds && is_string($googleCreds) && file_exists($googleCreds)) {
            $content = file_get_contents($googleCreds);
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                Config::set('firebase.projects.app.credentials', $decoded);
            }
        }
    }
}