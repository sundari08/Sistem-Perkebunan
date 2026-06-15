<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $otorisasi = session('otorisasi');
        $jabatan = session('jabatan');
        
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }
        
        // ADMIN punya semua akses
        if ($jabatan == 'ADMIN') {
            return $next($request);
        }
        
        $allowed = false;
        
        foreach ($permissions as $permission) {
            if ($permission == 'input_data' && str_contains($otorisasi, 'input data')) {
                $allowed = true;
            }
            if ($permission == 'edit_hapus' && str_contains($otorisasi, 'edit, hapus')) {
                $allowed = true;
            }
            if ($permission == 'lihat_laporan' && (str_contains($otorisasi, 'lihat laporan') || str_contains($otorisasi, 'input data') || str_contains($otorisasi, 'edit, hapus'))) {
                $allowed = true;
            }
        }
        
        if (!$allowed) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini!');
        }
        
        return $next($request);
    }
}