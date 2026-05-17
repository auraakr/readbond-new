@extends('layouts.profile')

@section('title', 'My Reading Journals')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10 text-white min-h-screen">
    
    {{-- ── HEADER SECTION ── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-white">My Journals</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">🔥 {{ $streak }} Days Reading Streak</p>
        </div>
        <button onclick="openCreateModal()" class="w-10 h-10 flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-full transition-all shadow-lg shadow-purple-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
    </div>

    {{-- ── HORIZONTAL CALENDAR ROW ── --}}
    <div class="flex items-center gap-3 overflow-x-auto pb-4 mb-8 scrollbar-hide">
        @php
            $startOfWeek = now()->startOfWeek();
        @endphp
        
        @for($i = 0; $i < 7; $i++)
            @php 
                $date = $startOfWeek->copy()->addDays($i);
                $isCurrentDay = $date->isToday();
            @endphp
            
            <a href="?date={{ $date->format('Y-m-d') }}" 
               class="flex-1 min-w-[50px] flex flex-col items-center py-3 px-2 rounded-2xl transition border {{ $isCurrentDay ? 'bg-slate-800 border-purple-600/50 shadow-md shadow-purple-600/5' : 'bg-slate-900/40 border-white/5 opacity-60 hover:opacity-100' }}">
                
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

    {{-- ── VERTICAL TIMELINE LOGS ── --}}
    <div class="relative border-l-2 border-dashed border-slate-800 ml-4 pl-8 space-y-6">
        @forelse($diaryLogs as $log)
            <div class="relative group">
                
                {{-- Penunjuk Waktu / Samping (Timeline Bullet) --}}
                <div class="absolute -left-[55px] top-8 bg-slate-900 px-1 text-[11px] font-bold text-slate-500 uppercase tracking-tighter">
                    {{ $log->read_date->format('H:i') == '00:00' ? $log->read_date->format('M d') : $log->read_date->format('g A') }}
                </div>
                <div class="absolute -left-[37px] top-[18px] w-2 h-2 rounded-full bg-slate-700 group-hover:bg-purple-500 transition-colors duration-300"></div>

                {{-- Card Log Jurnal --}}
                <div class="bg-slate-800/40 p-5 rounded-3xl border border-white/5 shadow-sm hover:border-white/10 hover:bg-slate-800/60 transition-all duration-300 flex items-start gap-4">
                    
                    {{-- Visual Mood Indicator Icon --}}
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl bg-slate-800 border border-slate-700 shadow-inner">
                        @switch(strtolower($log->mood))
                            @case('happy') 😊 @break
                            @case('sad') 😢 @break
                            @case('excited') 🤩 @break
                            @case('tired') 🥱 @break
                            @default 📖
                        @endswitch
                    </div>

                    {{-- Konten Jurnal --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h3 class="font-bold text-sm text-slate-200 tracking-tight leading-tight group-hover:text-purple-400 transition-colors duration-300">
                                {{ $log->book->title ?? 'Untitled Log' }}
                            </h3>
                            
                            {{-- Dropdown / Action Buttons --}}
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center gap-1">
                                <button onclick="openEditModal({{ $log->id }})" class="p-1 text-slate-500 hover:text-purple-400 rounded-md hover:bg-slate-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <form action="{{ route('diary.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Delete this log?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="p-1 text-slate-500 hover:text-red-400 rounded-md hover:bg-slate-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 font-light mb-2">
                            Progress: <span class="font-semibold text-purple-400">{{ $log->current_page ?? 0 }}</span> pg 
                            @if($log->pages_read) <span class="text-emerald-400">(+{{ $log->pages_read }} read)</span> @endif
                        </p>

                        <p class="text-xs text-slate-300 font-light leading-relaxed line-clamp-3">
                            {{ $log->notes ?? 'No thoughts recorded today.' }}
                        </p>
                        
                        <div class="flex items-center gap-2 mt-3 text-[10px] text-slate-500 font-medium">
                            <span>📄 {{ str_word_count($log->notes) }} total words</span>
                            @if($log->is_favorite)
                                <span class="text-rose-400">❤️ Favorite Entry</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-12 bg-slate-800/20 border border-dashed border-slate-700 rounded-3xl">
                <p class="text-sm text-slate-500 font-light">Today was a blank page. No logs found.</p>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    <div class="mt-8 text-purple-500">
        {{ $diaryLogs->links() }}
    </div>
</div>

{{-- Include file modal --}}
@include('components.diary-log-modal')

@endsection