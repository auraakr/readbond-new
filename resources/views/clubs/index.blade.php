@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">

    {{-- ─── PAGE HEADER ─── --}}
    <div class="max-w-6xl mx-auto mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <p class="text-slate-500 text-xs uppercase tracking-widest font-medium mb-1">Readbond</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Book Clubs</h1>
            <a href="{{ route('clubs.create') }}"
               class="inline-flex items-center gap-1.5 mt-2 text-purple-400 hover:text-purple-300 text-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat book club milikmu sendiri
            </a>
        </div>

        {{-- Search + Filter Kategori --}}
        <form action="{{ route('clubs.index') }}" method="GET" class="flex gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-64">
                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari book club..."
                       class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm
                              py-2.5 pl-10 pr-4 focus:ring-2 focus:ring-purple-500 outline-none
                              placeholder-slate-500 transition">
            </div>
            <select name="category" onchange="this.form.submit()"
                    class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2.5
                           text-sm outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer transition">
                <option value="">Kategori</option>
                <option value="Romance" {{ request('category') == 'Romance' ? 'selected' : '' }}>Romance</option>
                <option value="Thriller" {{ request('category') == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                <option value="Fantasy" {{ request('category') == 'Fantasy' ? 'selected' : '' }}>Fantasy</option>
                <option value="Nonfiction" {{ request('category') == 'Nonfiction' ? 'selected' : '' }}>Nonfiction</option>
                <option value="Sci-Fi" {{ request('category') == 'Sci-Fi' ? 'selected' : '' }}>Sci-Fi</option>
                <option value="Just for fun" {{ request('category') == 'Just for fun' ? 'selected' : '' }}>Just for fun</option>
            </select>
        </form>
    </div>

    {{-- ─── FEATURED CLUBS (Rekomendasi Utama dengan tumpukan Cover Bersama) ─── --}}
    <div class="max-w-6xl mx-auto mb-14">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-slate-500 text-[11px] uppercase tracking-widest">Aktivitas Tinggi</p>
                <h2 class="text-xl font-bold text-white mt-0.5">Featured Clubs</h2>
            </div>
        </div>

        <div class="space-y-8">
            {{-- Mengambil 3 Club teratas yang memiliki member banyak/aktif --}}
            @forelse($clubs->take(3) as $club)
            <div class="border-b border-slate-700/60 {{ $loop->last ? '' : 'pb-6' }} py-2 hover:border-slate-600 transition group">
                <div class="flex flex-col lg:flex-row lg:items-start gap-3">

                    {{-- Mini Book Stack / Avatar Identitas Klub --}}
                    <div class="flex shrink-0 relative">
                        <div class="w-20 lg:w-24 aspect-[3/4] rounded-sm overflow-hidden bg-gradient-to-br from-purple-900 to-slate-800 border border-slate-700 shadow-md flex flex-col items-center justify-center p-2 text-center relative group-hover:border-purple-500 transition">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-purple-400 mb-1">Club</span>
                            <span class="text-white font-black text-xs line-clamp-3 px-1 leading-tight">{{ $club->name }}</span>
                            
                            {{-- Dekorasi silang samar estetik jika cover image kosongan --}}
                            @if($club->cover_image)
                                <img src="{{ asset('storage/' . $club->cover_image) }}" class="absolute inset-0 w-full h-full object-cover rounded-sm z-10">
                            @else
                                <svg class="absolute inset-0 w-full h-full text-slate-700/20 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                    <line x1="0" y1="0" x2="100" y2="100" stroke-width="1"/>
                                    <line x1="100" y1="0" x2="0" y2="100" stroke-width="1"/>
                                </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Informasi Meta & Deskripsi Club --}}
                    <div class="flex-1 min-w-0 lg:pl-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="bg-purple-950/60 border border-purple-800 text-purple-400 text-[10px] font-bold px-2 py-0.5 rounded-full tracking-wide">
                                    #{{ $club->category }}
                                </span>
                                <h3 class="text-white font-bold text-lg leading-tight mt-2 group-hover:text-purple-300 transition">
                                    {{ $club->name }}
                                </h3>
                                <p class="text-slate-500 text-sm mt-1">
                                    Moderator <span class="text-slate-400">{{ $club->moderator->name ?? 'Admin' }}</span>
                                    · <span class="font-semibold text-slate-300">{{ number_format($club->members_count, 0, ',', '.') }}</span> Anggota
                                </p>
                                <p class="text-slate-400 text-sm mt-2 max-w-3xl line-clamp-2 leading-relaxed">
                                    {{ $club->description }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('clubs.show', $club->slug) }}"
                           class="inline-flex items-center gap-1.5 mt-4 text-purple-400 hover:text-purple-300 text-sm font-medium transition group/link">
                            Lihat aktivitas baca & room diskusi
                            <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @empty
                <p class="text-slate-500 text-center py-8">Belum ada book club rekomendasi tersedia.</p>
            @endforelse
        </div>
    </div>

    {{-- ─── POPULAR CLUBS (Grid Cards Layaknya Tampilan Kolase Koleksi) ─── --}}
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-slate-500 text-[11px] uppercase tracking-widest">Komunitas Berkembang</p>
                <h2 class="text-xl font-bold text-white mt-0.5">Popular Clubs</h2>
            </div>
        </div>

        {{-- Grid Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($clubs as $club)
            <a href="{{ route('clubs.show', $club->slug) }}" class="group flex flex-col">
                
                {{-- Kotak Identitas/Kolase Gambar Estetik Club --}}
                <div class="aspect-square bg-slate-800 rounded-sm border border-slate-700 overflow-hidden
                            mb-3 relative flex flex-col items-center justify-between p-3.5
                            group-hover:border-purple-500 transition-all duration-300
                            group-hover:shadow-lg group-hover:shadow-purple-900/30">
                    
                    <div class="w-full flex justify-between items-center">
                        <span class="text-[9px] font-bold text-purple-400 bg-purple-950/50 px-2 py-0.5 rounded border border-purple-900/50">
                            {{ $club->category }}
                        </span>
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                    </div>

                    {{-- Judul Besar Tengah Card --}}
                    <div class="text-center w-full px-1">
                        <span class="text-white font-extrabold text-sm tracking-tight line-clamp-3 group-hover:text-purple-200 transition">
                            {{ $club->name }}
                        </span>
                    </div>

                    {{-- Informasi Jumlah Post/Thread Diskusi di Bawah Card --}}
                    <div class="w-full text-left">
                        <p class="text-[10px] text-slate-500 truncate">
                            Active Reading Space
                        </p>
                    </div>

                    {{-- Garis Silang Khas Wireframe Minimalis --}}
                    @if($club->cover_image)
                        <img src="{{ asset('storage/' . $club->cover_image) }}" class="absolute inset-0 w-full h-full object-cover rounded-sm z-10">
                    @else
                    <svg class="absolute inset-0 w-full h-full text-slate-700/10 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                        <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5"/>
                        <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5"/>
                    </svg>
                    @endif
                </div>

                {{-- Judul & Metadata Luar Card --}}
                <h3 class="text-white text-sm font-semibold leading-tight truncate group-hover:text-purple-300 transition">
                    {{ $club->name }}
                </h3>
                <p class="text-slate-500 text-xs mt-0.5 truncate">by {{ $club->moderator->name ?? 'Admin' }}</p>
                
                <div class="flex items-center gap-3 mt-1.5 text-slate-600 text-xs">
                    <span class="flex items-center gap-1 font-medium text-slate-400">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        {{ number_format($club->members_count, 0, ',', '.') }}
                    </span>
                    <span class="text-slate-700">•</span>
                    <span class="text-purple-400/80 font-medium hover:underline text-[11px]">Join &rarr;</span>
                </div>
            </a>
            @empty
                <p class="text-slate-500 text-center py-8 col-span-full">Tidak ada book club yang terdaftar</p>
            @endforelse
        </div>
    </div>

</div>
@endsection