<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'age',
        'reading_streak',
        'last_diary_date',  
        'role',
        'avatar',
        'bio',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Relationships ──

    public function bookRatings()
    {
        return $this->hasMany(BookRating::class);
    }

    public function bookLikes()
    {
        return $this->hasMany(BookLike::class);
    }

    public function readingLists()
    {
        return $this->hasMany(BookReadList::class);
    }

    public function readingLogs()
    {
        return $this->hasMany(ReadingLog::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookReview::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class, 'user_id');
    }

    public function getBooksThisYearAttribute()
    {
        return $this->readingLogs()
            ->where('status', 'finished')
            ->whereYear('finished_at', now()->year)
            ->count();
    }

    public function diaryLogs()
    {
        return $this->hasMany(DiaryLog::class);
    }

    public function updateReadingStreak()
    {
        return DiaryLog::calculateStreak($this->id);
    }

    public function getReadingStreakAttribute()
    {
        return $this->attributes['reading_streak'] ?? 0;
    }

    public function clubs()
    {
        return $this->belongsToMany(BookClub::class, 'book_club_members')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'followers',      // nama tabel pivot
            'following_id',   // foreign key untuk user yang difollow (saya)
            'user_id'         // foreign key untuk user yang melakukan follow
        )->withTimestamps();
    }

    /**
     * Mengambil orang-orang yang diikuti oleh user ini (Following)
     * Following = Orang yang saya follow
     */
    public function following()
    {
        return $this->belongsToMany(
            User::class,
            'followers',      // nama tabel pivot
            'user_id',        // foreign key untuk user yang melakukan follow (saya)
            'following_id'    // foreign key untuk user yang difollow
        )->withTimestamps();
    }

    /**
     * Helper untuk mengecek apakah user ini sudah memfollow user tertentu
     */
    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    /**
     * Helper untuk mengecek apakah user ini difollow oleh user tertentu
     */
    public function isFollowedBy(User $user): bool
    {
        return $this->followers()->where('user_id', $user->id)->exists();
    }

    /**
     * Follow a user
     */
    public function follow(User $user): bool
    {
        // Tidak bisa follow diri sendiri
        if ($this->id === $user->id) {
            return false;
        }

        // Jika sudah follow, return false
        if ($this->isFollowing($user)) {
            return false;
        }

        // Tambahkan ke tabel followers
        $this->following()->attach($user->id);
        return true;
    }

    /**
     * Unfollow a user
     */
    public function unfollow(User $user): bool
    {
        // Jika belum follow, return false
        if (!$this->isFollowing($user)) {
            return false;
        }

        // Hapus dari tabel followers
        $this->following()->detach($user->id);
        return true;
    }

    /**
     * Toggle follow/unfollow
     */
    public function toggleFollow(User $user): bool
    {
        if ($this->id === $user->id) {
            return false; // Cannot follow yourself
        }

        if ($this->isFollowing($user)) {
            $this->unfollow($user);
            return false; // Now unfollowed
        } else {
            $this->follow($user);
            return true; // Now following
        }
    }

    // Relasi ke tabel buku melalui tabel pivot favorit
    public function favoriteBooks()
    {
        return $this->belongsToMany(Book::class, 'book_user_favorites')
                    ->withPivot('order_position')
                    ->orderBy('book_user_favorites.order_position', 'asc')
                    ->withTimestamps();
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!empty($this->avatar)) {
                return filter_var($this->avatar, FILTER_VALIDATE_URL) 
                    ? $this->avatar 
                    : asset('storage/' . $this->avatar);
            }
            return null;
        });
    }
}
