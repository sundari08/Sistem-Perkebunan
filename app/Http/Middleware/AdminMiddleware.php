<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (session('jabatan') != 'ADMIN') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman admin!');
        }
        
        return $next($request);
    }
}
