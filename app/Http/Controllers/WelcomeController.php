<?php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $popularBooks = Book::query()->whereNotNull('external_id')
                        ->withCount('likes')
                        ->orderBy('likes_count', 'desc')
                        ->limit(4)
                        ->get();

        $mostReviewed = Cache::remember('most_reviewed', 3600, function () {
            $response = Http::get('https://openlibrary.org/search.json', [
                'q' => 'fiction',
                'sort' => 'already_read',
                'limit' => 12,
            ]);
            return $response->json()['docs'] ?? [];
        });

        return view('welcome', compact('popularBooks', 'mostReviewed'));
    }
}