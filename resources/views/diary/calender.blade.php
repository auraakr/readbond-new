@extends('layouts.main')

@section('title', 'Reading Calendar')

@section('content')

<div class="min-h-screen bg-[#0a0a0f] text-white pb-20">
    <div class="max-w-6xl mx-auto px-6 py-6">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold">Reading Calendar</h1>
                <p class="text-slate-500 text-sm mt-1">Track your daily reading habits</p>
            </div>
            <div class="flex items-center gap-2">
                <button class="p-2 hover:bg-slate-800 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <span class="text-lg font-semibold px-4">{{ now()->format('F Y') }}</span>
                <button class="p-2 hover:bg-slate-800 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
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
                    $startOfMonth = now()->startOfMonth();
                    $endOfMonth = now()->endOfMonth();
                    $startDay = $startOfMonth->dayOfWeek;
                    $daysInMonth = $startOfMonth->daysInMonth;
                @endphp

                {{-- Empty cells before month starts --}}
                @for($i = 0; $i < $startDay; $i++)
                <div class="aspect-square border-b border-r border-white/5 bg-slate-800/30"></div>
                @endfor

                {{-- Days of month --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $date = now()->setDay($day);
                    $isToday = $date->isToday();
                    $hasEntry = false; // Check if user has diary entry for this day
                @endphp
                <div class="aspect-square border-b border-r border-white/5 p-2 hover:bg-slate-800 transition group cursor-pointer
                            {{ $isToday ? 'bg-purple-600/20 border-purple-500' : '' }}">
                    <div class="flex flex-col h-full">
                        <span class="text-sm font-semibold {{ $isToday ? 'text-purple-400' : 'text-white' }}">
                            {{ $day }}
                        </span>
                        @if($hasEntry)
                        <div class="flex-1 flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        </div>
                        @endif
                    </div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Reading Streak Info --}}
        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="bg-slate-900 rounded-xl p-4 border border-white/5">
                <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Current Streak</p>
                <p class="text-3xl font-bold text-orange-400">{{ Auth::user()->reading_streak ?? 0 }} 🔥</p>
            </div>
            <div class="bg-slate-900 rounded-xl p-4 border border-white/5">
                <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">This Month</p>
                <p class="text-3xl font-bold text-purple-400">12</p>
            </div>
            <div class="bg-slate-900 rounded-xl p-4 border border-white/5">
                <p class="text-slate-500 text-xs uppercase tracking-wider mb-2">Total Days</p>
                <p class="text-3xl font-bold text-blue-400">156</p>
            </div>
        </div>
    </div>
</div>

@endsection