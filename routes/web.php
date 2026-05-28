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
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\BookSearchController;
use App\Http\Controllers\BookClubController;

// admin
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController;

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
| 3. ADMIN ROUTES
|--------------------------------------------------------------------------
| Area eksklusif administrator. Menggunakan prefix dan name spacing 'admin.'.
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Resource CRUD Books untuk Admin
    Route::resource('books', BookController::class);

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::patch('/reports/{id}/dismiss', [ReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::delete('/reports/{id}/review', [ReportController::class, 'destroyReview'])->name('reports.destroyReview');
});

/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATED USER ROUTES (Butuh Login)
|--------------------------------------------------------------------------
| Semua fitur interaktif user (Like, Diary, Collection Management, Profile).
*/
Route::middleware('auth')->group(function () {

    // 1. GLOBAL & ACCOUNT MANAGEMENT (Rute Statis Mutlak)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');

    // API Pencarian Buku untuk Modal Favorit
    Route::get('/api/books/search', [BookSearchController::class, 'search'])->name('api.books.search');
    
    // Aksi Simpan & Hapus Buku Favorit
    Route::post('/settings/favorite-books', [ProfileController::class, 'addFavoriteBook'])->name('profile.favorite.add');
    Route::delete('/settings/favorite-books/{id}', [ProfileController::class, 'removeFavoriteBook'])->name('profile.favorite.remove');

    // 2. FRIENDS & SOCIAL INTERACTIONS (Aksi Statis)
    Route::get('/friends', [FriendsController::class, 'index'])->name('friends.index');
    Route::post('/user/{id}/follow', [FollowController::class, 'toggleFollow'])->name('user.follow');
    Route::post('/reviews/{review}/like', [BooksController::class, 'toggleLikeReview'])->name('reviews.like');

    // 3. BOOK INTERACTIONS
    Route::prefix('books')->name('books.')->group(function () {
        Route::post('/{id}/like',              [BooksController::class, 'toggleLike'])->name('like');
        Route::post('/{id}/rate',              [BooksController::class, 'rate'])->name('rate');
        Route::post('/{id}/readlist',          [BooksController::class, 'toggleReadlist'])->name('readlist');
        Route::post('/{id}/reading-log',       [BooksController::class, 'storeReadingLog'])->name('reading-log');
        Route::post('/{id}/add-to-collection', [BooksController::class, 'addToCollection'])->name('add-to-collection');
    });

    // 4. READING LOGS MANAGEMENT
    Route::prefix('reading-log')->name('reading-log.')->group(function () {
        Route::get('/',        [ReadingLogController::class, 'index'])->name('index');
        Route::delete('/{id}', [ReadingLogController::class, 'destroy'])->name('destroy');
    });

    // 5. COLLECTION MANAGEMENT (Rute Detail `show` dipindahkan ke sini)
    Route::prefix('collections')->name('collections.')->group(function () {
        Route::get('/',                           [CollectionController::class, 'index'])->name('index');
        Route::get('/create',                     [CollectionController::class, 'create'])->name('create');
        Route::post('/',                          [CollectionController::class, 'store'])->name('store');
        Route::get('/{id}',                       [CollectionController::class, 'show'])->name('show'); // AMAN DI SINI
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

    // 6. DIARY FEATURES
    Route::prefix('diary')->name('diary.')->group(function () {
        Route::get('/',                  [DiaryLogController::class, 'index'])->name('index');
        Route::get('/create',            [DiaryLogController::class, 'create'])->name('create');
        Route::post('/',                 [DiaryLogController::class, 'store'])->name('store');
        Route::get('/{diaryLog}/edit',   [DiaryLogController::class, 'edit'])->name('edit');
        Route::patch('/{diaryLog}',      [DiaryLogController::class, 'update'])->name('update');
        Route::delete('/{diaryLog}',     [DiaryLogController::class, 'destroy'])->name('destroy');
        Route::post('/{diaryLog}/favorite', [DiaryLogController::class, 'toggleFavorite'])->name('favorite');
    });

    // 7. BOOK CLUB FEATURES (Prefix Statis Sebelum Parameter Dinamis)
    Route::prefix('clubs')->name('clubs.')->group(function () {
        Route::get('/', [BookClubController::class, 'index'])->name('index');
        Route::get('/create', [BookClubController::class, 'create'])->name('create');
        Route::post('/', [BookClubController::class, 'store'])->name('store');

        Route::put('/{id}', [BookClubController::class, 'update'])->name('update');
        Route::post('/{id}/add-moderator', [BookClubController::class, 'addModerator'])->name('add-moderator');
        Route::post('/{id}/toggle-join', [BookClubController::class, 'toggleJoin'])->name('toggle-join');

        // Sub-Fitur Berbasis Slug Klub
        Route::prefix('{slug}')->group(function () {
            Route::get('/', [BookClubController::class, 'show'])->name('show');
            Route::get('/edit', [BookClubController::class, 'edit'])->name('edit');
            Route::get('/discussion/create', [BookClubController::class, 'createDiscussion'])->name('discussion.create');
            Route::post('/discussion', [BookClubController::class, 'storeDiscussion'])->name('discussion.store');
            Route::get('/discussion/{discussion}', [BookClubController::class, 'showDiscussion'])->name('discussion.show');
            Route::post('/discussion/{discussion}/posts', [BookClubController::class, 'storePost'])->name('discussion.posts.store');
            Route::post('/books/add', [BookClubController::class, 'addBook'])->name('books.add');
            Route::patch('/books/{bookId}/status', [BookClubController::class, 'updateBookStatus'])->name('books.status.update');
        });
    });

    // ─── 8. CATCH-ALL BLOCK (WAJIB DI PALING BAWAH GRUP) ───
    
    // Followers & Following (Dipindahkan ke bawah karena diawali parameter dinamis mentah)
    Route::get('/{username}/followers', [FollowController::class, 'followers'])->name('user.followers');
    Route::get('/{username}/following', [FollowController::class, 'following'])->name('user.following');

    // User Profiles Sub-Tabs
    Route::prefix('{user:username}')->name('profile.')->group(function () {
        Route::get('/activity',    [ProfileController::class, 'activity'])->name('activity');
        Route::get('/books',       [ProfileController::class, 'books'])->name('books');
        Route::get('/reviews',     [ProfileController::class, 'reviews'])->name('reviews');
        Route::get('/readlist',    [ProfileController::class, 'readlist'])->name('readlist');
        Route::get('/collections', [ProfileController::class, 'collections'])->name('collections');
        Route::get('/clubs',       [ProfileController::class, 'clubs'])->name('clubs');
        Route::get('/diary',       [ProfileController::class, 'diary'])->name('diary');
    });

    // report review
    Route::post('/reviews/{review}/report', [BookReviewController::class, 'report'])->name('reviews.report');
    // Main Profile Catch-All (Garis finish terakhir)
    Route::get('/{user:username}', [ProfileController::class, 'show'])->name('profile');

});
