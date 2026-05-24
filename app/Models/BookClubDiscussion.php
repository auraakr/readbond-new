<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookClubDiscussion extends Model
{
    use HasFactory;

    protected $table = 'book_club_discussions';

    protected $fillable = [
        'book_club_id',
        'user_id',
        'title',
    ];

    /**
     * Relasi ke Book Club tempat diskusi ini bernaung.
     */
    public function bookClub()
    {
        return $this->belongsTo(BookClub::class, 'book_club_id');
    }

    /**
     * Relasi ke User pembuat thread/topik diskusi ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(BookClubPost::class, 'discussion_id');
    }

    public function posts()
    {
        return $this->hasMany(BookClubPost::class, 'discussion_id');
    }
}