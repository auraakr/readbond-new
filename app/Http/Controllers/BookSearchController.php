<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        // Cari buku berdasarkan judul di DB lokal kamu
        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->select('id', 'title', 'cover')
            ->limit(5)
            ->get();

        return response()->json($books);
    }
}
