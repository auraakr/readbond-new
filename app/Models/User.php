<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
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
        'streak_count',  
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

}
