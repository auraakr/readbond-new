<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'author',
        'isbn',
        'notes',
        'status',
    ];

    // Relasi: Pengajuan ini milik seorang User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}