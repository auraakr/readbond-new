<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ReadingLogController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\BookController as AdminBookController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', WelcomeController::class)->name('welcome');
Route::get('/books', [BooksController::class, 'index'])->name('books');
Route::get('/books/autocomplete', [BooksController::class, 'autocomplete'])->name('books.autocomplete');
Route::get('/books/{external_id}', [BooksController::class, 'show'])->name('books.show');

// Protected book routes (butuh login)
Route::middleware('auth')->prefix('books')->name('books.')->group(function () {
    Route::post('/{id}/like',              [BooksController::class, 'toggleLike'])->name('like');
    Route::post('/{id}/rate',              [BooksController::class, 'rate'])->name('rate');
    Route::post('/{id}/readlist',          [BooksController::class, 'toggleReadlist'])->name('readlist');
    Route::post('/{id}/reading-log',       [BooksController::class, 'storeReadingLog'])->name('reading-log');
    Route::post('/{id}/add-to-collection', [BooksController::class, 'addToCollection'])->name('add-to-collection');
});

Route::middleware('auth')->prefix('reading-log')->name('reading-log.')->group(function () {
    Route::get('/',          [ReadingLogController::class, 'index'])->name('index');
    Route::delete('/{id}',   [ReadingLogController::class, 'destroy'])->name('destroy');
});

Route::prefix('collections')->name('collections.')->group(function () {
    // PUBLIC routes
    Route::get('/',           [CollectionController::class, 'index'])->name('index');

    // PROTECTED routes (butuh login)
    Route::middleware('auth')->group(function () {
        Route::get('/create',                     [CollectionController::class, 'create'])->name('create');
        Route::post('/',                          [CollectionController::class, 'store'])->name('store');
        Route::delete('/{id}',                    [CollectionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/like',                 [CollectionController::class, 'toggleLike'])->name('like');
        Route::post('/{id}/comments',             [CollectionController::class, 'storeComment'])->name('comments.store');
        Route::delete('/comments/{commentId}',    [CollectionController::class, 'destroyComment'])->name('comments.destroy');
        Route::post('/comments/{commentId}/like', [CollectionController::class, 'toggleCommentLike'])->name('comments.like');
        Route::post('/{id}/books/add',            [CollectionController::class, 'addBook'])->name('books.add');
        Route::post('/{id}/books/remove',         [CollectionController::class, 'removeBook'])->name('books.remove');
        Route::patch('/{id}/visibility',          [CollectionController::class, 'updateVisibility'])->name('visibility');
    });

    // PUBLIC routes (ditaruh paling bawah)
    Route::get('/{id}',       [CollectionController::class, 'show'])->name('show');
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
    
    // Menggunakan alias AdminBookController agar tidak bentrok dengan BooksController user
    Route::resource('books', AdminBookController::class);
});

/*
|--------------------------------------------------------------------------
| Front / User Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Taruh route fitur user lain di sini (misal: /settings, /reading-log)
    Route::get('/{user:username}/activity', [ProfileController::class, 'activity'])->name('profile.activity');
    Route::get('/{user:username}/books', [ProfileController::class, 'books'])->name('profile.books');
    Route::get('/{user:username}/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');
    Route::get('/{user:username}/readlist', [ProfileController::class, 'readlist'])->name('profile.readlist');
    Route::get('/{user:username}/collections', [ProfileController::class, 'collections'])->name('profile.collections');

    /**
     * CATCH-ALL ROUTE (WAJIB PALING BAWAH)
     * Route ini ditaruh paling bawah agar tidak bentrok dengan route statis.
     */
    Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profile');
});