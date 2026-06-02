<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Menampilkan semua buku (Read)
    public function index()
    {
        $books = Book::latest()->get();
        return view('admin.books.index', compact('books'));
    }

    // Menampilkan form tambah buku
    public function create(Request $request)
    {
        $bookRequest = null;

        // Jika admin datang dari halaman persetujuan book request
        if ($request->has('request_id')) {
            $bookRequest = BookRequest::findOrFail($request->request_id);
        } else {
            $bookRequest = null;
        }
        

        return view('admin.books.create', compact('bookRequest'));
    }

    // Menyimpan data buku baru ke database (Create)
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title'         => 'required|string|max:255',
            'author_name'   => 'required|string|max:255',
            'year'          => 'required|integer',
            'desc'          => 'nullable|string',
            'pageCount'     => 'nullable|integer',
            'subject'       => 'nullable|string', // Nanti kita ubah jadi array
            'cover'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Mengubah string subject (pisahkan dengan koma) menjadi array sesuai cast di model
        if ($request->subject) {
            $data['subject'] = array_map('trim', explode(',', $request->subject));
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        \App\Models\Book::create($data);

        if ($request->has('book_request_id')) {
            $bookRequest = BookRequest::find($request->book_request_id);
            if ($bookRequest) {
                $bookRequest->update(['status' => 'approved']);
            }
        }

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Menampilkan form edit
    public function edit(Book $book)
    {
        // Mengirim data buku ke view admin/books/edit.blade.php
        return view('admin.books.edit', compact('book'));
    }

    // 2. Memproses Perubahan Data (Update)
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'author_name'   => 'required|string|max:255',
            'year'          => 'required|integer',
            'desc'          => 'nullable|string',
            'pageCount'     => 'nullable|integer',
            'subject'       => 'nullable|string',
            'cover'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handling Subject (Ubah string koma menjadi array)
        if ($request->subject) {
            $data['subject'] = array_map('trim', explode(',', $request->subject));
        }

        // Handling Cover Image
        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada sebelum upload yang baru
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }
            
            // Simpan cover baru
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        } else {
            // Jika tidak upload cover baru, tetap gunakan cover yang lama
            $data['cover'] = $book->cover;
        }

        $book->update($data);

        return redirect()->route('admin.books.index')
                        ->with('success', 'Buku "' . $book->title . '" berhasil diperbarui!');
    }

    // Menghapus buku (Delete)
    public function destroy(Book $book)
    {
        // Hapus gambar agar tidak jadi sampah di storage
        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus!');
    }
}