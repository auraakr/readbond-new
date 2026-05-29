<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookRequest;

class BookRequestController extends Controller
{
    public function index()
    {
        $requests = BookRequest::with('user')->latest()->paginate(10);
        return view('admin.book-requests.index', compact('requests'));
    }

    /**
     * Menolak pengajuan buku
     */
    public function reject($id)
    {
        $bookRequest = BookRequest::findOrFail($id);
        $bookRequest->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Pengajuan buku berhasil ditolak.');
    }
}
