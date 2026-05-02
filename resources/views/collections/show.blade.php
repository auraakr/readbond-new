@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen">

    {{-- ═══════════════════════════════════════
         HERO — Collection header
    ════════════════════════════════════════ --}}
    <div class="relative pt-20 overflow-hidden">
        {{-- Atmospheric backdrop --}}
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-purple-900/30 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl z-0"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-16 pt-10 pb-12">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
                <a href="{{ route('collections.index') }}" class="hover:text-slate-300 transition">Collections</a>
                <span>/</span>
                <span class="text-slate-300">Best YA Books</span>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-14 items-start">

                {{-- Book stack visual --}}
                <div class="shrink-0 mx-auto lg:mx-0">
                    <div class="relative w-52 h-52 lg:w-64 lg:h-64">
                        {{-- 4-grid collage --}}
                        <div class="w-full h-full grid grid-cols-2 gap-1.5 p-1.5
                                    bg-slate-800 border border-slate-700 rounded-2xl overflow-hidden
                                    shadow-2xl shadow-black/50">
                            @foreach(range(1,4) as $n)
                                <div class="bg-slate-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-slate-600 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                        Best YA Books
                    </h1>

                    <div class="flex items-center gap-2 mt-3">
                        <div class="w-6 h-6 rounded-full bg-purple-500/30 flex items-center justify-center text-purple-300 text-xs font-bold">A</div>
                        <span class="text-slate-400 text-sm">by <span class="text-slate-200 font-medium">auraakr</span></span>
                    </div>

                    <p class="mt-4 text-slate-400 text-sm leading-relaxed max-w-lg">
                        Collection of the most popular Young Adult Books — hand-picked for gripping storylines, unforgettable characters, and worlds you won't want to leave.
                    </p>

                    {{-- Stats row --}}
                    <div class="flex flex-wrap items-center gap-5 mt-6">
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span><strong class="text-white">1.000</strong> buku</span>
                        </div>
                        <button class="flex items-center gap-2 text-sm text-slate-400 hover:text-red-400 transition group">
                            <svg class="w-4 h-4 group-hover:fill-red-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span><strong class="text-white">1.000</strong> likes</span>
                        </button>
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span><strong class="text-white">10</strong> komentar</span>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-wrap gap-3 mt-6">
                        <button class="flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-500
                                       text-white text-sm font-semibold rounded-lg transition shadow-lg shadow-purple-900/40">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Like Koleksi
                        </button>
                        <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                       text-white text-sm font-semibold rounded-lg transition border border-slate-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════════ --}}
    <div class="max-w-6xl mx-auto px-6 lg:px-16 py-10
                flex flex-col lg:flex-row gap-10 lg:gap-14">

        {{-- ── LEFT: Books grid ── --}}
        <div class="flex-1 min-w-0">
            <h2 class="text-lg font-bold text-white mb-5 flex items-center justify-between">
                <span>Buku dalam Koleksi</span>
                <span class="text-slate-500 text-sm font-normal">1.000 buku</span>
            </h2>

            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                @php
                $books = [
                    'The Hunger Games', 'Divergent', 'The Maze Runner',
                    'Twilight', 'Percy Jackson', 'The Fault in Our Stars',
                    'An Ember in the Ashes', 'Red Queen', 'The Mortal Instruments',
                    'Six of Crows',
                ];
                @endphp
                @foreach($books as $title)
                    <a href="#" class="group flex flex-col">
                        <div class="aspect-[3/4] bg-slate-800 rounded-lg overflow-hidden mb-2
                                    border border-slate-700
                                    group-hover:border-purple-500 group-hover:-translate-y-1
                                    transition-all duration-300
                                    group-hover:shadow-lg group-hover:shadow-purple-900/30
                                    flex items-center justify-center">
                            <svg class="w-8 h-8 text-slate-600 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-white text-[11px] font-medium leading-tight truncate
                                  group-hover:text-purple-300 transition">
                            {{ $title }}
                        </p>
                        {{-- Mini stars --}}
                        <div class="flex mt-1 gap-0.5">
                            @foreach(range(1,5) as $s)
                                <svg class="w-2.5 h-2.5 {{ $s <= 4 ? 'text-yellow-400' : 'text-slate-700' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endforeach
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Load more --}}
            <button class="w-full py-3 border border-slate-700 text-slate-400 text-sm rounded-xl
                           hover:border-slate-500 hover:text-slate-200 transition">
                Muat lebih banyak buku
            </button>
        </div>

        {{-- ── RIGHT: Comments sidebar ── --}}
        <div class="w-full lg:w-80 shrink-0">

            {{-- Comment input --}}
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 mb-6">
                <p class="text-white font-semibold text-sm mb-3">Tulis Komentar</p>
                <textarea placeholder="Bagikan pendapatmu tentang koleksi ini..."
                          rows="3"
                          class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-xl
                                 px-4 py-3 resize-none outline-none focus:ring-2 focus:ring-purple-500
                                 placeholder-slate-600 transition"></textarea>
                <div class="flex justify-end mt-3">
                    <button class="flex items-center gap-2 px-5 py-2 bg-purple-600 hover:bg-purple-500
                                   text-white text-sm font-semibold rounded-lg transition
                                   shadow-md shadow-purple-900/40">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        POST
                    </button>
                </div>
            </div>

            {{-- Comments list --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">
                    Komentar · 10
                </h3>

                @php
                $comments = [
                    ['user' => 'auraakr',      'avatar' => 'A', 'color' => 'purple', 'rating' => 5, 'text' => 'I LOOOVEE MAGICCC!!'],
                    ['user' => 'mysticluna',   'avatar' => 'M', 'color' => 'blue',   'rating' => 5, 'text' => 'Enchanted by spells!'],
                    ['user' => 'spellbinder99','avatar' => 'S', 'color' => 'green',  'rating' => 5, 'text' => 'Conjuring wonders daily'],
                    ['user' => 'readworm',     'avatar' => 'R', 'color' => 'orange', 'rating' => 4, 'text' => 'Solid picks, love the curation!'],
                    ['user' => 'pageturner',   'avatar' => 'P', 'color' => 'pink',   'rating' => 5, 'text' => 'Finished 3 already this month 🔥'],
                ];
                $colorMap = [
                    'purple' => 'bg-purple-500/20 text-purple-300',
                    'blue'   => 'bg-blue-500/20 text-blue-300',
                    'green'  => 'bg-green-500/20 text-green-300',
                    'orange' => 'bg-orange-500/20 text-orange-300',
                    'pink'   => 'bg-pink-500/20 text-pink-300',
                ];
                @endphp

                @foreach($comments as $comment)
                <div class="bg-slate-800/60 border border-slate-700/60 rounded-xl p-4
                            hover:border-slate-600 transition">
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center
                                    text-xs font-bold shrink-0 {{ $colorMap[$comment['color']] }}">
                            {{ $comment['avatar'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-white text-xs font-semibold truncate">{{ $comment['user'] }}</span>
                                <div class="flex shrink-0">
                                    @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-2.5 h-2.5 {{ $s <= $comment['rating'] ? 'text-yellow-400' : 'text-slate-700' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="text-slate-300 text-sm leading-relaxed">{{ $comment['text'] }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <button class="flex items-center gap-1.5 text-slate-600 hover:text-red-400 text-xs transition group">
                            <svg class="w-3.5 h-3.5 group-hover:fill-red-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Like
                        </button>
                        <button class="text-slate-600 hover:text-red-400 text-xs transition">Report</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection