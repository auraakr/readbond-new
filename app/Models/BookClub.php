<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Book;
use App\Models\BookClubDiscussion;
use App\Models\BookClubMember;

class BookClub extends Model
{
    protected $fillable = [
        'moderator_id', 'name', 'slug', 'description', 
        'category', 'rules', 'cover_image', 
        'current_book_id', 'current_book_reason', 'current_book_finish_date'
    ];

    // Otomatis buat slug saat bikin club baru
    protected static function boot() {
        parent::boot();
        static::creating(function ($club) {
            $club->slug = Str::slug($club->name);
        });
    }

    public function moderator() {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'book_club_members')
            ->using(BookClubMember::class) 
            ->withPivot('role')
            ->withTimestamps();
    }

    public function currentBook() {
        return $this->belongsTo(Book::class, 'current_book_id');
    }

    public function discussions() {
        return $this->hasMany(BookClubDiscussion::class);
    }
}