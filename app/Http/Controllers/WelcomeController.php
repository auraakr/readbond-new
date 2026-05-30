<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $friendsActivity = collect();
        if (Auth::check()) {
            $followingIds = Auth::user()->following()->pluck('users.id');
            if ($followingIds->isNotEmpty()) {
                $friendsActivity = ReadingLog::with(['user', 'book'])
                    ->whereIn('user_id', $followingIds)
                    ->whereHas('book')
                    ->latest('updated_at')
                    ->limit(6)
                    ->get();
            }
        }

        return view('welcome', compact('popularBooks', 'mostReviewed', 'friendsActivity'));
    }
}
