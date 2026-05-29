<?php

namespace App\Http\Controllers;

use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookRequestController extends Controller
{

    /**
     * Menampilkan halaman form ajukan buku
     */
    public function create()
    {
        return view('book-request'); 
    }

    /**
     * Menyimpan data pengajuan buku baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi Inputan User
        $validated = $request->validate([
            'title'  => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn'   => 'nullable|string|max:20',
            'notes'  => 'nullable|string|max:1000',
        ], [
            'title.required'  => 'Judul buku wajib diisi.',
            'author.required' => 'Nama penulis wajib diisi.',
        ]);

        // 2. Masukkan data ke database dengan mengikat id user yang sedang login
        BookRequest::create([
            'user_id' => auth()->id(),
            'title'   => $validated['title'],
            'author'  => $validated['author'],
            'isbn'    => $validated['isbn'],
            'notes'   => $validated['notes'],
            'status'  => 'pending', // Default saat pertama kali diajukan
        ]);

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('books')
            ->with('success', 'Terima kasih! Pengajuan buku berhasil dikirim dan akan segera ditinjau oleh Admin.');
    }
}