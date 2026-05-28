<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewReport;
use App\Models\BookReview;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Menampilkan daftar laporan masuk
    public function index()
    {
        // Mengambil report berstatus pending beserta data relasi user pelapor dan reviewnya
        $reports = ReviewReport::with(['user', 'bookReview.user', 'bookReview.book'])
                                ->latest()
                                ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    // Aksi 1: Mengabaikan laporan (Dismiss) jika review dinilai aman
    public function dismiss($id)
    {
        $report = ReviewReport::findOrFail($id);
        $report->update(['status' => 'dismissed']);

        return redirect()->back()->with('success', 'Laporan berhasil diabaikan.');
    }

    // Aksi 2: Menghapus review karena melanggar aturan (Otomatis menghapus laporan terkait lewat cascade)
    public function destroyReview($id)
    {
        // Cari review berdasarkan ID yang dilaporkan
        $report = ReviewReport::findOrFail($id);
        $review = BookReview::findOrFail($report->book_review_id);
        
        $review->delete(); // Ini akan otomatis menghapus report terkait karena constraint cascade onDelete

        return redirect()->back()->with('success', 'Review yang melanggar berhasil dihapus.');
    }
}