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
            ->withCount(['readingLogs as books_count']) // atau ->withCount(['diaryLogs as books_count']) tergantung tabel mana yang dihitung
            ->firstOrFail();
        $year = $request->input('year', now()->year);
        $month = $request->input('month');

        // Get diary logs
        $diaryQuery = $user->diaryLogs()
            ->with('book')
            ->whereYear('read_date', $year);

        if ($month) {
            $diaryQuery->whereMonth('read_date', $month);
        }

        $diaryLogs = $diaryQuery->orderBy('read_date', 'desc')->paginate(20);

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
            ->pluck('year')
            ->sort()
            ->reverse();

        return view('diary.index', [
            'user' => $user,
            'diaryLogs' => $diaryLogs,
            'streak' => $streak,
            'stats' => $stats,
            'currentYear' => $year,
            'currentMonth' => $month,
            'availableYears' => $availableYears,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('diary.create');
    }

    /**
     * Store diary log
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'read_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'pages_read' => 'nullable|integer|min:0',
            'current_page' => 'nullable|integer|min:0',
            'mood' => 'nullable|string|max:50',
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
     * Show edit form
     */
    public function edit(DiaryLog $diaryLog)
    {
        // Authorization
        if ($diaryLog->user_id !== Auth::id()) {
            abort(403);
        }

        return view('diary.edit', compact('diaryLog'));
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
}