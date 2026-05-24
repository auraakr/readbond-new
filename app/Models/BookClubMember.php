<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\BookClub;

class BookClubMember extends Pivot
{
    // Beri tahu Laravel bahwa pivot table ini menggunakan incrementing ID & timestamps
    public $incrementing = true;
    
    protected $table = 'book_club_members';

    protected $fillable = [
        'book_club_id',
        'user_id',
        'role', // 'moderator' atau 'member'
    ];

    /**
     * Helper check: Apakah entry member ini merupakan seorang moderator?
     */
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    /**
     * Relasi opsional balik ke objek User langsung dari entitas Pivot
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi opsional balik ke objek BookClub langsung dari entitas Pivot
     */
    public function bookClub()
    {
        return $this->belongsTo(BookClub::class, 'book_club_id');
    }
}