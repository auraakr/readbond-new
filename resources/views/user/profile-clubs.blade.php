@extends('layouts.profile')

@section('title', $user->username . "'s Book Clubs")

@section('content')

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

<div class="min-h-screen bg-[#0a0a0f] text-white" style="font-family:'DM Sans',sans-serif;">

   <div class="max-w-6xl mx-auto px-6 py-8">
        <p class="text-[10px] py-3 font-semibold uppercase tracking-widest text-slate-500">Book Clubs</p>
        
        {{-- Tombol Bikin Club Baru --}}
        <a href="{{ route('clubs.create') }}"
            class="inline-flex items-center gap-1.5 mt-2 text-purple-400 hover:text-purple-300 text-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Establish a new book club
        </a>

        @forelse($clubs as $club)
        <div class="border-b border-slate-800 py-5 lg:py-6 hover:border-slate-700 transition group">
            <div class="flex flex-col sm:flex-row sm:items-center gap-5">

                {{-- Gambar Cover Club Buku (Rasio 3:4) --}}
                <div class="w-20 lg:w-24 aspect-[3/4] bg-slate-800 border border-slate-700 rounded-sm overflow-hidden shrink-0 shadow-md">
                    @if($club->cover_image)
                        <img src="{{ asset('storage/' . $club->cover_image) }}" alt="{{ $club->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        {{-- Placeholder jika tidak ada cover --}}
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-b from-slate-800 to-slate-900 text-slate-600 p-2 text-center">
                            <svg class="w-6 h-6 opacity-30 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500 truncate w-full">{{ $club->name }}</span>
                        </div>
                    @endif
                </div>

                {{-- Informasi Detail Club --}}
                <div class="flex-1 min-w-0 lg:pl-2">
                    <div class="flex flex-col gap-1.5">
                        
                        {{-- Barisan Badges Status --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[9px] bg-purple-950/60 text-purple-400 border border-purple-900/50 px-2 py-0.5 rounded-sm uppercase font-black tracking-wider">
                                {{ $club->category }}
                            </span>
                            
                            @if($club->visibility === 'private')
                                <span class="text-[9px] bg-red-950/40 text-red-400 border border-red-900/40 px-2 py-0.5 rounded-sm uppercase font-bold tracking-wider">
                                    🔒 Private
                                </span>
                            @else
                                <span class="text-[9px] bg-slate-800 text-slate-400 border border-slate-700/60 px-2 py-0.5 rounded-sm uppercase font-bold tracking-wider">
                                    🌍 Public
                                </span>
                            @endif

                            {{-- Label penanda jika user saat ini adalah pemilik/moderator utama klub tersebut --}}
                            @if(Auth::check() && $club->moderator_id === Auth::id())
                                <span class="text-[9px] bg-amber-950/50 text-amber-400 border border-amber-900/40 px-2 py-0.5 rounded-sm uppercase font-bold tracking-wider">
                                    Host
                                </span>
                            @endif
                        </div>

                        {{-- Nama & Deskripsi Pendek --}}
                        <div>
                            <h3 class="text-white font-bold text-lg leading-tight group-hover:text-purple-300 transition">
                                <a href="{{ route('clubs.show', $club->slug) }}">{{ $club->name }}</a>
                            </h3>
                            <p class="text-slate-400 text-xs mt-1 line-clamp-2 max-w-2xl leading-relaxed">
                                {{ $club->description }}
                            </p>
                        </div>

                        {{-- Metadata Penghitung Anggota / Aktivitas --}}
                        <div class="text-slate-500 text-[11px] mt-1 flex items-center gap-3">
                            <span class="flex items-center gap-1">
                                👥 <b>{{ $club->members_count ?? $club->members->count() }}</b> members
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                📖 <b>{{ $club->books_count ?? $club->books->count() }}</b> reading logs
                            </span>
                        </div>
                    </div>

                    {{-- Tombol Navigasi Masuk --}}
                    <div class="mt-4">
                        <a href="{{ route('clubs.show', $club->slug) }}"
                            class="inline-flex items-center gap-1.5 text-purple-400 hover:text-purple-300 text-xs font-bold uppercase tracking-wider transition group/link">
                            Enter Club Space
                            <svg class="w-3.5 h-3.5 transition-transform group-hover/link:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        @empty
            <div class="text-center py-12 border border-dashed border-slate-800 rounded-sm mt-4">
                <p class="text-sm text-slate-500 italic">Belum bergabung atau mendirikan club buku manapun.</p>
            </div>
        @endforelse
    </div>

</div>

@endsection