@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">
    <div class="max-w-5xl mx-auto">

        {{-- ─── Header ─── --}}
        <div class="mb-10">
            <p class="text-slate-500 text-xs uppercase tracking-widest font-medium mb-1">Akunmu</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Reading Log</h1>
            <p class="text-slate-500 text-sm mt-1">Semua aktivitas bacaanmu dalam satu tempat.</p>
        </div>

        {{-- ─── Stats Row ─── --}}
        <div class="grid grid-cols-3 gap-4 mb-10">
            @php
                $statItems = [
                    ['label' => 'Selesai Dibaca', 'count' => $counts['finished'],     'color' => 'text-green-400',  'bg' => 'bg-green-500/10 border-green-500/20'],
                    ['label' => 'Sedang Dibaca',  'count' => $counts['reading'],      'color' => 'text-blue-400',   'bg' => 'bg-blue-500/10 border-blue-500/20'],
                    ['label' => 'Ingin Dibaca',   'count' => $counts['want_to_read'], 'color' => 'text-purple-400', 'bg' => 'bg-purple-500/10 border-purple-500/20'],
                ];
            @endphp
            @foreach($statItems as $stat)
                <div class="rounded-xl border {{ $stat['bg'] }} px-5 py-4 text-center">
                    <p class="text-2xl font-black {{ $stat['color'] }}">{{ $stat['count'] }}</p>
                    <p class="text-slate-400 text-xs mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ─── Filter Tabs ─── --}}
        <div class="flex gap-1 border-b border-slate-700 mb-8">
            @foreach([
                'all'          => 'Semua',
                'reading'      => 'Sedang Dibaca',
                'want_to_read' => 'Ingin Dibaca',
                'finished'     => 'Selesai',
            ] as $val => $label)
                <a href="{{ route('reading-log.index', ['status' => $val === 'all' ? null : $val]) }}"
                   class="px-5 py-3 text-sm font-medium border-b-2 -mb-px transition
                          {{ ($activeStatus ?? 'all') === $val
                              ? 'text-white border-purple-500'
                              : 'text-slate-500 border-transparent hover:text-slate-300' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- ─── Book List ─── --}}
        @if($logs->isEmpty())
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <svg class="w-16 h-16 text-slate-700 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="text-slate-400 text-lg font-semibold mb-2">Belum ada buku di sini</h3>
                <p class="text-slate-600 text-sm mb-6">Mulai tandai buku yang ingin kamu baca!</p>
                <a href="{{ route('books.index') }}"
                   class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-sm transition">
                    Jelajahi Buku
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($logs as $log)
                    @php
                        $book = $log->book;
                        $statusConfig = [
                            'finished'     => ['label' => 'Selesai',       'class' => 'bg-green-500/15 text-green-400 border-green-500/30'],
                            'reading'      => ['label' => 'Sedang Dibaca', 'class' => 'bg-blue-500/15 text-blue-400 border-blue-500/30'],
                            'want_to_read' => ['label' => 'Ingin Dibaca',  'class' => 'bg-purple-500/15 text-purple-400 border-purple-500/30'],
                        ];
                        $status = $statusConfig[$log->status];
                    @endphp
                    <div class="group bg-slate-800/60 border border-slate-700 rounded-2xl p-4
                                hover:border-slate-600 transition flex gap-4 items-start">

                        {{-- Cover --}}
                        <a href="{{ route('books.show', $book->external_id) }}" class="shrink-0">
                            <div class="w-14 aspect-[3/4] rounded-lg overflow-hidden bg-slate-700
                                        border border-slate-600 group-hover:border-purple-500 transition">
                                @if($book->cover)
                                    <img src="{{ $book->cover }}" alt="{{ $book->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-600 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('books.show', $book->external_id) }}"
                                       class="text-white font-semibold text-sm leading-tight
                                              hover:text-purple-300 transition line-clamp-1">
                                        {{ $book->title }}
                                    </a>
                                    <p class="text-slate-500 text-xs mt-0.5">
                                        {{ $book->author_name ?? 'Unknown Author' }}
                                    </p>
                                </div>

                                {{-- Status badge --}}
                                <span class="shrink-0 px-2.5 py-1 rounded-full text-[11px] font-medium border
                                             {{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </div>

                            {{-- Tanggal & catatan --}}
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                @if($log->started_at)
                                    <span>Mulai: {{ $log->started_at->format('d M Y') }}</span>
                                @endif
                                @if($log->finished_at)
                                    <span>Selesai: {{ $log->finished_at->format('d M Y') }}</span>
                                @endif
                                @if($log->started_at && $log->finished_at)
                                    @php
                                        $days = $log->started_at->diffInDays($log->finished_at);
                                    @endphp
                                    <span class="text-slate-600">{{ $days }} hari</span>
                                @endif
                            </div>

                            @if($log->notes)
                                <p class="mt-2 text-slate-400 text-xs leading-relaxed italic line-clamp-2">
                                    "{{ $log->notes }}"
                                </p>
                            @endif
                        </div>

                        {{-- Action buttons --}}
                        <div class="shrink-0 flex flex-col gap-2 items-end">
                            {{-- Edit --}}
                            <a href="{{ route('books.show', $book->external_id) }}"
                               class="text-slate-600 hover:text-purple-400 transition"
                               title="Edit log">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('reading-log.destroy', $log->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus log buku ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-slate-600 hover:text-red-400 transition"
                                        title="Hapus log">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection