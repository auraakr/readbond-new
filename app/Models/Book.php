<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Atribut yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'external_id',
        'title',
        'desc',
        'author_id',
        'author_name', 
        'year',
        'cover',
        'subject',
        'pageCount',
        'averageRating',
    ];

    protected $casts = [
        'subject' => 'array',
    ];

    public function getBookDetails(): void
    {
        logger("Detail Buku: {$this->title} oleh Author ID {$this->author_id}");
    }

    public function updateAverageRating($newRating): void
    {
        $this->averageRating = $newRating;
        $this->save();
    }
}