<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'desc',
        'author_name',
        'year',
        'cover',
        'subject',
        'pageCount',
        'averageRating',
        'view_count',
    ];

    protected $casts = [
        'subject' => 'array',
        'view_count' => 'integer',
    ];

    // ── Relationships ──

    public function likes()
    {
        return $this->hasMany(BookLike::class);
    }

    public function ratings()
    {
        return $this->hasMany(BookRating::class);
    }

    // Tambahkan alias untuk consistency
    public function bookRatings()
    {
        return $this->hasMany(BookRating::class);
    }

    public function readlists()
    {
        return $this->hasMany(BookReadlist::class);
    }

    public function readingLogs()
    {
        return $this->hasMany(ReadingLog::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_book')
                    ->withPivot('order');
    }

    // ── Scopes untuk filtering ──

    public function scopePopular($query, $days = 30)
    {
        return $query->withCount(['likes' => function ($q) use ($days) {
            $q->where('created_at', '>=', now()->subDays($days));
        }])
        ->orderBy('likes_count', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->orderBy('averageRating', 'desc');
    }

    public function scopeMostViewed($query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    // ── Helpers ──

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function isInReadlistOf(User $user): bool
    {
        return $this->readlists()->where('user_id', $user->id)->exists();
    }

    public function ratingBy(User $user): ?int
    {
        return $this->ratings()->where('user_id', $user->id)->value('rating');
    }

    public function readingLogFor(User $user): ?ReadingLog
    {
        return $this->readingLogs()->where('user_id', $user->id)->first();
    }

    // Recalculate averageRating dari semua rating yang ada
    public function recalculateRating(): void
    {
        $avg = $this->ratings()->avg('rating') ?? 0;
        $this->update(['averageRating' => round($avg, 2)]);
    }

    // fungsi get cover img pake accessor
    public function getCoverUrlAttribute(): string
    {
        if (!$this->cover) {
            return asset('images/placeholder-book.png');
        }

        // Kalau sudah URL lengkap (dari API), pakai langsung
        if (Str::startsWith($this->cover, 'http')) {
            return $this->cover;
        }

        // Kalau path lokal (dari upload admin), pakai storage
        return asset('storage/' . $this->cover);
    }
}