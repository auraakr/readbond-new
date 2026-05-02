<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionComment;
use App\Models\CollectionLike;
use App\Models\CollectionCommentLike;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    // ────────────────────────────────────────────
    // INDEX — Halaman daftar koleksi
    // ────────────────────────────────────────────
    public function index(Request $request)
    {
        $genre = $request->input('genre');
        $search = $request->input('search');

        // Featured collections (kurated oleh admin / is_featured = true)
        $featured = Collection::where('is_featured', true)
                               ->with(['curator', 'books' => fn($q) => $q->limit(4)])
                               ->withCount('books')
                               ->latest()
                               ->take(3)
                               ->get();

        // Popular collections — diurutkan berdasarkan likes
        $popular = Collection::with(['curator', 'books' => fn($q) => $q->limit(4)])
                              ->withCount(['books', 'comments'])
                              ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                              ->orderByDesc('likes_count')
                              ->paginate(12);

        return view('collections.index', compact('featured', 'popular', 'search', 'genre'));
    }

    // ────────────────────────────────────────────
    // SHOW — Detail satu koleksi
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
    // CREATE — Form buat koleksi baru
    // ────────────────────────────────────────────
    public function create()
    {
        $this->middleware('auth');
        return view('collections.create');
    }

    // ────────────────────────────────────────────
    // STORE — Simpan koleksi baru
    // ────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'book_titles'   => 'nullable|array',
            'book_titles.*' => 'string|max:255',
        ]);
    
        $collection = Collection::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
        ]);
    
        // Cari buku di DB berdasarkan judul, lalu attach ke koleksi
        if (!empty($validated['book_titles'])) {
            $books = Book::whereIn('title', $validated['book_titles'])->get();
    
            $syncData = $books->mapWithKeys(fn($book, $index) => [
                $book->id => ['order' => $index]
            ])->toArray();
    
            $collection->books()->sync($syncData);
        }
    
        return redirect()->route('collections.show', $collection->id)
                        ->with('success', 'Koleksi berhasil dibuat!');
    }

    // ────────────────────────────────────────────
    // DESTROY — Hapus koleksi
    // ────────────────────────────────────────────
    public function destroy(string $id)
    {
        $collection = Collection::findOrFail($id);

        if ($collection->user_id !== Auth::id()) {
            abort(403, 'Kamu tidak punya akses untuk menghapus koleksi ini.');
        }

        $collection->delete();

        return redirect()->route('collections.index')
                         ->with('success', 'Koleksi berhasil dihapus.');
    }

    // ────────────────────────────────────────────
    // LIKE / UNLIKE koleksi — toggle
    // ────────────────────────────────────────────
    public function toggleLike(string $id)
    {
        $this->middleware('auth');

        $collection = Collection::findOrFail($id);
        $userId     = Auth::id();

        $existing = CollectionLike::where('collection_id', $id)
                                   ->where('user_id', $userId)
                                   ->first();

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
    // STORE COMMENT — Tambah komentar
    // ────────────────────────────────────────────
    public function storeComment(Request $request, string $id)
    {
        $this->middleware('auth');

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
    // DELETE COMMENT — Hapus komentar
    // ────────────────────────────────────────────
    public function destroyComment(string $commentId)
    {
        $comment = CollectionComment::findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Komentar dihapus.');
    }

    // ────────────────────────────────────────────
    // LIKE COMMENT — Toggle like komentar
    // ────────────────────────────────────────────
    public function toggleCommentLike(string $commentId)
    {
        $this->middleware('auth');

        $comment  = CollectionComment::findOrFail($commentId);
        $userId   = Auth::id();

        $existing = CollectionCommentLike::where('collection_comment_id', $commentId)
                                          ->where('user_id', $userId)
                                          ->first();

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
    // ADD BOOK — Tambah buku ke koleksi
    // ────────────────────────────────────────────
    public function addBook(Request $request, string $id)
    {
        $collection = Collection::findOrFail($id);

        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['book_id' => 'required|exists:books,id']);

        $maxOrder = $collection->books()->max('collection_book.order') ?? -1;

        // Ignore jika sudah ada
        $collection->books()->syncWithoutDetaching([
            $request->book_id => ['order' => $maxOrder + 1],
        ]);

        return back()->with('success', 'Buku ditambahkan ke koleksi.');
    }

    // ────────────────────────────────────────────
    // REMOVE BOOK — Hapus buku dari koleksi
    // ────────────────────────────────────────────
    public function removeBook(Request $request, string $id)
    {
        $collection = Collection::findOrFail($id);

        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate(['book_id' => 'required|exists:books,id']);

        $collection->books()->detach($request->book_id);

        return back()->with('success', 'Buku dihapus dari koleksi.');
    }
}