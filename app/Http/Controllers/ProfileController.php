<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show($username)
    {
        // 1. Ambil data user beserta count relasinya
        $user = User::where('username', $username)
            // ->withCount(['following', 'followers', 'readingLogs as books_count'])
            ->withCount(['readingLogs as books_count'])
            ->firstOrFail();

        // 2. Ambil Buku Favorit (Asumsi ada kolom/relasi is_favorite di tabel books atau pivot)
        // $favoriteBooks = $user->books()
        //     ->wherePivot('is_favorite', true)
        //     ->limit(4)
        //     ->get();

        // 4. Review Terbaru
        $recentReviews = $user->readingLogs() // atau $user->reviews() tergantung nama relasimu
            ->with('book')
            ->latest()
            ->where('status', 'finished')
            ->limit(3)
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'review'; // TAMBAHKAN INI
                return $item;
            });

        // 5. Readlist (Buku yang ingin dibaca / status 'want_to_read')
        $readlist = $user->readingLists()
            ->with('book')
            ->latest()
            ->limit(4)
            ->get();

        $logs = $user->readingLogs()
            ->with('book')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->activity_type = 'log'; // Tandai tipe aktivitas
                return $item;
            });

        // 6. Distribusi Rating (untuk grafik batang)
        $ratingsDistribution = $user->bookRatings()
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->pluck('total', 'rating')
            ->all();
            
        // Isi default 0 jika rating tertentu tidak ada (1-5)
        $ratingsDistribution = array_replace([1=>0, 2=>0, 3=>0, 4=>0, 5=>0], $ratingsDistribution);

        // 3. Aktivitas Terbaru (Log membaca)
        $recentActivity = $logs->concat($recentReviews)
        ->sortByDesc('created_at')
        ->take(5);

        // 7. Hitung buku yang dibaca tahun ini
        $booksThisYear = $user->readingLogs()
            ->where('status', 'finished')
            ->whereYear('finished_at', now()->year)
            ->count();

        return view('profile', [
            'user' => $user,
            // 'favoriteBooks' => $favoriteBooks,
            'recentActivity' => $recentActivity,
            'recentReviews' => $recentReviews,
            'readlist' => $readlist,
            'ratingsDistribution' => $ratingsDistribution,
            'booksThisYear' => $booksThisYear,
            // 'following' => $user->following()->limit(10)->get(),
        ]);
    }
}