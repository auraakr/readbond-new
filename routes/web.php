<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', WelcomeController::class)->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Route khusus admin dengan prefix 'admin' agar URL menjadi /admin/dashboard
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Tambahkan route admin lainnya di sini...
});

/*
|--------------------------------------------------------------------------
| Front / User Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Taruh route fitur user lain di sini (misal: /settings, /reading-log)
    // ...

    /**
     * CATCH-ALL ROUTE (WAJIB PALING BAWAH)
     * Route ini ditaruh paling bawah agar tidak bentrok dengan route statis.
     */
    Route::get('/{user:username}', function (User $user) {
        return view('profile', ['user' => $user]);
    })->name('profile');
});