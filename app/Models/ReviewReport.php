<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewReport extends Model
{
    protected $fillable = [
        'user_id', 
        'book_review_id', 
        'reason', 
        'notes', 
        'status'
    ];

    public function reports()
    {
        return $this->hasMany(ReviewReport::class, 'book_review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    function bookReview()
    {
        return $this->belongsTo(BookReview::class);
    }
}
