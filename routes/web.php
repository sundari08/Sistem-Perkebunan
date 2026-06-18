<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\AdminController;

// ========== ROUTE LOGIN ==========
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/', function () {
    return redirect()->route('login');
}); 

// ========== ROUTE LOGOUT ==========
// Ubah dari hanya POST menjadi POST dan GET
Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

// ========== ROUTE ADMIN ==========
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'usersCreate'])->name('users.create');
    Route::post('/users', [AdminController::class, 'usersStore'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'usersEdit'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'usersUpdate'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'usersDestroy'])->name('users.destroy');
    
    Route::get('/estates', [AdminController::class, 'estatesIndex'])->name('estates.index');
    Route::get('/estates/create', [AdminController::class, 'estatesCreate'])->name('estates.create');
    Route::post('/estates', [AdminController::class, 'estatesStore'])->name('estates.store');
    Route::get('/estates/{id}/edit', [AdminController::class, 'estatesEdit'])->name('estates.edit');
    Route::put('/estates/{id}', [AdminController::class, 'estatesUpdate'])->name('estates.update');
    Route::delete('/estates/{id}', [AdminController::class, 'estatesDestroy'])->name('estates.destroy');
});

// ========== ROUTE YANG MEMERLUKAN LOGIN ==========
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Export Excel - HANYA SATU!
    Route::get('/panen/export', [FirebaseController::class, 'exportExcel'])->name('panen.export');
    // HAPUS route /export-panen di bawah ini!
    
    Route::get('/test-download', function () {
        $content = 'Hello, this is a test file.';
        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, $content);
        return response()->download($tempFile, 'test.txt')->deleteFileAfterSend(true);
    });

    Route::get('/panen/get-divisi-user', [FirebaseController::class, 'getDivisiByUser'])->name('panen.getDivisiByUser');
    Route::get('/panen/get-divisi-by-estate', [FirebaseController::class, 'getDivisiByEstate'])->name('panen.getDivisiByEstate');
    Route::get('/panen/get-all-divisi', [FirebaseController::class, 'getAllDivisi'])->name('panen.getAllDivisi');
    Route::get('/panen/get-estates-by-unit', [FirebaseController::class, 'getEstatesByUnit'])->name('panen.getEstatesByUnit');
    
    Route::get('/api/statistik-bulan', [FirebaseController::class, 'statistikBulan'])->name('panen.statistik')->middleware('auth');

    // Route untuk yang bisa INPUT DATA (KERANI & ADMIN)
    Route::middleware(['role:input_data'])->group(function () {
        Route::get('/panen/create', [FirebaseController::class, 'createView'])->name('panen.create');
        Route::post('/panen', [FirebaseController::class, 'store'])->name('panen.store');
    });
    
    // Route untuk yang bisa EDIT & HAPUS (ASISTEN & ADMIN)
    Route::middleware(['role:edit_hapus'])->group(function () {
        Route::get('/panen/{id}/edit', [FirebaseController::class, 'editView'])->name('panen.edit');
        Route::put('/panen/{id}', [FirebaseController::class, 'update'])->name('panen.update');
        Route::delete('/panen/{id}', [FirebaseController::class, 'destroy'])->name('panen.destroy');
    });

    // Route untuk DETAIL (show) - semua role yang login bisa lihat
    Route::get('/panen/{id}', [FirebaseController::class, 'show'])->name('panen.show');

    // Route untuk LIHAT LAPORAN (semua role)
    Route::middleware(['role:lihat_laporan'])->group(function () {
        Route::get('/panen', [FirebaseController::class, 'index'])->name('panen.index');
    });
    
    // API untuk dropdown
    Route::get('/api/estates', [FirebaseController::class, 'getEstates']);
    Route::get('/api/divisi/{estate_id}', [FirebaseController::class, 'getDivisi']);
});