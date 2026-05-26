<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BookReviewController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ReadingLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiaryLogController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\BookClubController;

/*
|--------------------------------------------------------------------------
| 1. GUEST / PUBLIC ROUTES
|--------------------------------------------------------------------------
| Route yang bisa diakses tanpa login (atau khusus user yang belum login).
*/

// Public Landing & Catalog
Route::get('/', WelcomeController::class)->name('welcome');
Route::get('/books', [BooksController::class, 'index'])->name('books');
Route::get('/books/autocomplete', [BooksController::class, 'autocomplete'])->name('books.autocomplete');
Route::get('/books/{external_id}', [BooksController::class, 'show'])->name('books.show');

// Public Collection Views
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');

// Authentication (Khusus Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED USER ROUTES (Butuh Login)
|--------------------------------------------------------------------------
| Semua fitur interaktif user (Like, Diary, Collection Management, Profile).
*/
Route::middleware('auth')->group(function () {

    // Global Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Edit Profile
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');

    // User Book Interactions
    Route::prefix('books')->name('books.')->group(function () {
        Route::post('/{id}/like',              [BooksController::class, 'toggleLike'])->name('like');
        Route::post('/{id}/rate',              [BooksController::class, 'rate'])->name('rate');
        Route::post('/{id}/readlist',          [BooksController::class, 'toggleReadlist'])->name('readlist');
        Route::post('/{id}/reading-log',       [BooksController::class, 'storeReadingLog'])->name('reading-log');
        Route::post('/{id}/add-to-collection', [BooksController::class, 'addToCollection'])->name('add-to-collection');
    });

    // Reading Logs Management
    Route::prefix('reading-log')->name('reading-log.')->group(function () {
        Route::get('/',        [ReadingLogController::class, 'index'])->name('index');
        Route::delete('/{id}', [ReadingLogController::class, 'destroy'])->name('destroy');
    });

    // Protected Collection Management
    Route::prefix('collections')->name('collections.')->group(function () {
        Route::get('/',                           [CollectionController::class, 'index'])->name('index');
        Route::get('/create',                     [CollectionController::class, 'create'])->name('create');
        Route::post('/',                          [CollectionController::class, 'store'])->name('store');
        Route::delete('/{id}',                    [CollectionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/like',                 [CollectionController::class, 'toggleLike'])->name('like');
        Route::patch('/{id}/visibility',          [CollectionController::class, 'updateVisibility'])->name('visibility');
        Route::post('/{id}/books/add',            [CollectionController::class, 'addBook'])->name('books.add');
        Route::post('/{id}/books/remove',         [CollectionController::class, 'removeBook'])->name('books.remove');
        
        // Collection Comments
        Route::post('/{id}/comments',             [CollectionController::class, 'storeComment'])->name('comments.store');
        Route::delete('/comments/{commentId}',    [CollectionController::class, 'destroyComment'])->name('comments.destroy');
        Route::post('/comments/{commentId}/like', [CollectionController::class, 'toggleCommentLike'])->name('comments.like');
    });

    // Diary Features
    Route::prefix('diary')->name('diary.')->group(function () {
        Route::get('/',                      [DiaryLogController::class, 'index'])->name('index');
        Route::get('/create',                [DiaryLogController::class, 'create'])->name('create');
        Route::post('/',                     [DiaryLogController::class, 'store'])->name('store');
        Route::get('/{diaryLog}/edit',       [DiaryLogController::class, 'edit'])->name('edit');
        Route::patch('/{diaryLog}',          [DiaryLogController::class, 'update'])->name('update');
        Route::delete('/{diaryLog}',         [DiaryLogController::class, 'destroy'])->name('destroy');
        Route::post('/{diaryLog}/favorite',  [DiaryLogController::class, 'toggleFavorite'])->name('favorite');
    });

    // User Profiles Sub-Tabs
    Route::prefix('{user:username}')->name('profile.')->group(function () {
        Route::get('/activity',    [ProfileController::class, 'activity'])->name('activity');
        Route::get('/books',       [ProfileController::class, 'books'])->name('books');
        Route::get('/reviews',     [ProfileController::class, 'reviews'])->name('reviews');
        Route::get('/readlist',    [ProfileController::class, 'readlist'])->name('readlist');
        Route::get('/collections', [ProfileController::class, 'collections'])->name('collections');
        Route::get('/clubs',       [ProfileController::class, 'clubs'])->name('clubs');
    });

    // Book Club Features
    Route::prefix('clubs')->name('clubs.')->group(function () {
        
        // 1. Rute Statis Utama (Wajib di Atas agar Tidak Tertabrak Slug)
        Route::get('/', [BookClubController::class, 'index'])->name('index');
        Route::get('/create', [BookClubController::class, 'create'])->name('create');
        Route::post('/', [BookClubController::class, 'store'])->name('store');

        // 2. Rute Menggunakan ID Global (Aman Ditulis di Sini)
        Route::put('/{id}', [BookClubController::class, 'update'])->name('update');
        Route::post('/{id}/add-moderator', [BookClubController::class, 'addModerator'])->name('add-moderator');
        Route::post('/{id}/toggle-join', [BookClubController::class, 'toggleJoin'])->name('toggle-join');

        // 3. Rute Berbasis Slug Klub & Fitur Internal Sub-Mendasar (Nested)
        Route::prefix('{slug}')->group(function () {
            // Halaman Utama & Edit Klub
            Route::get('/', [BookClubController::class, 'show'])->name('show');
            Route::get('/edit', [BookClubController::class, 'edit'])->name('edit');
            
            // Sub-Fitur: Diskusi Internal Klub (Sekarang Menerima $clubSlug Otomatis)
            Route::get('/discussion/create', [BookClubController::class, 'createDiscussion'])->name('discussion.create');
            Route::post('/discussion', [BookClubController::class, 'storeDiscussion'])->name('discussion.store');
            Route::get('/discussion/{discussion}', [BookClubController::class, 'showDiscussion'])->name('discussion.show');
            
            // Sub-Fitur: Balasan Post di Dalam Diskusi
            Route::post('/discussion/{discussion}/posts', [BookClubController::class, 'storePost'])->name('discussion.posts.store');

            Route::post('/books/add', [BookClubController::class, 'addBook'])->name('books.add');
            Route::patch('/books/{bookId}/status', [BookClubController::class, 'updateBookStatus'])->name('books.status.update');
        });
    });
    /**
     * CATCH-ALL ROUTE (WAJIB PALING BAWAH)
     * Ditempatkan di dasar scope 'auth' agar username dinamis tidak menabrak url statis seperti /diary atau /collections
     */
    Route::get('/collections/{id}', [CollectionController::class, 'show'])->name('collections.show');
    
    Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profile');
    
    // Book Reviews Interactions
    Route::post('/reviews/{review}/like', [BooksController::class, 'toggleLikeReview'])->name('reviews.like');

});


/*
|--------------------------------------------------------------------------
| 3. ADMIN ROUTES
|--------------------------------------------------------------------------
| Area eksklusif administrator. Menggunakan prefix dan name spacing 'admin.'.
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Resource CRUD Books untuk Admin
    Route::resource('books', AdminBookController::class);
});