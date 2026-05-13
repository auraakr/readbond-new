<?php

namespace App\Http\Controllers;

use App\Models\ReadingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── INDEX — tampilkan semua log milik user ──
    public function index(Request $request)
    {
        $status = $request->input('status'); // null = semua

        $logs = ReadingLog::with('book')
            ->where('user_id', Auth::id())
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        // Hitung jumlah per status untuk stats row
        $counts = ReadingLog::where('user_id', Auth::id())
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = array_merge([
            'finished'     => 0,
            'reading'      => 0,
            'want_to_read' => 0,
        ], $counts);

        $activeStatus = $status ?? 'all';

        return view('user.reading-log', compact('logs', 'counts', 'activeStatus'));
    }

    // ── DESTROY — hapus satu log ──
    public function destroy(string $id)
    {
        $log = ReadingLog::where('user_id', Auth::id())->findOrFail($id);
        $log->delete();

        return back()->with('success', 'Reading log dihapus.');
    }
}