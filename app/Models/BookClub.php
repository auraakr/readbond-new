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

    // Relasi ke semua buku yang terikat dengan Club ini
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_club_books')
                    ->withPivot('status', 'added_by')
                    ->withTimestamps();
    }

    // Shortcut query untuk mengambil buku yang saat ini SEDANG DIBACA bersama
    public function currentlyReading()
    {
        return $this->books()->wherePivot('status', 'reading');
    }

    // Shortcut query untuk mengambil daftar arsip buku yang SUDAH SELESAI dibaca bersama
    public function completedBooks()
    {
        return $this->books()->wherePivot('status', 'completed');
    }
}