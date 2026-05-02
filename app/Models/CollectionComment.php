<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'user_id',
        'body',
        'rating',
        'likes_count',
    ];

    // ── Relationships ──

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(CollectionCommentLike::class);
    }

    // ── Helpers ──

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}