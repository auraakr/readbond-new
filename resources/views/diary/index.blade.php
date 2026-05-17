@extends('layouts.profile')

@section('title', $user->username . "'s Diary Logs")

@section('content')

<style>
    .input-field:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.1); outline: none; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.06); }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
    
    /* Calendar book covers grid */
    .calendar-covers {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
    }
    
    .calendar-cover-item {
        aspect-ratio: 2/3;
        background-size: cover;
        background-position: center;
    }
</style>

    {{-- ── BODY ── --}}
    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex items-end justify-between gap-6 mb-4 border-b border-slate-800/40">
            {{-- Header --}}
            <div class="flex flex-col gap-3 py-3">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-3">Reading Diary</p>
                {{-- Streak Display --}}
                <div class="text-center">
                    <p class="text-sm font-semibold uppercase tracking-wider mb-2 opacity-90">Streak</p>
                    <div class="flex gap-2 mb-1">
                        <span class="text-5xl">🔥</span>
                        <span class="text-5xl font-bold">{{ $streak }}</span>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-purple-400">{{ $stats['total_entries'] }}</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Entries</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-blue-400">{{ $stats['total_books'] }}</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Books</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-green-400">{{ $stats['total_pages'] }}</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">Pages</p>
                </div>
                <div class="p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-400">{{ $stats['this_month'] }}</p>
                    <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">This Month</p>
                </div>
            </div>
        </div>

        <div class="flex gap-8 items-start">
            
            {{-- ── MAIN COLUMN ── --}}
            <div class="flex-1 min-w-0 flex flex-col gap-4">

                {{-- Month Navigation --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('diary.index', ['year' => $currentYear, 'month' => $currentMonth == 1 ? 12 : $currentMonth - 1, 'year' => $currentMonth == 1 ? $currentYear - 1 : $currentYear]) }}" 
                    class="p-2 hover:bg-slate-800 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <span class="text-lg font-semibold px-4">
                        {{ Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}
                    </span>
                    <a href="{{ route('diary.index', ['year' => $currentYear, 'month' => $currentMonth == 12 ? 1 : $currentMonth + 1, 'year' => $currentMonth == 12 ? $currentYear + 1 : $currentYear]) }}" 
                    class="p-2 hover:bg-slate-800 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                {{-- Calendar Grid --}}
                <div class="bg-slate-900 rounded-xl overflow-hidden border border-white/5">
                    {{-- Day Headers --}}
                    <div class="grid grid-cols-7 border-b border-white/5">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="p-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                            {{ $day }}
                        </div>
                        @endforeach
                    </div>

                    {{-- Calendar Days --}}
                    <div class="grid grid-cols-7">
                        @php
                            $startOfMonth = Carbon\Carbon::create($currentYear, $currentMonth, 1);
                            $endOfMonth = $startOfMonth->copy()->endOfMonth();
                            $startDay = $startOfMonth->dayOfWeek;
                            $daysInMonth = $startOfMonth->daysInMonth;
                            $today = now();
                        @endphp

                        {{-- Empty cells before month starts --}}
                        @for($i = 0; $i < $startDay; $i++)
                        <div class="aspect-square border-b border-r border-white/5 bg-slate-800/30"></div>
                        @endfor

                        {{-- Days of month --}}
                        @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $date = Carbon\Carbon::create($currentYear, $currentMonth, $day);
                            $dateKey = $date->format('Y-m-d');
                            $isToday = $date->isToday();
                            $isPast = $date->isPast() && !$isToday;
                            $isFuture = $date->isFuture();
                            $entries = $calendarEntries->get($dateKey, collect());
                            $hasEntry = $entries->isNotEmpty();
                        @endphp
                        <div class="aspect-square border-b border-r border-white/5 p-1 
                                    {{ $isToday ? 'bg-purple-600/20 border-purple-500' : '' }}
                                    {{ $isFuture ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-800 transition group cursor-pointer' }}"
                             {{ !$isFuture ? 'onclick=showDayEntries("' . $dateKey . '")' : '' }}>
                            <div class="flex flex-col h-full">
                                {{-- Day Number --}}
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs font-semibold {{ $isToday ? 'text-purple-400' : ($isFuture ? 'text-slate-600' : 'text-white') }}">
                                        {{ $day }}
                                    </span>
                                    @if($hasEntry)
                                    <span class="text-[9px] bg-green-600/20 text-green-400 px-1.5 py-0.5 rounded-full font-bold">
                                        {{ $entries->count() }}
                                    </span>
                                    @endif
                                </div>
                                
                                {{-- Book Covers Grid --}}
                                @if($hasEntry)
                                <div class="flex-1 calendar-covers gap-1">
                                    @foreach($entries->take(4) as $entry)
                                        @if($entry['cover'])
                                        <div class="calendar-cover-item opacity-80 group-hover:opacity-100 transition w-7"
                                             style="background-image: url('{{ $entry['cover'] }}');"
                                             title="{{ $entry['title'] }}">
                                        </div>
                                        @else
                                        <div class="calendar-cover-item bg-slate-800 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

            </div>

            {{-- ── SIDEBAR ── --}}
            <div class="hidden lg:flex flex-col gap-6 w-80 flex-shrink-0">
                {{-- Week Navigation --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-3 scrollbar-hide">
                    @php
                        $startOfWeek = now()->startOfWeek();
                    @endphp
                    
                    @for($i = 0; $i < 7; $i++)
                        @php 
                            $date = $startOfWeek->copy()->addDays($i);
                            $isCurrentDay = $date->isToday();
                        @endphp
                        
                        <a href="?date={{ $date->format('Y-m-d') }}" 
                           class="flex-1 min-w-[50px] flex flex-col items-center py-3 px-2 rounded-2xl transition border 
                                  {{ $isCurrentDay ? 'bg-slate-800 border-purple-600/50 shadow-md shadow-purple-600/5' : 'bg-slate-900/40 border-white/5 opacity-60 hover:opacity-100' }}">
                            
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                {{ $date->format('D') }}
                            </span>
                            
                            <span class="text-base font-bold mt-1 {{ $isCurrentDay ? 'text-purple-400' : 'text-slate-300' }}">
                                {{ $date->format('d') }}
                            </span>
                            
                            @if($isCurrentDay)
                                <span class="w-1 h-1 rounded-full bg-purple-500 mt-1"></span>
                            @endif
                        </a>
                    @endfor
                </div>

                <p onclick="openCreateModal()" class="text-sm font-semibold tracking-widest text-purple-500 hover:text-purple-400 cursor-pointer transition">
                    + New Diary Log
                </p>

                {{-- Timeline Logs --}}
                <div class="relative border-l-2 border-dashed border-slate-800 ml-4 pl-8 space-y-6">
                    @forelse($diaryLogs as $log)
                        <div class="relative group">
                            
                            {{-- Timeline Marker --}}
                            <div class="absolute -left-[55px] top-8 bg-slate-900 px-1 text-[11px] font-bold text-slate-500 uppercase tracking-tighter">
                                {{ $log->read_date->format('M d') }}
                            </div>
                            <div class="absolute -left-[37px] top-[18px] w-2 h-2 rounded-full bg-slate-700 group-hover:bg-purple-500 transition-colors duration-300"></div>

                            {{-- Card Log --}}
                            <div class="bg-slate-800/40 p-5 rounded-3xl border border-white/5 shadow-sm hover:border-white/10 hover:bg-slate-800/60 transition-all duration-300 flex items-start gap-4">

                                {{-- Mood Indicator --}}
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl bg-slate-800 border border-slate-700 shadow-inner flex-shrink-0">
                                    @switch(strtolower($log->mood ?? ''))
                                        @case('happy') 😊 @break
                                        @case('sad') 😢 @break
                                        @case('excited') 🤩 @break
                                        @case('calm') 😌 @break
                                        @case('thoughtful') 🤔 @break
                                        @default 📖
                                    @endswitch
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-1">
                                        <a href="{{ route('books.show', $log->book->external_id) }}" 
                                           class="font-bold text-sm text-slate-200 tracking-tight leading-tight hover:text-purple-400 transition-colors duration-300">
                                            {{ $log->book->title ?? 'Untitled Log' }}
                                        </a>
                                        
                                        {{-- Action Buttons --}}
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1 flex-shrink-0">
                                            <button onclick="deleteDiaryLog({{ $log->id }})" 
                                                    class="p-1 text-slate-500 hover:text-red-400 rounded-md hover:bg-slate-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-400 font-light mb-2">
                                        Progress: <span class="font-semibold text-purple-400">{{ $log->current_page ?? 0 }}</span> pg 
                                        @if($log->pages_read) 
                                        <span class="text-emerald-400">(+{{ $log->pages_read }} read)</span> 
                                        @endif
                                    </p>

                                    <p class="text-xs text-slate-300 font-light leading-relaxed line-clamp-3">
                                        {{ $log->notes ?? 'No thoughts recorded today.' }}
                                    </p>
                                    
                                    <div class="flex items-center gap-2 mt-3 text-[10px] text-slate-500 font-medium">
                                        <span>📄 {{ str_word_count($log->notes ?? '') }} words</span>
                                        @if($log->is_favorite)
                                            <span class="text-rose-400">❤️ Favorite</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-800/20 border border-dashed border-slate-700 rounded-3xl">
                            <p class="text-sm text-slate-500 font-light">No diary entries yet.</p>
                            <button onclick="openCreateModal()" 
                                    class="mt-4 px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg text-sm font-semibold transition">
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
            {{-- end sidebar --}}

        </div>
    </div>

<script>
function showDayEntries(date) {
    window.location.href = '{{ route("diary.index") }}?date=' + date;
}

function deleteDiaryLog(id) {
    if (!confirm('Delete this diary entry?')) return;
    
    fetch(`/diary/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    })
    .catch(err => {
        alert('Error deleting diary log');
        console.error(err);
    });
}
</script>
{{-- Include modal --}}
@include('components.diary-log-modal')

@endsection