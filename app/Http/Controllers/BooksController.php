<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Book;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $genre = $request->input('genre');
        $year  = $request->input('year');

        $popularBooks = Cache::remember("popular_books_{$query}_{$genre}_{$year}", 3600, function () use ($query, $genre, $year) {
            $q = $query ?? 'popular';
            if ($year) $q .= " first_publish_year:{$year}";

            $params = [
                'q'      => $q,
                'fields' => 'key,title,author_name,cover_i,first_publish_year,subject', // ✅ tambah subject
                'limit'  => 12,
            ];
            if ($genre) $params['subject'] = $genre;

            return Http::get('https://openlibrary.org/search.json', $params)->json()['docs'] ?? [];
        });

        return view('books', compact('popularBooks', 'query', 'genre', 'year'));
    }

    public function show($external_id)
    {
        $book = Book::where('external_id', $external_id)->first();

        if (!$book) {
            $response = Http::get("https://openlibrary.org/works/{$external_id}.json")->json();

            $year = '0';
            $searchResponse = Http::get('https://openlibrary.org/search.json', [
                'q'      => "key:/works/{$external_id}",
                'fields' => 'first_publish_year,subject',
                'limit'  => 1,
            ])->json();
            $year    = $searchResponse['docs'][0]['first_publish_year'] ?? 0;
            // ✅ Ambil subject dari search endpoint (lebih lengkap)
            $subject = $searchResponse['docs'][0]['subject'] ?? [];
            // Batasi 10 subject saja agar tidak terlalu banyak
            $subject = array_slice($subject, 0, 10);

            $description = is_array($response['description'] ?? null)
                            ? $response['description']['value']
                            : ($response['description'] ?? 'No description available.');

            $authorName  = 'Unknown Author';
            $authorKey   = $response['authors'][0]['author']['key'] ?? null;
            if ($authorKey) {
                $authorResponse = Http::get("https://openlibrary.org{$authorKey}.json")->json();
                $authorName = $authorResponse['name'] ?? 'Unknown Author';
            }

            $book = Book::create([
                'external_id'   => $external_id,
                'title'         => $response['title'],
                'desc'          => $description,
                'year'          => (int)$year,
                'pageCount'     => $response['number_of_pages'] ?? 0,
                'cover'         => isset($response['covers'])
                                ? "https://covers.openlibrary.org/b/id/{$response['covers'][0]}-L.jpg"
                                : null,
                'author_name'   => $authorName,
                'subject'       => $subject, // ✅
                'averageRating' => 0,
            ]);
        }

        return view('book-detail', compact('book'));
    }

    // Tambah endpoint autocomplete
    public function autocomplete(Request $request)
    {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) return response()->json([]);

        $results = Http::get('https://openlibrary.org/search.json', [
            'q'      => $q,
            'fields' => 'key,title,author_name,cover_i', // ✅ tambah key
            'limit'  => 6,
        ])->json()['docs'] ?? [];

        return response()->json(collect($results)->map(fn($b) => [
            'title'  => $b['title'],
            'author' => $b['author_name'][0] ?? 'Unknown',
            'cover'  => isset($b['cover_i']) ? "https://covers.openlibrary.org/b/id/{$b['cover_i']}-S.jpg" : null,
            'url'    => route('books.show', str_replace('/works/', '', $b['key'])), // ✅ langsung generate URL
        ]));
    }
}