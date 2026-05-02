@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">

    {{-- ─── PAGE HEADER ─── --}}
    <div class="max-w-6xl mx-auto mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="text-slate-500 text-xs uppercase tracking-widest font-medium mb-1">Readbond</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Collections</h1>
            <a href="{{ route('collections.create') }}"
               class="inline-flex items-center gap-1.5 mt-2 text-purple-400 hover:text-purple-300 text-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat koleksimu sendiri
            </a>
        </div>

        {{-- Search + Filter --}}
        <div class="flex gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" placeholder="Cari collection..."
                       class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-lg
                              py-2.5 pl-10 pr-4 focus:ring-2 focus:ring-purple-500 outline-none
                              placeholder-slate-500 transition">
            </div>
            <select class="bg-slate-800 text-slate-300 border border-slate-700 rounded-lg px-4 py-2.5
                           text-sm outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer transition">
                <option value="">Genre</option>
                <option>Fiction</option>
                <option>Fantasy</option>
                <option>Mystery</option>
                <option>Romance</option>
                <option>History</option>
                <option>Sci-Fi</option>
            </select>
        </div>
    </div>

    {{-- ─── FEATURED COLLECTIONS ─── --}}
    <div class="max-w-6xl mx-auto mb-14">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-slate-500 text-[11px] uppercase tracking-widest">Pilihan Editor</p>
                <h2 class="text-xl font-bold text-white mt-0.5">Featured Collection</h2>
            </div>
        </div>

        <div class="space-y-8">
            @php
            $featured = [
                ['title' => "Readbond's Picks for 2026", 'count' => 100, 'curator' => 'readbond', 'rating' => 4.2,
                 'books' => [
                    ['cover' => null, 'title' => 'Dune'],
                    ['cover' => null, 'title' => 'Foundation'],
                    ['cover' => null, 'title' => 'Hyperion'],
                    ['cover' => null, 'title' => 'Neuromancer'],
                 ]],
                ['title' => "Best Fantasy of the Decade", 'count' => 84, 'curator' => 'fantasyclub',  'rating' => 4.5,
                 'books' => [
                    ['cover' => null, 'title' => 'The Name of the Wind'],
                    ['cover' => null, 'title' => 'Mistborn'],
                    ['cover' => null, 'title' => 'The Way of Kings'],
                    ['cover' => null, 'title' => 'Elantris'],
                 ]],
            ];
            @endphp

            @foreach($featured as $col)
            <div class="bg-slate-800/60 border border-slate-700 rounded-2xl p-5 lg:p-6
                        hover:border-slate-600 transition group">
                <div class="flex flex-col lg:flex-row lg:items-center gap-5">

                    {{-- Book stack preview --}}
                    <div class="flex gap-2 shrink-0 relative">
                        @foreach($col['books'] as $i => $b)
                            <div class="w-20 lg:w-24 aspect-[3/4] rounded-lg overflow-hidden
                                        bg-slate-700 border border-slate-600 shrink-0
                                        transition-transform duration-300
                                        {{ $i > 2 ? 'hidden sm:block' : '' }}"
                                 style="{{ $i > 0 ? 'margin-left: -16px; z-index:'.($i).';' : '' }} position: relative; z-index: {{ 4 - $i }}">
                                <div class="w-full h-full flex items-center justify-center text-slate-600">
                                    <svg class="w-8 h-8 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            </div>
                        @endforeach

                        {{-- Count bubble --}}
                        <div class="absolute -right-3 -bottom-2 bg-slate-900 border border-slate-600
                                    rounded-full px-2.5 py-0.5 text-xs text-slate-300 font-medium z-10">
                            +{{ $col['count'] - 3 }}
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0 lg:pl-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-white font-bold text-lg leading-tight group-hover:text-purple-300 transition">
                                    {{ $col['title'] }}
                                </h3>
                                <p class="text-slate-500 text-sm mt-0.5">
                                    oleh <span class="text-slate-400">{{ $col['curator'] }}</span>
                                    · {{ $col['count'] }} buku
                                </p>
                            </div>
                            {{-- Star rating --}}
                            <div class="flex items-center gap-1.5 shrink-0">
                                <div class="flex text-yellow-400">
                                    @for($s = 1; $s <= 5; $s++)
                                        @if($s <= round($col['rating']))
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-slate-500 text-xs">{{ number_format($col['rating'], 1) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('collections.show', 1) }}"
                           class="inline-flex items-center gap-1.5 mt-4 text-purple-400 hover:text-purple-300
                                  text-sm font-medium transition group/link">
                            Lihat semua buku di koleksi ini
                            <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ─── POPULAR COLLECTIONS ─── --}}
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-slate-500 text-[11px] uppercase tracking-widest">Terpopuler</p>
                <h2 class="text-xl font-bold text-white mt-0.5">Popular Collection</h2>
            </div>
            <a href="#" class="inline-flex items-center gap-1.5 text-purple-400 hover:text-purple-300 text-sm transition group">
                Lihat semua
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        {{-- Grid cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @php
            $popular = [
                ['title' => 'Best YA Novel',       'count' => 1000, 'likes' => 10,  'curator' => 'auraakr'],
                ['title' => 'Dark Academia Reads', 'count' => 340,  'likes' => 87,  'curator' => 'mysticreads'],
                ['title' => 'Cozy Fantasy',        'count' => 210,  'likes' => 54,  'curator' => 'hobbithole'],
                ['title' => 'Sci-Fi Classics',     'count' => 560,  'likes' => 130, 'curator' => 'galaxyreads'],
                ['title' => 'Romance of the Year', 'count' => 180,  'likes' => 42,  'curator' => 'rosepages'],
                ['title' => 'Horror Masterpieces', 'count' => 95,   'likes' => 33,  'curator' => 'darkbound'],
                ['title' => 'Award Winners 2025',  'count' => 420,  'likes' => 201, 'curator' => 'litprize'],
                ['title' => 'Short Story Gold',    'count' => 75,   'likes' => 19,  'curator' => 'shortstacks'],
            ];
            @endphp

            @foreach($popular as $col)
            <a href="{{ route('collections.show', 1) }}" class="group flex flex-col">
                {{-- Cover collage --}}
                <div class="aspect-square bg-slate-800 rounded-xl border border-slate-700 overflow-hidden
                            mb-3 relative grid grid-cols-2 gap-0.5 p-0.5
                            group-hover:border-purple-500 transition-all duration-300
                            group-hover:shadow-lg group-hover:shadow-purple-900/30">
                    @foreach(range(1,4) as $n)
                        <div class="bg-slate-700 rounded-sm flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-600 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endforeach
                </div>

                <h3 class="text-white text-sm font-semibold leading-tight truncate
                           group-hover:text-purple-300 transition">
                    {{ $col['title'] }}
                </h3>
                <p class="text-slate-500 text-xs mt-0.5 truncate">by {{ $col['curator'] }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-slate-600 text-xs">
                    <span>{{ number_format($col['count']) }} buku</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        {{ $col['likes'] }}
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</div>
@endsection