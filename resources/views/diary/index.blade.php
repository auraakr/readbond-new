@extends('layouts.main')

@section('title', 'My Diary')

@section('content')

<div class="min-h-screen bg-[#0a0a0f] text-white pb-20" style="font-family:'DM Sans',sans-serif;">
    
    {{-- Header --}}
    <div class="bg-slate-900 border-b border-white/5 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">My Diary</h1>
                <button onclick="openDiaryModal()" 
                        class="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add diary log
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-6 py-6">
        
        {{-- Streak Card --}}
        <div class="bg-gradient-to-br from-orange-600 to-red-600 rounded-2xl p-6 mb-6 text-center">
            <p class="text-sm font-semibold uppercase tracking-wider mb-2 opacity-90">Streak</p>
            <div class="flex items-center justify-center gap-2 mb-1">
                <span class="text-5xl">🔥</span>
                <span class="text-5xl font-bold">{{ $streak }}</span>
            </div>
            <p class="text-sm opacity-90">{{ $streak === 1 ? 'day' : 'days' }} reading streak!</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-900 border border-white/5 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-purple-400">{{ $stats['total_entries'] }}</p>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Entries</p>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-blue-400">{{ $stats['total_books'] }}</p>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Books</p>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-green-400">{{ $stats['total_pages'] }}</p>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Pages</p>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-yellow-400">{{ $stats['this_month'] }}</p>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">This Month</p>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="bg-slate-900 border border-white/5 rounded-xl mb-6 overflow-hidden">
            <div class="flex overflow-x-auto scrollbar-hide">
                <button class="flex-1 px-4 py-3 text-xs font-semibold uppercase tracking-widest border-b-2 border-purple-500 text-white whitespace-nowrap">
                    Buku
                </button>
                <button class="flex-1 px-4 py-3 text-xs font-semibold uppercase tracking-widest border-b-2 border-transparent text-slate-500 hover:text-white transition whitespace-nowrap">
                    Judul
                </button>
                <button class="flex-1 px-4 py-3 text-xs font-semibold uppercase tracking-widest border-b-2 border-transparent text-slate-500 hover:text-white transition whitespace-nowrap">
                    Bulan
                </button>
                <button class="flex-1 px-4 py-3 text-xs font-semibold uppercase tracking-widest border-b-2 border-transparent text-slate-500 hover:text-white transition whitespace-nowrap">
                    Hari
                </button>
                <button class="flex-1 px-4 py-3 text-xs font-semibold uppercase tracking-widest border-b-2 border-transparent text-slate-500 hover:text-white transition whitespace-nowrap">
                    Note
                </button>
            </div>
        </div>

        {{-- Diary Entries --}}
        <div class="space-y-3">
            @forelse($diaryLogs as $log)
            <div class="bg-slate-900 border border-white/5 rounded-xl overflow-hidden hover:border-purple-500/30 transition group">
                <div class="flex items-center gap-4 p-4">
                    {{-- Book Cover --}}
                    <a href="{{ route('books.show', $log->book->external_id) }}" 
                       class="flex-shrink-0 w-16 h-24 rounded overflow-hidden bg-slate-800 border border-white/10 group-hover:border-purple-500/50 transition">
                        @if($log->book->cover_url)
                            <img src="{{ $log->book->cover_url }}" 
                                 class="w-full h-full object-cover" 
                                 alt="{{ $log->book->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        @endif
                    </a>

                    {{-- Book Title --}}
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('books.show', $log->book->external_id) }}" 
                           class="font-semibold text-white hover:text-purple-400 transition line-clamp-1">
                            {{ $log->book->title }}
                        </a>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $log->book->author_name }}</p>
                        
                        @if($log->notes)
                        <p class="text-sm text-slate-400 mt-2 line-clamp-2">{{ $log->notes }}</p>
                        @endif

                        @if($log->pages_read)
                        <p class="text-xs text-slate-600 mt-1">{{ $log->pages_read }} pages read</p>
                        @endif
                    </div>

                    {{-- Date --}}
                    <div class="flex-shrink-0 text-center px-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-400">
                            {{ $log->month_short }}
                        </p>
                        <p class="text-2xl font-bold text-white">{{ $log->day }}</p>
                        <p class="text-xs text-slate-600">{{ $log->read_date->year }}</p>
                    </div>

                    {{-- Note Icon --}}
                    <div class="flex-shrink-0">
                        @if($log->notes)
                        <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        @else
                        <div class="w-10 h-10"></div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-slate-500 text-lg font-medium">No diary entries yet</p>
                <p class="text-slate-600 text-sm mt-2">Start logging your reading journey today!</p>
                <button onclick="openDiaryModal()" 
                        class="mt-6 px-6 py-3 bg-purple-600 hover:bg-purple-700 rounded-lg transition font-semibold">
                    Add Your First Entry
                </button>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($diaryLogs->hasPages())
        <div class="mt-8">
            {{ $diaryLogs->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Diary Modal Component --}}
<x-diary-log-modal />

<script>
function openDiaryModal() {
    document.getElementById('diary-modal').classList.remove('hidden');
}
</script>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

@endsection