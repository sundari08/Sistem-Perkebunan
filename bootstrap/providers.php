<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    Kreait\Laravel\Firebase\ServiceProvider::class,
    App\Providers\FirebaseServiceProvider::class, // ← Tambahkan
];