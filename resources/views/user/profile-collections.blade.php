@extends('layouts.profile')

@section('title', $user->username . "'s Collections")

@section('content')

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

<div class="min-h-screen bg-[#0a0a0f] text-white" style="font-family:'DM Sans',sans-serif;">

   <div class="max-w-6xl mx-auto px-6 py-8">
        <p class="text-[10px] py-3 font-semibold uppercase tracking-widest text-slate-500">Collections</p>
        <a href="{{ route('collections.create') }}"
            class="inline-flex items-center gap-1.5 mt-2 text-purple-400 hover:text-purple-300 text-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Buat koleksimu sendiri
        </a>
        @forelse($collections as $col)
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

@endsection