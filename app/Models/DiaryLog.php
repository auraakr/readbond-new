<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiaryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'read_date',
        'notes',
        'pages_read',
        'current_page',
        'mood',
        'is_favorite',
    ];

    protected $casts = [
        'read_date' => 'date',
        'is_favorite' => 'boolean',
    ];

    protected $dates = [
        'read_date',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Scopes
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('read_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('read_date', now()->year)
                    ->whereMonth('read_date', now()->month);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('read_date', now()->year);
    }

    /**
     * Helpers
     */
    public function getFormattedDateAttribute()
    {
        return $this->read_date->format('d M Y');
    }

    public function getMonthYearAttribute()
    {
        return $this->read_date->format('F Y');
    }

    public function getDayAttribute()
    {
        return $this->read_date->format('j');
    }

    public function getMonthShortAttribute()
    {
        return $this->read_date->format('M');
    }

    /**
     * Calculate reading streak for user
     */
    public static function calculateStreak($userId)
    {
        $user = User::find($userId);
        if (!$user) return 0;

        $today = today();
        $streak = 0;
        $checkDate = $today;

        // Check backwards from today
        while (true) {
            $hasEntry = self::where('user_id', $userId)
                ->whereDate('read_date', $checkDate)
                ->exists();

            if ($hasEntry) {
                $streak++;
                $checkDate = $checkDate->subDay();
            } else {
                break;
            }
        }

        // Update user streak
        $user->update([
            'reading_streak' => $streak,
            'last_diary_date' => $streak > 0 ? $today : null,
        ]);

        return $streak;
    }
}