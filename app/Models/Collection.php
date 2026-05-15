<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'cover',
        'visibility',
        'is_featured',
        'likes_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    // ── Relationships ──

    public function curator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'collection_book')
                    ->withPivot('order')
                    ->orderByPivot('order');
    }

    public function comments()
    {
        return $this->hasMany(CollectionComment::class)->latest();
    }

    public function likes()
    {
        return $this->hasMany(CollectionLike::class);
    }

    // ── Helpers ──

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // Ambil max 4 cover buku untuk collage preview
    public function previewCovers(): array
    {
        return $this->books()
                    ->whereNotNull('cover')
                    ->limit(4)
                    ->pluck('cover')
                    ->toArray();
    }
}