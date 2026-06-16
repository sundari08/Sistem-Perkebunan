<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    
    // Firebase ServiceProvider (dari package)
    Kreait\Laravel\Firebase\ServiceProvider::class,
    
    // FirebaseServiceProvider KITA (harus setelah package)
    App\Providers\FirebaseServiceProvider::class,
];