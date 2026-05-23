<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\BookLike;
use App\Models\BookRating;
use App\Models\BookReadlist;
use App\Models\ReadingLog;
use App\Models\BookReview;
use App\Models\BookReviewLike;

class BooksController extends Controller
{
    /**
     * Display books - menggunakan database, bukan API
     */
    public function index(Request $request)
    {
        $query = $request->input('search');
        $genre = $request->input('genre');
        $year  = $request->input('year');
        $sort  = $request->input('sort', 'popular'); // popular, recent, top_rated

        // Query builder - hanya ambil buku dengan external_id (valid books)
        $booksQuery = Book::query()->whereNotNull('external_id');

        // Filter search
        if ($query) {
            $booksQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author_name', 'like', "%{$query}%");
            });
        }

        // Filter genre/subject
        if ($genre) {
            $booksQuery->whereJsonContains('subject', $genre);
        }

        // Filter year
        if ($year) {
            $booksQuery->where('year', $year);
        }

        // Sorting
        switch ($sort) {
            case 'popular':
                // Sort by likes count (most liked first)
                $booksQuery->withCount('likes')
                           ->orderBy('likes_count', 'desc');
                break;
            
            case 'top_rated':
                // Sort by average rating
                $booksQuery->orderBy('averageRating', 'desc');
                break;
            
            case 'recent':
                // Sort by newest added to database
                $booksQuery->latest();
                break;
            
            case 'year_desc':
                // Sort by publication year (newest first)
                $booksQuery->orderBy('year', 'desc');
                break;
        }

        // Paginate results
        $books = $booksQuery->paginate(12);

        // Get popular books (most liked in last 30 days)
        $popularBooks = $this->getPopularBooks();

        // Get trending genres
        $trendingGenres = $this->getTrendingGenres();

        // Get recent reviews
        $recentReviews = $this->getRecentReviews();

        // get popular reviews
        $popularReviews = $this->getPopularReviews();

        $filters = [
            'search' => $query,
            'genre' => $genre,
            'year' => $year,
            'sort' => $sort,
        ];

        return view('books', compact('books', 'popularBooks', 'trendingGenres', 'recentReviews', 'popularReviews', 'filters'));
    }

    /**
     * Get popular books based on likes count
     */
    private function getPopularBooks($limit = 12)
    {
        return Cache::remember('popular_books_homepage', 3600, function () use ($limit) {
            return Book::whereNotNull('external_id')
                ->withCount(['likes' => function ($query) {
                    // Count likes from last 30 days untuk trending
                    $query->where('created_at', '>=', now()->subDays(30));
                }])
                ->having('likes_count', '>', 0)
                ->orderBy('likes_count', 'desc')
                ->orderBy('averageRating', 'desc') // Secondary sort by rating
                ->limit($limit)
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'external_id' => $book->external_id,
                        'title' => $book->title,
                        'author_name' => $book->author_name,
                        'year' => $book->year,
                        'cover' => $book->cover_url,
                        'averageRating' => $book->averageRating,
                        'likes_count' => $book->likes_count,
                        'url' => route('books.show', $book->external_id),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get trending genres from popular books
     */
    private function getTrendingGenres($limit = 10)
    {
        return Cache::remember('trending_genres', 3600, function () use ($limit) {
            // Get all subjects from popular books
            $subjects = Book::whereNotNull('external_id')
                ->withCount('likes')
                ->having('likes_count', '>', 0)
                ->orderBy('likes_count', 'desc')
                ->limit(100)
                ->pluck('subject')
                ->flatten()
                ->filter()
                ->countBy()
                ->sortDesc()
                ->take($limit)
                ->keys()
                ->toArray();

            return $subjects;
        });
    }

    /**
     * Get recent reviews with book and user info
     */
    private function getRecentReviews($limit = 6)
    {
        return Cache::remember('recent_reviews', 1800, function () use ($limit) {
            return BookReview::with(['book', 'user'])
                ->where('rating', '>', 0)
                ->withCount('likes') // Menghitung total relasi 'likes' pada model BookReview
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user->name,
                        'rating' => $review->rating,
                        'review' => $review->review,
                        'is_liked' => $review->is_liked,
                        'likes_count' => $review->likes_count,
                        'created_at' => $review->created_at->diffForHumans(),
                        'book_title' => $review->book->title,
                        'book_cover' => $review->book->cover,
                        'book_url' => route('books.show', $review->book->external_id),
                    ];
                })
                ->toArray();
        });
    }

    private function getPopularReviews($limit = 6)
    {
        // Menggunakan cache selama 30 menit (1800 detik) agar tidak membebani database
        return Cache::remember('popular_reviews', 1800, function () use ($limit) {
            return BookReview::with(['book', 'user'])
                ->where('rating', '>', 0)
                ->withCount('likes') // Menghitung total relasi 'likes' pada model BookReview
                ->orderBy('likes_count', 'desc') // Urutkan dari yang paling banyak di-like
                ->limit($limit)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'user_name' => $review->user->name,
                        'rating' => $review->rating,
                        'review' => $review->review,
                        'likes_count' => $review->likes_count, // Menyimpan total jumlah likes
                        'created_at' => $review->created_at->diffForHumans(),
                        'book_title' => $review->book->title,
                        'book_cover' => $review->book->cover,
                        'book_url' => route('books.show', $review->book->external_id),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Show book detail
     */
    public function show($external_id)
    {
        $book = Book::where('external_id', $external_id)->first();

        // Jika buku belum ada di database, fetch dari API
        if (!$book) {
            $book = $this->fetchAndCreateBook($external_id);
            
            if (!$book) {
                abort(404, 'Book not found');
            }
        }

        // Update view count (optional)
        $book->increment('view_count');

        // Status interaksi user yang sedang login
        $userLiked      = false;
        $userRating     = null;
        $userInReadlist = false;
        $userReadingLog = null;
        $userCollections = collect();
        $currentUserId  = Auth::id(); // Ambil ID User yang sedang login

        if (Auth::check()) {
            $user           = Auth::user();
            $userLiked      = $book->isLikedBy($user);
            $userRating     = $book->ratingBy($user);
            $userInReadlist = $book->isInReadlistOf($user);
            $userReadingLog = $book->readingLogFor($user);
            $userCollections = $user->collections ?? collect();
        }

        // Similar books (based on genre/subject)
        $similarBooks = $this->getSimilarBooks($book, 6);

        // ── PERBAIKAN DI SINI ──
        // Mengambil reviews beserta hitungan jumlah likes & status like user yang login
        $reviews = $book->reviews()
            ->with('user')
            ->where('rating', '>', 0)
            ->withCount('likes') // Menghitung otomatis kolom 'likes_count'
            ->withExists(['likes' => function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId); // Mengecek kolom 'likes_exists' (true/false)
            }])
            ->latest()
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user->name,
                    'user_avatar' => $review->user->avatar,
                    'rating' => $review->rating,
                    'review' => $review->review,
                    
                    // Masukkan hasil kueri ke dalam array pembungkus
                    'is_liked' => $review->likes_exists, // Bernilai true jika user login sudah nge-like
                    'likes_count' => $review->likes_count, // Bernilai angka riil jumlah like dari DB
                    
                    'created_at' => $review->created_at->diffForHumans(),
                ];
            });

        $stats = [
            'likes_count' => $book->likes()->count(),
            'ratings_count' => $book->ratings()->count(),
            'readers_count' => $book->readingLogs()->whereIn('status', ['reading', 'finished'])->distinct('user_id')->count(),
            'reviews_count' => $reviews->count(),
        ];

        return view('book-detail', compact('book', 'similarBooks', 'reviews', 'userLiked', 'userRating', 'userInReadlist', 'userReadingLog', 'userCollections', 'stats'));
    }

    /**
     * Fetch book from API and create in database
     */
    private function fetchAndCreateBook($external_id)
    {
        try {
            $response = Http::timeout(10)->get("https://openlibrary.org/works/{$external_id}.json")->json();

            if (!$response) {
                return null;
            }

            $searchResponse = Http::timeout(10)->get('https://openlibrary.org/search.json', [
                'q'      => "key:/works/{$external_id}",
                'fields' => 'first_publish_year,subject',
                'limit'  => 1,
            ])->json();

            $year        = $searchResponse['docs'][0]['first_publish_year'] ?? 0;
            $subject     = array_slice($searchResponse['docs'][0]['subject'] ?? [], 0, 10);
            $description = is_array($response['description'] ?? null)
                            ? $response['description']['value']
                            : ($response['description'] ?? 'No description available.');

            $authorName = 'Unknown Author';
            $authorKey  = $response['authors'][0]['author']['key'] ?? null;
            if ($authorKey) {
                $authorResponse = Http::timeout(10)->get("https://openlibrary.org{$authorKey}.json")->json();
                $authorName = $authorResponse['name'] ?? 'Unknown Author';
            }

            return Book::create([
                'external_id'   => $external_id,
                'title'         => $response['title'],
                'desc'          => $description,
                'year'          => (int) $year,
                'pageCount'     => $response['number_of_pages'] ?? 0,
                'cover'         => isset($response['covers'])
                                    ? "https://covers.openlibrary.org/b/id/{$response['covers'][0]}-L.jpg"
                                    : null,
                'author_name'   => $authorName,
                'subject'       => $subject,
                'averageRating' => 0,
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to fetch book from OpenLibrary: {$external_id}", [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get similar books based on genre/subject
     */
    private function getSimilarBooks(Book $book, $limit = 6)
    {
        if (empty($book->subject)) {
            return Book::where('id', '!=', $book->id)
                ->whereNotNull('external_id')
                ->withCount('likes')
                ->orderBy('likes_count', 'desc')
                ->limit($limit)
                ->get();
        }

        // Find books with matching subjects
        return Book::where('id', '!=', $book->id)
            ->whereNotNull('external_id')
            ->where(function ($query) use ($book) {
                foreach ($book->subject as $subject) {
                    $query->orWhereJsonContains('subject', $subject);
                }
            })
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Autocomplete search - gunakan database dulu, fallback ke API
     */
    public function autocomplete(Request $request)
    {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) {
            return response()->json([]);
        }

        // Search di database terlebih dahulu
        $dbResults = Book::whereNotNull('external_id')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                      ->orWhere('author_name', 'like', "%{$q}%");
            })
            ->limit(6)
            ->get()
            ->map(fn($book) => [
                'id'          => $book->id,
                'external_id' => $book->external_id,
                'title'       => $book->title,
                'author'      => $book->author_name,
                'cover'       => $book->cover_url,
                'url'         => route('books.show', $book->external_id),
                'source'      => 'database',
            ]);

        // Jika hasil dari database kurang dari 6, tambahkan dari API
        if ($dbResults->count() < 6) {
            try {
                $apiResults = Http::timeout(5)->get('https://openlibrary.org/search.json', [
                    'q'      => $q,
                    'fields' => 'key,title,author_name,cover_i',
                    'limit'  => 6 - $dbResults->count(),
                ])->json()['docs'] ?? [];

                $apiMapped = collect($apiResults)->map(fn($b) => [
                    'external_id' => str_replace('/works/', '', $b['key']),
                    'title'       => $b['title'],
                    'author'      => $b['author_name'][0] ?? 'Unknown',
                    'cover'       => isset($b['cover_i'])
                                     ? "https://covers.openlibrary.org/b/id/{$b['cover_i']}-S.jpg"
                                     : null,
                    'url'         => route('books.show', str_replace('/works/', '', $b['key'])),
                    'source'      => 'api',
                ]);

                $dbResults = $dbResults->concat($apiMapped);
            } catch (\Exception $e) {
                // Jika API error, hanya return hasil dari database
            }
        }

        return response()->json($dbResults->take(6));
    }

    // ── Toggle Like ──
    public function toggleLike(Request $request, $id)
    {
        $userId = Auth::id();

        // Pastikan user sudah login
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Handle numeric ID maupun external_id
        $book = null;
        if (is_numeric($id)) {
            $book = Book::findOrFail($id);
        } else {
            $book = Book::where('external_id', $id)->firstOrFail();
        }

        // Cari data like menggunakan ID internal database yang valid
        $existing = BookLike::where('user_id', $userId)->where('book_id', $book->id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            BookLike::create(['user_id' => $userId, 'book_id' => $book->id]);
            $liked = true;
        }

        // Clear cache untuk popular books karena jumlah likes berubah
        Cache::forget('popular_books_homepage');

        if ($request->wantsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $book->likes()->count(),
            ]);
        }

        return back();
    }

    // ── Rate buku ──
    public function rate(Request $request, $id)
    {
        $request->validate(['rating' => 'required|integer|min:1|max:5']);

        // Handle numeric ID maupun external_id
        $book = is_numeric($id) ? Book::findOrFail($id) : Book::where('external_id', $id)->firstOrFail();

        BookRating::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            ['rating'  => $request->rating]
        );

        // Update averageRating di tabel books
        $book->recalculateRating();

        if ($request->wantsJson()) {
            return response()->json([
                'rating'        => $request->rating,
                'averageRating' => $book->fresh()->averageRating,
                'ratings_count' => $book->ratings()->count(),
            ]);
        }

        return back()->with('success', 'Rating berhasil disimpan!');
    }

    // ── Toggle Readlist ──
    public function toggleReadlist(Request $request, $id)
    {
        $userId = Auth::id();
        
        // Handle numeric ID maupun external_id
        $book = is_numeric($id) ? Book::findOrFail($id) : Book::where('external_id', $id)->firstOrFail();

        $existing = BookReadlist::where('user_id', $userId)->where('book_id', $book->id)->first();

        if ($existing) {
            $existing->delete();
            $inReadlist = false;
        } else {
            BookReadlist::create(['user_id' => $userId, 'book_id' => $book->id]);
            $inReadlist = true;
        }

        if ($request->wantsJson()) {
            return response()->json(['inReadlist' => $inReadlist]);
        }

        return back();
    }

    // ── Simpan Reading Log ──
    public function storeReadingLog(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:want_to_read,reading,finished',
            'started_at'  => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'rating'      => 'nullable|integer|min:1|max:5',
            'review'      => 'nullable|string|max:5000',
            'is_liked'    => 'nullable|boolean',
        ]);

        // Handle both numeric ID and external_id
        $book = null;
        if (is_numeric($id)) {
            $book = Book::findOrFail($id);
        } else {
            $book = Book::where('external_id', $id)->firstOrFail();
        }

        ReadingLog::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->id],
            [
                'status'      => $request->status,
                'started_at'  => $request->started_at,
                'finished_at' => $request->finished_at,
            ]
        );

        // Save review if status is finished and has rating
        if ($request->status === 'finished' && $request->rating) {
            BookReview::updateOrCreate(
                ['user_id' => Auth::id(), 'book_id' => $book->id],
                [
                    'rating'   => $request->rating,
                    'review'   => $request->review,
                    'is_liked' => $request->boolean('is_liked', false),
                ]
            );
            
            // --- PERBAIKAN: Hapus duplikasi pembersihan cache di sini ---
            Cache::forget('recent_reviews');
            Cache::forget('popular_reviews');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $request->status]);
        }

        return back()->with('success', 'Reading log berhasil disimpan!');
    }

    // ── Tambah ke koleksi ──
    public function addToCollection(Request $request, $id)
    {
        $request->validate(['collection_id' => 'required|exists:collections,id']);

        $book       = Book::findOrFail($id);
        $collection = Auth::user()->collections()->findOrFail($request->collection_id);
        $maxOrder   = $collection->books()->max('collection_book.order') ?? -1;

        $collection->books()->syncWithoutDetaching([
            $book->id => ['order' => $maxOrder + 1],
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', "Buku ditambahkan ke koleksi \"{$collection->title}\"!");
    }

    public function toggleLikeReview(Request $request, $id)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Mencari review berdasarkan ID
        $review = BookReview::findOrFail($id);

        // Memeriksa apakah user sudah pernah nge-like review ini
        // (Asumsi: model BookReview memiliki relasi bernama 'likes')
        $existing = $review->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $review->likes()->detach($userId);
            $isLiked = false;
        } else {
            $review->likes()->attach($userId);
            $isLiked = true;
        }

        // Bersihkan cache yang menampung data review agar langsung ter-update
        Cache::forget('book_reviews');
        Cache::forget('recent_reviews');
        Cache::forget('popular_reviews');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'likes_count' => $review->likes()->count(),
            ]);
        }

        return back();
    }
}