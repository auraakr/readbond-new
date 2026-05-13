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
                       class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm
                              py-2.5 pl-10 pr-4 focus:ring-2 focus:ring-purple-500 outline-none
                              placeholder-slate-500 transition">
            </div>
            <select class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2.5
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
                <h2 class="text-xl font-bold text-white mt-0.5">Featured Collection ({{ count($featured) }})</h2>
            </div>
        </div>

        <div class="space-y-8">
            @forelse($featured as $col)
            <div class="border-b border-slate-700 py-5 lg:py-6
                        hover:border-slate-600 transition group">
                <div class="flex flex-col lg:flex-row lg:items-center gap-5">

                    {{-- Book stack preview --}}
                    <div class="flex gap-2 shrink-0 relative">
                        @foreach($col->books as $i => $b)
                            <div class="w-20 lg:w-24 aspect-[3/4] rounded-sm overflow-hidden
                                        bg-slate-700 border border-slate-600 shrink-0
                                        transition-transform duration-300
                                        {{ $i > 2 ? 'hidden sm:block' : '' }}"
                                 style="{{ $i > 0 ? 'margin-left: -16px; z-index:'.($i).';' : '' }} position: relative; z-index: {{ 4 - $i }}">
                                @if($b->cover)
                                    <img src="{{ $b->cover }}" alt="{{ $b->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600">
                                        <svg class="w-8 h-8 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Count bubble --}}
                        @if($col->books_count > 4)
                        <div class="absolute -right-3 -bottom-2 bg-slate-900 border border-slate-600
                                    rounded-full px-2.5 py-0.5 text-xs text-slate-300 font-medium z-10">
                            +{{ $col->books_count - 4 }}
                        </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0 lg:pl-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-white font-bold text-lg leading-tight group-hover:text-purple-300 transition">
                                    {{ $col->title }}
                                </h3>
                                <p class="text-slate-500 text-sm mt-0.5">
                                    oleh <span class="text-slate-400">{{ $col->curator->username ?? 'Admin' }}</span>
                                    · {{ $col->books_count }} buku
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('collections.show', $col->id) }}"
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
            @empty
                <p class="text-slate-500 text-center py-8">Tidak ada featured collections</p>
            @endforelse
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
            @forelse($popular as $col)
            <a href="{{ route('collections.show', $col->id) }}" class="group flex flex-col">
                {{-- Cover collage --}}
                <div class="aspect-square bg-slate-800 rounded-sm border border-slate-700 overflow-hidden
                            mb-3 relative grid grid-cols-2 gap-0.5 p-0.5
                            group-hover:border-purple-500 transition-all duration-300
                            group-hover:shadow-lg group-hover:shadow-purple-900/30">
                    @foreach($col->books->take(4) as $book)
                        <div class="bg-slate-700 rounded-sm flex items-center justify-center overflow-hidden">
                            @if($book->cover)
                                <img src="{{ $book->cover }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-5 h-5 text-slate-600 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            @endif
                        </div>
                    @endforeach
                </div>

                <h3 class="text-white text-sm font-semibold leading-tight truncate
                           group-hover:text-purple-300 transition">
                    {{ $col->title }}
                </h3>
                <p class="text-slate-500 text-xs mt-0.5 truncate">by {{ $col->curator->username ?? 'Admin' }}</p>
                <div class="flex items-center gap-3 mt-1.5 text-slate-600 text-xs">
                    <span>{{ $col->books_count }} buku</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        {{ $col->likes_count ?? 0 }}
                    </span>
                </div>
            </a>
            @empty
                <p class="text-slate-500 text-center py-8 col-span-full">Tidak ada popular collections</p>
            @endforelse
        </div>
    </div>

</div>
@endsection