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

        $filters = [
            'search' => $query,
            'genre' => $genre,
            'year' => $year,
            'sort' => $sort,
        ];

        return view('books', compact('books', 'popularBooks', 'trendingGenres', 'filters'));
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

        $stats = [
            'likes_count' => $book->likes()->count(),
            'ratings_count' => $book->ratings()->count(),
            'readers_count' => $book->readingLogs()->whereIn('status', ['reading', 'finished'])->distinct('user_id')->count(),
        ];

        return view('book-detail', compact('book', 'similarBooks', 'userLiked', 'userRating', 'userInReadlist', 'userReadingLog', 'userCollections', 'stats'));
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
        $book   = Book::findOrFail($id);
        $userId = Auth::id();

        $existing = BookLike::where('user_id', $userId)->where('book_id', $id)->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            BookLike::create(['user_id' => $userId, 'book_id' => $id]);
            $liked = true;
        }

        // Clear cache untuk popular books karena likes berubah
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

        $book = Book::findOrFail($id);

        BookRating::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $id],
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
        $book   = Book::findOrFail($id);
        $userId = Auth::id();

        $existing = BookReadlist::where('user_id', $userId)->where('book_id', $id)->first();

        if ($existing) {
            $existing->delete();
            $inReadlist = false;
        } else {
            BookReadlist::create(['user_id' => $userId, 'book_id' => $id]);
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
            'notes'       => 'nullable|string|max:1000',
        ]);

        Book::findOrFail($id);

        ReadingLog::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $id],
            [
                'status'      => $request->status,
                'started_at'  => $request->started_at,
                'finished_at' => $request->finished_at,
                'notes'       => $request->notes,
            ]
        );

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
}