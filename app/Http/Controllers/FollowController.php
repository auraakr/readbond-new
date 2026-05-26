<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    /**
     * Toggle follow/unfollow a user
     */
    public function toggleFollow(Request $request, $id)
    {
        Log::info('Follow toggle attempt', [
            'user_id' => Auth::id(),
            'target_id' => $id
        ]);

        try {
            // Check authentication
            if (!Auth::check()) {
                Log::warning('Follow attempt without auth');
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized. Please login first.'
                ], 401);
            }

            $currentUser = Auth::user();
            Log::info('Current user loaded', ['id' => $currentUser->id]);
            
            // Find the user to follow/unfollow
            $targetUser = User::find($id);
            
            if (!$targetUser) {
                Log::warning('Target user not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'error' => 'User not found'
                ], 404);
            }

            Log::info('Target user loaded', ['id' => $targetUser->id]);

            // Prevent following yourself
            if ($currentUser->id === $targetUser->id) {
                Log::warning('Attempted to follow self');
                return response()->json([
                    'success' => false,
                    'error' => 'You cannot follow yourself'
                ], 400);
            }

            // Check current follow status
            $isCurrentlyFollowing = $currentUser->isFollowing($targetUser);
            Log::info('Current follow status', ['is_following' => $isCurrentlyFollowing]);

            // Toggle follow status
            if ($isCurrentlyFollowing) {
                // Unfollow
                $currentUser->following()->detach($targetUser->id);
                $isFollowing = false;
                Log::info('Unfollowed successfully');
            } else {
                // Follow
                $currentUser->following()->attach($targetUser->id);
                $isFollowing = true;
                Log::info('Followed successfully');
            }

            // Verify the action
            $verifyFollow = $currentUser->isFollowing($targetUser);
            Log::info('Verification', ['is_following' => $verifyFollow]);

            $followersCount = $targetUser->followers()->count();
            $followingCount = $currentUser->following()->count();

            return response()->json([
                'success' => true,
                'is_following' => $isFollowing,
                'followers_count' => $followersCount,
                'following_count' => $followingCount,
                'message' => $isFollowing ? 'Successfully followed' : 'Successfully unfollowed'
            ]);

        } catch (\Exception $e) {
            Log::error('Follow toggle error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'An error occurred: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}