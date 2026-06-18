<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;

class AuthController extends Controller
{
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        
        $users = $this->database->getReference('users')->getValue();
        
        if (!$users) {
            return back()->with('error', 'Tidak ada user terdaftar!');
        }
        
        $userFound = null;
        
        foreach ($users as $key => $user) {
            if (isset($user['username']) && $user['username'] == $request->username) {
                $userFound = $user;
                $userFound['firebase_key'] = $key;
                break;
            }
        }
        
        if (!$userFound) {
            return back()->with('error', 'Username tidak ditemukan!');
        }
        
        // Verifikasi password
        if (!password_verify($request->password, $userFound['password'])) {
            return back()->with('error', 'Password salah!');
        }
        
        // Simpan session
        session([
            'user_id' => $userFound['firebase_key'],
            'username' => $userFound['username'],
            'jabatan' => $userFound['jabatan'] ?? '',
            'unit' => $userFound['unit'] ?? '',
            'estate' => $userFound['estate'] ?? null,
            'divisi' => $userFound['divisi'] ?? null,
            'otorisasi' => $userFound['otorisasi'] ?? '',
        ]);
        
        // ✅ REDIRECT BERDASARKAN JABATAN
        $jabatan = $userFound['jabatan'] ?? '';
        
        if ($jabatan == 'ADMIN') {
            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil!');
        } else {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }
    }

    public function dashboard()
    {
        return view('dashboard', [
            'jabatan' => session('jabatan', ''),
            'otorisasi' => session('otorisasi', ''),
            'username' => session('username', ''),
            'unit' => session('unit', ''),
            'estate' => session('estate'),
            'divisi' => session('divisi'),
        ]);
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}