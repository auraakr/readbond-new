<?php

namespace App\Http\Controllers;

use App\Models\DiaryLog;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DiaryLogController extends Controller
{
    /**
     * Display diary logs
     */
    public function index(Request $request)
    {
        $user = User::where('id', Auth::id())
            ->withCount(['readingLogs as books_count'])
            ->withCount(['followers as followers_count', 'following as following_count'])
            ->firstOrFail();
        
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Get diary logs untuk bulan ini
        $diaryQuery = $user->diaryLogs()
            ->with(['book:id,external_id,title,author_name,cover'])
            ->whereYear('read_date', $year)
            ->whereMonth('read_date', $month);

        $diaryLogs = $diaryQuery->orderBy('read_date', 'desc')->paginate(5);

        // Get all diary logs for calendar (hanya untuk bulan yang ditampilkan)
        $calendarEntries = $user->diaryLogs()
            ->with(['book:id,external_id,title,cover'])
            ->whereYear('read_date', $year)
            ->whereMonth('read_date', $month)
            ->get()
            ->groupBy(function($log) {
                return $log->read_date->format('Y-m-d');
            })
            ->map(function($logs) {
                // Ambil max 4 cover buku per hari
                return $logs->take(4)->map(function($log) {
                    return [
                        'book_id' => $log->book_id,
                        'cover' => $log->book->cover_url ?? null,
                        'title' => $log->book->title ?? 'Unknown',
                        'mood' => $log->mood,
                    ];
                });
            });

        // Calculate streak
        $streak = DiaryLog::calculateStreak($user->id);

        // Get stats for this year
        $stats = [
            'total_entries' => $user->diaryLogs()->thisYear()->count(),
            'total_books' => $user->diaryLogs()->thisYear()->distinct('book_id')->count(),
            'total_pages' => $user->diaryLogs()->thisYear()->sum('pages_read'),
            'this_month' => $user->diaryLogs()->thisMonth()->count(),
        ];

        // Get available years
        $availableYears = $user->diaryLogs()
            ->selectRaw('DISTINCT YEAR(read_date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('diary.index', [
            'user' => $user,
            'diaryLogs' => $diaryLogs,
            'calendarEntries' => $calendarEntries,
            'streak' => $streak,
            'stats' => $stats,
            'currentYear' => $year,
            'currentMonth' => $month,
            'availableYears' => $availableYears,
        ]);
    }

    /**
     * Store diary log - Only allow today's date
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'read_date' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . now()->subDays(1)->format('Y-m-d'), // Allow yesterday and today only
            ],
            'notes' => 'nullable|string|max:1000',
            'pages_read' => 'nullable|integer|min:0',
            'current_page' => 'nullable|integer|min:0',
            'mood' => 'nullable|string|max:50',
        ], [
            'read_date.before_or_equal' => 'You cannot add diary entries for future dates.',
            'read_date.after_or_equal' => 'You can only add entries for today or yesterday.',
        ]);

        $validated['user_id'] = Auth::id();

        // Create or update diary log for this date
        $diaryLog = DiaryLog::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'read_date' => $validated['read_date'],
            ],
            $validated
        );

        // Update streak
        DiaryLog::calculateStreak(Auth::id());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'diary_log' => $diaryLog->load('book'),
                'message' => 'Diary log saved successfully!',
            ]);
        }

        return redirect()->route('diary.index')
            ->with('success', 'Diary log berhasil disimpan!');
    }

    /**
     * Update diary log
     */
    public function update(Request $request, DiaryLog $diaryLog)
    {
        // Authorization
        if ($diaryLog->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'pages_read' => 'nullable|integer|min:0',
            'current_page' => 'nullable|integer|min:0',
            'mood' => 'nullable|string|max:50',
            'is_favorite' => 'boolean',
        ]);

        $diaryLog->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'diary_log' => $diaryLog->fresh()->load('book'),
            ]);
        }

        return redirect()->route('diary.index')
            ->with('success', 'Diary log berhasil diupdate!');
    }

    /**
     * Delete diary log
     */
    public function destroy(DiaryLog $diaryLog)
    {
        // Authorization
        if ($diaryLog->user_id !== Auth::id()) {
            abort(403);
        }

        $diaryLog->delete();

        // Update streak
        DiaryLog::calculateStreak(Auth::id());

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Diary log deleted successfully!',
            ]);
        }

        return redirect()->route('diary.index')
            ->with('success', 'Diary log berhasil dihapus!');
    }

    /**
     * Toggle favorite
     */
    public function toggleFavorite(DiaryLog $diaryLog)
    {
        if ($diaryLog->user_id !== Auth::id()) {
            abort(403);
        }

        $diaryLog->update([
            'is_favorite' => !$diaryLog->is_favorite,
        ]);

        return response()->json([
            'success' => true,
            'is_favorite' => $diaryLog->is_favorite,
        ]);
    }

    /**
     * Navigate to previous/next month
     */
    public function changeMonth(Request $request)
    {
        $direction = $request->input('direction', 'next'); // 'next' or 'prev'
        $currentYear = $request->input('year', now()->year);
        $currentMonth = $request->input('month', now()->month);

        $date = Carbon::create($currentYear, $currentMonth, 1);
        
        if ($direction === 'next') {
            $date->addMonth();
        } else {
            $date->subMonth();
        }

        return redirect()->route('diary.index', [
            'year' => $date->year,
            'month' => $date->month,
        ]);
    }
}