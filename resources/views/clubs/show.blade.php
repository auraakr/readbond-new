@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">
    <div class="max-w-5xl mx-auto">
        
        {{-- ─── NAVIGATION & NAVIGATION DROPDOWN ─── --}}
        <div class="mb-8 flex justify-between items-center border-b border-slate-800 pb-4">
            <a href="{{ route('clubs.index') }}" class="text-slate-400 hover:text-white transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali ke Explore
            </a>
            
            {{-- Dropdown Pilihan Menu Club --}}
            <div class="relative">
                <select onchange="location = this.value;" class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm py-1.5 pl-3 pr-8 text-xs font-semibold outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer transition appearance-none">
                    <option value="#" selected>Club's Home</option>
                    @if($isModerator)
                        <option value="{{ route('clubs.edit', $club->slug) }}">Club's Settings (Admin)</option>
                    @endif
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- ─── CLUB PROFILE HEAD (Grid Atas Sesuai Wireframe) ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start mb-10">
            
            {{-- Sisi Kiri: Cover & Tombol Join/Leave Dinamis --}}
            <div class="col-span-1 flex flex-col items-center w-full">
                <div class="w-full aspect-[3/4] bg-slate-800 border border-slate-700 rounded-sm relative flex items-center justify-center mb-3 group transition-all">
                    {{-- Efek Garis Silang Wireframe --}}
                    <svg class="absolute inset-0 w-full h-full text-slate-700/20 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                        <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5"/>
                        <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5"/>
                    </svg>
                    
                    @if($club->cover_image)
                        <img src="{{ asset('storage/' . $club->cover_image) }}" class="absolute inset-0 w-full h-full object-cover rounded-sm z-10">
                    @else
                        <span class="text-slate-600 text-xs font-bold tracking-wider z-10 uppercase text-center px-2">{{ $club->name }}</span>
                    @endif
                </div>
                
                {{-- Tombol AJAX Join / Joined Modifikasi Purple Premium --}}
                @auth
                    <button onclick="toggleJoinClub({{ $club->id }})" 
                            id="club-join-btn"
                            class="w-full text-center py-2 text-xs font-bold rounded-sm tracking-wide transition duration-200
                            {{ $isMember ? 'bg-slate-800 text-purple-400 border border-purple-500/30 hover:bg-rose-950/40 hover:text-rose-400 hover:border-rose-500/50' : 'bg-purple-600 text-white hover:bg-purple-500 shadow-md shadow-purple-950/40' }}">
                        {{ $isMember ? '✓ Joined' : '+ Join' }}
                    </button>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center block py-2 text-xs font-bold bg-purple-600 text-white rounded-sm hover:bg-purple-500 transition">+ Join</a>
                @endauth
            </div>

            {{-- Sisi Kanan: Metadata Informasi & Aturan Klub --}}
            <div class="col-span-1 md:col-span-3 text-sm text-slate-300 space-y-3">
                <h2 class="text-2xl font-black text-white tracking-tight leading-none">{{ $club->name }}</h2>
                <p class="italic text-slate-400 text-sm">"{{ $club->description }}"</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs border-t border-slate-800/60">
                    <div>
                        <span class="font-bold block text-slate-500 uppercase tracking-wider mb-0.5">Category</span>
                        <p class="text-slate-300 font-medium">{{ $club->category }}</p>
                    </div>
                    <div>
                        <span class="font-bold block text-slate-500 uppercase tracking-wider mb-0.5">Community Weight</span>
                        <p class="text-slate-300 font-medium">{{ number_format($club->members_count, 0, ',', '.') }} members</p>
                    </div>
                </div>

                <div class="text-xs pt-1">
                    <span class="font-bold block text-slate-500 uppercase tracking-wider mb-1">Rules</span>
                    <p class="leading-relaxed text-slate-400 bg-slate-850 border border-slate-800/80 p-3 rounded-sm">
                        {{ $club->rules ?? 'Please be courteous to other book club members. No specific custom rules added.' }}
                    </p>
                </div>

                <div class="text-xs pt-1">
                    <span class="font-bold block text-slate-500 uppercase tracking-wider mb-1">Tags</span>
                    <div class="flex gap-1.5 flex-wrap">
                        <span class="bg-slate-800 text-purple-400 border border-purple-900/50 text-[10px] font-bold px-2.5 py-0.5 rounded-sm">#{{ $club->category }}</span>
                        <span class="bg-slate-800 text-slate-400 border border-slate-700/60 text-[10px] font-bold px-2.5 py-0.5 rounded-sm">#ReadbondVibe</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── SECTION: CURRENTLY READING ─── --}}
        <div class="mb-10 border-t border-slate-800 pt-6">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Currently reading</h3>
            
            @if($club->currentBook)
                <div class="flex gap-4 bg-slate-850 border border-slate-800 p-4 rounded-sm">
                    <div class="w-16 h-24 bg-slate-800 border border-slate-700 rounded-sm relative flex-shrink-0 flex items-center justify-center overflow-hidden">
                        <svg class="absolute inset-0 w-full h-full text-slate-700/20 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                            <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5"/>
                            <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5"/>
                        </svg>
                        @if($club->currentBook->cover)
                            <img src="{{ $club->currentBook->cover }}" class="absolute inset-0 w-full h-full object-cover">
                        @endif
                    </div>
                    <div class="text-xs text-slate-300 flex-1">
                        <h4 class="font-bold text-white text-sm">{{ $club->currentBook->title }}</h4>
                        <p class="text-slate-500 mt-0.5">by {{ $club->currentBook->author ?? 'Unknown Author' }}</p>
                        <p class="mt-2 italic text-slate-400 bg-slate-900/40 p-2 border-l-2 border-purple-500 rounded-r-sm">
                            "{{ $club->current_book_reason ?? 'Membaca bersama agenda pekan ini.' }}"
                        </p>
                        @if($club->current_book_finish_date)
                            <p class="mt-2 text-purple-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Finish date {{ \Carbon\Carbon::parse($club->current_book_finish_date)->diffInDays() }} days left
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-500 italic">Belum ada buku yang diset untuk dibaca bersama saat ini.</p>
            @endif
        </div>

        {{-- ─── SECTION: DISCUSSIONS BOARD ─── --}}
        <div class="border-t border-slate-800 pt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Discussions Board</h3>
                @if($isMember || $isModerator)
                    <button class="inline-flex items-center gap-1 text-purple-400 hover:text-purple-300 text-xs font-bold transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Thread
                    </button>
                @endif
            </div>

            {{-- List Obrolan Diskusi --}}
            <div class="divide-y divide-slate-800 border-t border-b border-slate-800">
                @forelse($discussions as $discussion)
                    <div class="py-3.5 flex justify-between items-center group">
                        <div class="min-w-0 pr-4">
                            <a href="#" class="block text-sm font-semibold text-white group-hover:text-purple-300 transition truncate">
                                {{ $discussion->title }}
                            </a>
                            <span class="text-[10px] text-slate-500 block mt-0.5">Dimulai oleh {{ $discussion->user->username ?? 'Anggota' }}</span>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="text-xs font-bold bg-slate-800 text-slate-400 px-2.5 py-1 rounded-sm border border-slate-700/50">
                                {{ $discussion->posts_count ?? 0 }} posts
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic py-4 text-center">Belum ada topik diskusi dimulai. Jadilah yang pertama berkomentar!</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

@auth
<script>
async function toggleJoinClub(clubId) {
    const joinBtn = document.getElementById('club-join-btn');
    
    // Cegah double-click saat proses request sedang berjalan
    if (joinBtn.disabled) return;
    joinBtn.disabled = true;

    try {
        const response = await fetch(`/clubs/${clubId}/toggle-join`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.success) {
            if (data.joined) {
                // Jika user berhasil bergabung (State: Joined)
                joinBtn.textContent = '✓ Joined';
                joinBtn.className = "w-full text-center py-2 text-xs font-bold rounded-sm tracking-wide transition duration-200 bg-slate-800 text-purple-400 border border-purple-500/30 hover:bg-rose-950/40 hover:text-rose-400 hover:border-rose-500/50";
            } else {
                // Jika user keluar dari club (State: Not Joined)
                joinBtn.textContent = '+ Join';
                joinBtn.className = "w-full text-center block py-2 text-xs font-bold bg-purple-600 text-white rounded-sm hover:bg-purple-500 transition shadow-md shadow-purple-950/40";
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan, silakan coba lagi.');
    } finally {
        // Kembalikan status tombol agar bisa diklik lagi
        joinBtn.disabled = false;
    }
}
</script>
@endauth

@endsection