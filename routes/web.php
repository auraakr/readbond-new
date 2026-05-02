<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CollectionController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', WelcomeController::class)->name('welcome');
Route::get('/books', [BooksController::class, 'index'])->name('books');
Route::get('/books/autocomplete', [BooksController::class, 'autocomplete'])->name('books.autocomplete');
Route::get('/books/{external_id}', [BooksController::class, 'show'])->name('books.show');

Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/create', [CollectionController::class, 'create'])->name('collections.create');
Route::get('/collections/{id}', [CollectionController::class, 'show'])->name('collections.show');

Route::prefix('collections')->name('collections.')->group(function () {
    Route::get('/',           [CollectionController::class, 'index'])->name('index');
    Route::get('/create',     [CollectionController::class, 'create'])->name('create');
    Route::get('/{id}',       [CollectionController::class, 'show'])->name('show');

    // Routes berikut butuh login
    Route::middleware('auth')->group(function () {
        Route::post('/',                          [CollectionController::class, 'store'])->name('store');
        Route::delete('/{id}',                    [CollectionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/like',                 [CollectionController::class, 'toggleLike'])->name('like');
        Route::post('/{id}/comments',             [CollectionController::class, 'storeComment'])->name('comments.store');
        Route::delete('/comments/{commentId}',    [CollectionController::class, 'destroyComment'])->name('comments.destroy');
        Route::post('/comments/{commentId}/like', [CollectionController::class, 'toggleCommentLike'])->name('comments.like');
        Route::post('/{id}/books/add',            [CollectionController::class, 'addBook'])->name('books.add');
        Route::post('/{id}/books/remove',         [CollectionController::class, 'removeBook'])->name('books.remove');
    });
});

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