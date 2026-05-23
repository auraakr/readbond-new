<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReview extends Model
{
    protected $fillable = ['user_id', 'book_id', 'rating', 'review', 'is_liked'];

    protected $casts = [
        'rating' => 'integer',
        'is_liked' => 'boolean',
    ];

    /**
     * Get the user who wrote the review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book being reviewed
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'book_review_likes', 'book_review_id', 'user_id')
                    ->withTimestamps();
    }

}
