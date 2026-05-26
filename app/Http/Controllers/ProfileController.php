<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\BookClub;
use App\Models\BookRating;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show($username)
    {
        // 1. Ambil data user beserta count relasinya
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count'])
            ->firstOrFail();

        // 2. Ambil semua aktivitas dan gabungkan
        $recentActivity = $this->getRecentActivity($user);

        // 3. Review Terbaru (untuk section terpisah)
        $recentReviews = $user->readingLogs()
            ->with('book')
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
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
            'recentActivity' => $recentActivity,
            'recentReviews' => $recentReviews,
            'readlist' => $readlist,
            'ratingsDistribution' => $ratingsDistribution,
            'booksThisYear' => $booksThisYear,
        ]);
    }

    public function edit()
    {
        return view('user.profile-edit', [
            'user' => auth()->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|alpha_num|max:50|unique:users,username,' . $user->id,
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Batasi mimes agar aman
        ]);

        $data = $request->only(['name', 'username']);

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
            ->withCount(['readingLogs as books_count'])
            ->firstOrFail();

        // Ambil semua aktivitas (tanpa limit)
        $allActivity = $this->getAllRecentActivity($user);

        return view('user.profile-activity', [
            'user' => $user,
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

        return $activities
            ->concat($readingLogs)
            ->concat($likes)
            ->concat($ratings)
            ->concat($reviews)
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Tampilkan halaman books yang sudah finished
     */
    public function books($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count'])
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

        return view('user.profile-books', compact('user', 'finishedBooks'));
    }

    /**
     * Tampilkan halaman reviews lengkap
     */
    public function reviews($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count'])
            ->firstOrFail();

        // Ambil semua ulasan
        $allReviews = $this->getAllReviews($user);

        return view('user.profile-reviews', [
            'user' => $user,
            'allReviews' => $allReviews,
        ]);
    }

    public function getAllReviews($user)
    {
        return $user->readingLogs()
            ->with('book')
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->latest()
            ->get();
    }

    public function readlist($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count'])
            ->firstOrFail();

        // Ambil semua buku di readlist
        $readlistBooks = $user->readingLists()
            ->with('book')
            ->latest()
            ->paginate(12);

        return view('user.profile-readlist', [
            'user' => $user,
            'readlistBooks' => $readlistBooks,
        ]);
    }

    public function collections($username)
    {
        $user = User::where('username', $username)
            ->withCount(['readingLogs as books_count'])
            ->firstOrFail();

        // Ambil semua koleksi user
        $collections = $user->collections()
            ->with(['curator', 'books' => fn($q) => $q->limit(4)])
            ->withCount('books')
            ->latest()
            ->get();

        return view('user.profile-collections', [
            'user' => $user,
            'collections' => $collections,
        ]);
    }

    public function clubs($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        // Ambil semua club yang diikuti atau didirikan oleh user tersebut
        $clubs = $user->clubs() // Asumsi nama relasi BelongsToMany ke BookClub di model User adalah clubs()
                    ->withCount(['members', 'books'])
                    ->orderBy('book_club_members.created_at', 'desc')
                    ->get();

        return view('user.profile-clubs', compact('user', 'clubs'));
    }
}