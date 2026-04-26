<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        // Cache data selama 1 jam agar web kencang dan tidak kena limit API
        $popularBooks = Cache::remember('popular_books', 3600, function () {
            $response = Http::timeout(30) // Tunggu sampai 30 detik
            ->connectTimeout(15)      // Batas waktu usaha menyambung ke server
            ->get('https://openlibrary.org/search.json', [
                'q' => 'fiction',
                'sort' => 'rating',
                'limit' => 6,
            ]);
            return $response->json()['docs'] ?? [];
        });

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