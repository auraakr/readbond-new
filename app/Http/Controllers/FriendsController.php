<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendsController extends Controller
{
    /**
     * Display a listing of the user's friends.
     */
    public function index()
    {
        // Mengambil user populer atau fitur beserta 4 buku terakhir yang mereka selesaikan (status: finished)
        $featuredMembers = User::withCount(['reviews', 'readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->with(['readingLogs' => function($query) {
                $query->where('status', 'finished')->with('book')->latest()->take(4);
            }])
            ->orderBy('reviews_count', 'desc')
            ->take(5)
            ->get();

        $popularMembers = User::withCount(['reviews', 'readingLogs as books_count' => function($query) {
                $query->where('status', 'finished');
            }])
            ->with(['readingLogs' => function($query) {
                $query->where('status', 'finished')->with('book')->latest()->take(4);
            }])
            ->latest()
            ->take(5)
            ->get();

        // Memproses URL avatar secara aman untuk setiap member
        $currentUserId = Auth::id();

        $formatAvatar = function($member) use ($currentUserId) {
            $member->avatar_url = !empty($member->avatar)
                ? (filter_var($member->avatar, FILTER_VALIDATE_URL) ? $member->avatar : asset('storage/' . $member->avatar))
                : null;
                
            // Cek apakah user yang login mem-follow member ini
            $member->is_followed = $currentUserId 
                ? $member->followers()->where('user_id', $currentUserId)->exists() 
                : false;

            return $member;
        };

        $featuredMembers->transform($formatAvatar);
        $popularMembers->transform($formatAvatar);

        return view('friends.index', compact('featuredMembers', 'popularMembers'));
    }
}