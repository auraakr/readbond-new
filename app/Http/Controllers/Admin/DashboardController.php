<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\ReviewReport;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Menghitung data statistik utama secara dinamis
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalReports = ReviewReport::where('status', 'pending')->count(); // Fokus ke laporan aktif
        $activeReviewsCount = BookReview::count();

        // 2. Mengambil 5 aktivitas komunitas terbaru (Review & Laporan baru)
        // Kita bisa menggabungkan atau mengambil review terbaru sebagai log aktivitas utama
        $recentActivities = BookReview::with(['user', 'book'])
                                      ->latest()
                                      ->take(5)
                                      ->get();

        // 3. Simulasi user aktif (opsional, atau bisa dihitung dari kolom last_seen sejenis)
        $activeUsersNow = User::where('updated_at', '>=', now()->subMinutes(5))->count() ?: 12;

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalBooks', 
            'totalReports', 
            'activeReviewsCount',
            'recentActivities',
            'activeUsersNow'
        ));
    }
}