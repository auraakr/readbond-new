<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\BookClub;
use App\Models\BookRating;
use App\Models\DiaryLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show($username)
    {
        // 1. Ambil data user beserta count relasinya
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->withCount(['readingLists as readlist_count'])
            ->firstOrFail();

        $user->avatar_url = !empty($user->avatar)
            ? (filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : asset('storage/' . $user->avatar))
            : null;

        // ── CEK STATUS FOLLOW (Apakah kita mem-follow user ini) ──
        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;
            
        // 2. Ambil semua aktivitas dan gabungkan
        $recentActivity = $this->getRecentActivity($user);

        // 3. Review Terbaru (untuk section terpisah)
        $recentReviews = $user->reviews()
            ->with('book')
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->latest()
            ->limit(3)
            ->get();

        // 4. Readlist (Buku yang ingin dibaca)
        $readlist = $user->readingLists()
            ->with('book')
            ->latest()
            ->limit(4)
            ->get();

        // 5. Distribusi Rating (untuk grafik batang)
        $ratingsDistribution = $user->bookRatings()
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->pluck('total', 'rating')
            ->all();
            
        $ratingsDistribution = array_replace([1=>0, 2=>0, 3=>0, 4=>0, 5=>0], $ratingsDistribution);

        // 6. Hitung buku yang dibaca tahun ini
        $booksThisYear = $user->readingLogs()
            ->where('status', 'finished')
            ->whereYear('finished_at', now()->year)
            ->count();

        return view('user.profile', [
            'user' => $user,
            'isFollowed' => $isFollowed,
            'recentActivity' => $recentActivity,
            'recentReviews' => $recentReviews,
            'readlist' => $readlist,
            'ratingsDistribution' => $ratingsDistribution,
            'booksThisYear' => $booksThisYear,
        ]);
    }

    public function edit()
    {
        $user = auth()->user();
    
        // Ambil maksimal 4 buku favorit milik pengguna
        $favoriteBooks = $user->favoriteBooks()->latest()->take(4)->get();

        $isFollowed = auth()->check() 
                ? $user->followers()->where('user_id', auth()->id())->exists() 
                : false;

        return view('user.profile-edit', compact('user', 'favoriteBooks'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|alpha_num|max:50|unique:users,username,' . $user->id,
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Batasi mimes agar aman
            'bio'      => 'nullable|string|max:1000',
        ]);

        $data = $request->only(['name', 'username', 'bio']);

        // Handling Avatar Image (Mengikuti pola update buku kamu)
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama di storage jika ada (Gunakan kolom 'avatar')
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan avatar baru ke folder 'avatars' di disk public
            // Hasilnya: "avatars/nama_file.jpg"
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            // Jika tidak upload baru, tetap pertahankan path yang lama
            $data['avatar'] = $user->avatar;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Gabungkan semua jenis aktivitas user
     */
    private function getRecentActivity($user)
    {
        $activities = collect();

        // 1. Reading Logs (status changes)
        $readingLogs = $user->readingLogs()
            ->with('book')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'reading_log',
                    'status' => $log->status,
                    'book' => $log->book,
                    'rating' => null,
                    'notes' => $log->notes,
                    'created_at' => $log->updated_at,
                    'data' => $log,
                ];
            });

        // 2. Likes
        $likes = $user->bookLikes()
            ->with('book')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($like) {
                return [
                    'type' => 'like',
                    'book' => $like->book,
                    'rating' => null,
                    'notes' => null,
                    'created_at' => $like->created_at,
                    'data' => $like,
                ];
            });

        // 3. Ratings
        $ratings = $user->bookRatings()
            ->with('book')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($rating) {
                return [
                    'type' => 'rating',
                    'book' => $rating->book,
                    'rating' => $rating->rating,
                    'notes' => null,
                    'created_at' => $rating->created_at,
                    'data' => $rating,
                ];
            });

        // 4. Reviews (reading logs with notes)
        $reviews = $user->readingLogs()
            ->with('book')
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'review',
                    'book' => $log->book,
                    'rating' => null,
                    'notes' => $log->notes,
                    'created_at' => $log->updated_at,
                    'data' => $log,
                ];
            });

        // ─── 5. NEW: Club Activity (User Bergabung ke Book Club) ───
        // Sesuaikan nama relasi 'clubs' jika di model User bernama 'bookClubs'
        $clubs = $user->clubs() 
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($club) {
                return [
                    'type' => 'club_join',
                    'book' => null,
                    'club' => $club, // Mengirim data club untuk dirender namanya/linknya di view
                    'rating' => null,
                    'notes' => null,
                    'created_at' => $club->pivot ? $club->pivot->created_at : $club->created_at,
                    'data' => $club,
                ];
            });

        // ─── 6. NEW: Diary Activity (User Menulis Catatan Harian) ───
        $diaries = $user->diaryLogs()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($diary) {
                return [
                    'type' => 'diary',
                    'book' => null,
                    'rating' => null,
                    'notes' => $diary->content ?? $diary->notes, // Sesuaikan nama kolom isi diary-mu
                    'created_at' => $diary->created_at,
                    'data' => $diary,
                ];
            });

        // Gabungkan semua dan sort by created_at secara Descending
        return $activities
            ->concat($readingLogs)
            ->concat($likes)
            ->concat($ratings)
            ->concat($reviews)
            ->concat($clubs)  
            ->concat($diaries) 
            ->sortByDesc('created_at')
            ->take(5) 
            ->values();
    }

    /**
     * Tampilkan halaman activity lengkap
     */
    public function activity($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        // Ambil semua aktivitas (tanpa limit)
        $allActivity = $this->getAllRecentActivity($user);

    $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-activity', [
            'user' => $user,
            'isFollowed' => $isFollowed,
            'allActivity' => $allActivity,
        ]);
    }

    /**
     * Ambil semua aktivitas user (untuk halaman activity full)
     */
    private function getAllRecentActivity($user)
    {
        $activities = collect();

        // 1. Reading Logs
        $readingLogs = $user->readingLogs()
            ->with('book')
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'reading_log',
                    'status' => $log->status,
                    'book' => $log->book,
                    'rating' => null,
                    'notes' => $log->notes,
                    'created_at' => $log->updated_at,
                    'data' => $log,
                ];
            });

        // 2. Likes
        $likes = $user->bookLikes()
            ->with('book')
            ->latest()
            ->get()
            ->map(function ($like) {
                return [
                    'type' => 'like',
                    'book' => $like->book,
                    'rating' => null,
                    'notes' => null,
                    'created_at' => $like->created_at,
                    'data' => $like,
                ];
            });

        // 3. Ratings
        $ratings = $user->bookRatings()
            ->with('book')
            ->latest()
            ->get()
            ->map(function ($rating) {
                return [
                    'type' => 'rating',
                    'book' => $rating->book,
                    'rating' => $rating->rating,
                    'notes' => null,
                    'created_at' => $rating->created_at,
                    'data' => $rating,
                ];
            });

        // 4. Reviews
        $reviews = $user->readingLogs()
            ->with('book')
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->latest()
            ->get()
            ->map(function ($log) {
                return [
                    'type' => 'review',
                    'book' => $log->book,
                    'rating' => null,
                    'notes' => $log->notes,
                    'created_at' => $log->updated_at,
                    'data' => $log,
                ];
            });

        $clubs = $user->clubs() 
            ->latest()
            ->get()
            ->map(function ($club) {
                return [
                    'type' => 'club_join',
                    'book' => null,
                    'club' => $club,
                    'rating' => null,
                    'notes' => null,
                    'created_at' => $club->pivot ? $club->pivot->created_at : $club->created_at,
                    'data' => $club,
                ];
            });

        $diaries = $user->diaryLogs()
            ->with('book')
            ->latest()
            ->get()
            ->map(function ($diary) {
                return [
                    'type' => 'diary',
                    'book' => $diary->book,
                    'rating' => null,
                    'notes' => $diary->content,
                    'created_at' => $diary->updated_at,
                    'data' => $diary,
                ];
            });

        return $activities
            ->concat($readingLogs)
            ->concat($likes)
            ->concat($ratings)
            ->concat($reviews)
            ->concat($clubs)  
            ->concat($diaries)
            ->sortByDesc('created_at')
            ->values();
    }

    public function diary(Request $request, $username)
    {
        // 1. Cari user berdasarkan username yang ada di URL
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Get diary logs untuk bulan ini
        $diaryQuery = $user->diaryLogs()
            ->with(['book:id,external_id,title,author_name,cover'])
            ->whereYear('read_date', $year)
            ->whereMonth('read_date', $month);

        $diaryLogs = $diaryQuery->orderBy('read_date', 'desc')->paginate(5);

        // Get all diary logs for calendar (hanya untuk bulan yang ditampilkan)
        $calendarEntries = $user->diaryLogs()
            ->with(['book:id,external_id,title,cover'])
            ->whereYear('read_date', $year)
            ->whereMonth('read_date', $month)
            ->get()
            ->groupBy(function($log) {
                return $log->read_date->format('Y-m-d');
            })
            ->map(function($logs) {
                // Ambil max 4 cover buku per hari
                return $logs->take(4)->map(function($log) {
                    return [
                        'book_id' => $log->book_id,
                        'cover' => $log->book->cover_url ?? null,
                        'title' => $log->book->title ?? 'Unknown',
                        'mood' => $log->mood,
                    ];
                });
            });

        // Calculate streak
        $streak = DiaryLog::calculateStreak($user->id);

        // Get stats for this year
        $stats = [
            'total_entries' => $user->diaryLogs()->thisYear()->count(),
            'total_books' => $user->diaryLogs()->thisYear()->distinct('book_id')->count(),
            'total_pages' => $user->diaryLogs()->thisYear()->sum('pages_read'),
            'this_month' => $user->diaryLogs()->thisMonth()->count(),
        ];

        // Get available years
        $availableYears = $user->diaryLogs()
            ->selectRaw('DISTINCT YEAR(read_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('diary.index', [
            'user' => $user,
            'isFollowed' => $isFollowed,
            'diaryLogs' => $diaryLogs,
            'calendarEntries' => $calendarEntries,
            'streak' => $streak,
            'stats' => $stats,
            'currentYear' => $year,
            'currentMonth' => $month,
            'availableYears' => $availableYears,
        ]);
    }

    /**
     * Tampilkan halaman books yang sudah finished
     */
    public function books($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        // CARA TERBAIK: Ambil buku yang selesai + rating-nya sekaligus menggunakan relasi
        $finishedBooks = $user->readingLogs()
            ->where('status', 'finished')
            ->with(['book.ratings' => function($query) use ($user) {
                // Hanya ambil rating milik user ini untuk buku tersebut
                $query->where('user_id', $user->id);
            }])
            ->latest('finished_at')
            ->paginate(12);

        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-books', compact('user', 'finishedBooks', 'isFollowed'));
    }

    /**
     * Tampilkan halaman reviews lengkap
     */
    public function reviews($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        // Ambil semua ulasan
        $allReviews = $this->getAllReviews($user);

    $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-reviews', [
            'user' => $user,
            'isFollowed' => $isFollowed,
            'allReviews' => $allReviews,
        ]);
    }

    public function getAllReviews($user)
    {
        return $user->reviews()
            ->with('book')
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->latest()
            ->get();
    }

    public function readlist($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        // Ambil semua buku di readlist
        $readlistBooks = $user->readingLists()
            ->with('book')
            ->latest()
            ->paginate(12);

        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-readlist', [
            'user' => $user,
            'isFollowed' => $isFollowed,
            'readlistBooks' => $readlistBooks,
        ]);
    }

    public function collections($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        // Ambil semua koleksi user
        $collections = $user->collections()
            ->with(['curator', 'books' => fn($q) => $q->limit(4)])
            ->withCount('books')
            ->latest()
            ->get();

        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-collections', [
            'user' => $user,
            'isFollowed' => $isFollowed,
            'collections' => $collections,
        ]);
    }

    public function clubs($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        // Ambil semua club yang diikuti atau didirikan oleh user tersebut
        $clubs = $user->clubs() // Asumsi nama relasi BelongsToMany ke BookClub di model User adalah clubs()
                    ->withCount(['members', 'books'])
                    ->orderBy('book_club_members.created_at', 'desc')
                    ->get();

        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-clubs', compact('user', 'clubs', 'isFollowed'));
    }

    public function addFavoriteBook(Request $request)
    {
        $request->validate([
            'book_id'     => 'nullable|integer',
            'external_id' => 'nullable|string',
            'position'    => 'required|integer|min:0|max:3',
        ]);

        $bookId = $request->book_id;

        // Resolve from external_id if no local book_id provided
        if (!$bookId && $request->external_id) {
            $book = Book::where('external_id', $request->external_id)->first();

            if (!$book) {
                try {
                    $data = Http::timeout(15)
                        ->get("https://openlibrary.org/works/{$request->external_id}.json")
                        ->json();

                    $title = $data['title'] ?? 'Unknown';
                    $coverId = $data['covers'][0] ?? null;

                    $authorName = 'Unknown';
                    if (!empty($data['authors'][0]['author']['key'])) {
                        $authorKey = $data['authors'][0]['author']['key'];
                        $authorData = Http::timeout(5)->get("https://openlibrary.org{$authorKey}.json")->json();
                        $authorName = $authorData['name'] ?? 'Unknown';
                    }

                    $book = Book::create([
                        'external_id' => $request->external_id,
                        'title'       => $title,
                        'author_name' => $authorName,
                        'cover'   => $coverId
                            ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg"
                            : null,
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Gagal mengambil data buku dari Open Library.'], 422);
                }
            }

            $bookId = $book->id;
        }

        if (!$bookId || !Book::find($bookId)) {
            return response()->json(['success' => false, 'message' => 'Buku tidak ditemukan.'], 422);
        }

        $user = auth()->user();
        $user->favoriteBooks()->wherePivot('order_position', $request->position)->detach();
        $user->favoriteBooks()->attach($bookId, ['order_position' => $request->position]);

        return response()->json(['success' => true]);
    }

    public function removeFavoriteBook($id)
    {
        $user = auth()->user();
        $user->favoriteBooks()->detach($id);

        return response()->json(['success' => true]);
    }

    public function networks($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();

        $followers = $user->followers()->latest()->get();
        $following = $user->following()->latest()->get();

        $isFollowed = auth()->check() 
            ? $user->followers()->where('user_id', auth()->id())->exists() 
            : false;

        return view('user.profile-networks', compact('user', 'followers', 'following', 'isFollowed'));
    }  
}