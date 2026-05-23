<?php

namespace App\Http\Controllers; // Pastikan namespace ini ada

use App\Models\BookReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth; // Tambahkan ini agar Auth:: terdeteksi

class BookReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menyimpan list reviews selama 60 detik (1 menit)
        $reviews = Cache::remember('book_reviews', 60, function () {
            // Menggunakan withCount('likes') agar total like langsung ikut ter-load secara efisien
            return BookReview::with(['user', 'book'])->withCount('likes')->get();
        });

        return response()->json($reviews);
    }

}