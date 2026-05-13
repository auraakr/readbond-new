<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingLog extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'started_at',
        'finished_at',
        'notes',
    ];

    protected $casts = [
        'started_at'  => 'date',
        'finished_at' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function book() { return $this->belongsTo(Book::class); }
}