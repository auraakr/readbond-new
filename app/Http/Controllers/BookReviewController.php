<?php

namespace App\Http\Controllers; // Pastikan namespace ini ada

use App\Models\BookReview;
use App\Models\ReviewReport;
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

    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            ReviewReport::create([
                'user_id' => auth()->id(),
                'book_review_id' => $id,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Review berhasil dilaporkan. Terima kasih atas laporan Anda.'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Jika user mencoba me-report lagi (terkena batasan UNIQUE di database)
            return response()->json([
                'success' => false, 
                'message' => 'Anda sudah melaporkan review ini sebelumnya.'
            ], 422);
        }
    }

}