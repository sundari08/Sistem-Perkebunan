<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;

class CustomGuzzleClient
{
    public static function create()
    {
        // 1. Buat handler stack
        $handlerStack = HandlerStack::create(new CurlHandler());

        // 2. Tambahkan middleware untuk memaksa 'verify' => false
        $handlerStack->push(Middleware::mapRequest(function ($request) {
            return $request->withHeader('User-Agent', 'My-Custom-Guzzle-Client');
        }));

        // 3. Buat client dengan konfigurasi super paksa
        return new Client([
            'verify'          => false,      // Matikan SSL verification!
            'timeout'         => 30,
            'handler'         => $handlerStack,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]
        ]);
    }
}