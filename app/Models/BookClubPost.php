<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookClubPost extends Model
{
    use HasFactory;

    protected $table = 'book_club_posts';

    protected $fillable = [
        'discussion_id',
        'user_id',
        'content',
    ];

    /**
     * Relasi balik ke Thread Diskusi induknya.
     */
    public function discussion()
    {
        return $this->belongsTo(BookClubDiscussion::class, 'discussion_id');
    }

    /**
     * Relasi ke User yang menulis post/balasan ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}