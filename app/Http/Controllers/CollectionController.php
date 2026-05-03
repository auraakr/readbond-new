<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionComment;
use App\Models\CollectionLike;
use App\Models\CollectionCommentLike;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CollectionController extends Controller
{
    // ────────────────────────────────────────────
    // INDEX — ambil dari database
    // ────────────────────────────────────────────
    public function index(Request $request)
    {
        $search = $request->input('search');
        $genre  = $request->input('genre');

        $featured = Collection::where('is_featured', true)
            ->with(['curator', 'books' => fn($q) => $q->limit(4)])
            ->withCount('books')
            ->latest()
            ->take(3)
            ->get();

        $popular = Collection::with(['curator', 'books' => fn($q) => $q->limit(4)])
            ->withCount(['books', 'comments'])
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderByDesc('likes_count')
            ->paginate(12);

        return view('collections.index', compact('featured', 'popular', 'search', 'genre'));
    }

    // ────────────────────────────────────────────
    // SHOW
    // ────────────────────────────────────────────
    public function show(string $id)
    {
        $collection = Collection::with([
            'curator',
            'books',
            'comments.author',
            'comments.likes',
        ])->withCount(['books', 'comments', 'likes'])->findOrFail($id);

        $isLiked = Auth::check() && $collection->isLikedBy(Auth::user());

        return view('collections.show', compact('collection', 'isLiked'));
    }

    // ────────────────────────────────────────────
    // CREATE
    // ────────────────────────────────────────────
    public function create()
    {
        return view('collections.create');
    }

    // ────────────────────────────────────────────
    // STORE — simpan koleksi + auto-save buku dari API
    // ────────────────────────────────────────────
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string|max:1000',
            'book_external_ids'   => 'nullable|array',
            'book_external_ids.*' => 'string',
        ]);

        $collection = Collection::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);

        if (!empty($validated['book_external_ids'])) {
            $syncData = [];

            foreach ($validated['book_external_ids'] as $index => $externalId) {
                $book = $this->findOrFetchBook($externalId);
                if ($book) {
                    $syncData[$book->id] = ['order' => $index];
                }
            }

            $collection->books()->sync($syncData);
        }

        return redirect()->route('collections.show', $collection->id)
                         ->with('success', 'Koleksi berhasil dibuat!');
    }

    // ────────────────────────────────────────────
    // Helper: cari di DB, kalau tidak ada fetch dari API lalu simpan
    // ────────────────────────────────────────────
    private function findOrFetchBook(string $externalId): ?Book
    {
        $book = Book::where('external_id', $externalId)->first();
        if ($book) return $book;

        try {
            $response = Http::timeout(5)
                ->get("https://openlibrary.org/works/{$externalId}.json")
                ->json();

            if (empty($response['title'])) return null;

            $searchResponse = Http::timeout(5)
                ->get('https://openlibrary.org/search.json', [
                    'q'      => "key:/works/{$externalId}",
                    'fields' => 'first_publish_year,subject',
                    'limit'  => 1,
                ])->json();

            $year    = $searchResponse['docs'][0]['first_publish_year'] ?? 0;
            $subject = array_slice($searchResponse['docs'][0]['subject'] ?? [], 0, 10);

            $description = is_array($response['description'] ?? null)
                ? $response['description']['value']
                : ($response['description'] ?? 'No description available.');

            $authorName = 'Unknown Author';
            $authorKey  = $response['authors'][0]['author']['key'] ?? null;
            if ($authorKey) {
                $authorResponse = Http::timeout(5)
                    ->get("https://openlibrary.org{$authorKey}.json")
                    ->json();
                $authorName = $authorResponse['name'] ?? 'Unknown Author';
            }

            return Book::create([
                'external_id'   => $externalId,
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
            \Log::warning("Gagal fetch buku {$externalId}: " . $e->getMessage());
            return null;
        }
    }

    // ────────────────────────────────────────────
    // DESTROY
    // ────────────────────────────────────────────
    public function destroy(string $id)
    {
        $collection = Collection::findOrFail($id);
        if ($collection->user_id !== Auth::id()) abort(403);
        $collection->delete();

        return redirect()->route('collections.index')
                         ->with('success', 'Koleksi berhasil dihapus.');
    }

    // ────────────────────────────────────────────
    // TOGGLE LIKE koleksi
    // ────────────────────────────────────────────
    public function toggleLike(string $id)
    {
        $collection = Collection::findOrFail($id);
        $userId     = Auth::id();

        $existing = CollectionLike::where('collection_id', $id)
                                   ->where('user_id', $userId)->first();
        if ($existing) {
            $existing->delete();
            $collection->decrement('likes_count');
            $liked = false;
        } else {
            CollectionLike::create(['collection_id' => $id, 'user_id' => $userId]);
            $collection->increment('likes_count');
            $liked = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['liked' => $liked, 'count' => $collection->fresh()->likes_count]);
        }

        return back();
    }

    // ────────────────────────────────────────────
    // STORE COMMENT
    // ────────────────────────────────────────────
    public function storeComment(Request $request, string $id)
    {
        $validated = $request->validate([
            'body'   => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        CollectionComment::create([
            'collection_id' => $id,
            'user_id'       => Auth::id(),
            'body'          => $validated['body'],
            'rating'        => $validated['rating'] ?? null,
        ]);

        return back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    // ────────────────────────────────────────────
    // DELETE COMMENT
    // ────────────────────────────────────────────
    public function destroyComment(string $commentId)
    {
        $comment = CollectionComment::findOrFail($commentId);
        if ($comment->user_id !== Auth::id()) abort(403);
        $comment->delete();

        return back()->with('success', 'Komentar dihapus.');
    }

    // ────────────────────────────────────────────
    // TOGGLE LIKE COMMENT
    // ────────────────────────────────────────────
    public function toggleCommentLike(string $commentId)
    {
        $comment  = CollectionComment::findOrFail($commentId);
        $userId   = Auth::id();

        $existing = CollectionCommentLike::where('collection_comment_id', $commentId)
                                          ->where('user_id', $userId)->first();
        if ($existing) {
            $existing->delete();
            $comment->decrement('likes_count');
            $liked = false;
        } else {
            CollectionCommentLike::create([
                'collection_comment_id' => $commentId,
                'user_id'               => $userId,
            ]);
            $comment->increment('likes_count');
            $liked = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['liked' => $liked, 'count' => $comment->fresh()->likes_count]);
        }

        return back();
    }

    // ────────────────────────────────────────────
    // ADD BOOK ke koleksi
    // ────────────────────────────────────────────
    public function addBook(Request $request, string $id)
    {
        $collection = Collection::findOrFail($id);
        if ($collection->user_id !== Auth::id()) abort(403);

        $request->validate(['external_id' => 'required|string']);

        $book = $this->findOrFetchBook($request->external_id);
        if (!$book) return back()->with('error', 'Buku tidak ditemukan.');

        $maxOrder = $collection->books()->max('collection_book.order') ?? -1;
        $collection->books()->syncWithoutDetaching([
            $book->id => ['order' => $maxOrder + 1],
        ]);

        return back()->with('success', 'Buku ditambahkan ke koleksi.');
    }

    // ────────────────────────────────────────────
    // REMOVE BOOK dari koleksi
    // ────────────────────────────────────────────
    public function removeBook(Request $request, string $id)
    {
        $collection = Collection::findOrFail($id);
        if ($collection->user_id !== Auth::id()) abort(403);

        $request->validate(['book_id' => 'required|exists:books,id']);
        $collection->books()->detach($request->book_id);

        return back()->with('success', 'Buku dihapus dari koleksi.');
    }
}