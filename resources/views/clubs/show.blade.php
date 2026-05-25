@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">
    <div class="max-w-5xl mx-auto">
        
        {{-- ─── NAVIGATION & NAVIGATION DROPDOWN ─── --}}
        <div class="mb-8 flex justify-between items-center border-b border-slate-800 pb-4">
            <a href="{{ route('clubs.index') }}" class="font-semibold uppercase tracking-wider text-slate-500 hover:text-white transition flex items-center gap-2 text-sm">
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
        <div class="mb-10 border-t border-slate-800 pt-6" x-data="{ openAddBookModal: false }">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Currently reading</h3>
                
                {{-- Tombol Tambah Buku (Hanya muncul jika diizinkan / Moderator) --}}
                @if($club->allow_member_add_book || (Auth::check() && $club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists()))
                    <button @click="openAddBookModal = true" onclick="openModal()" class="bg-slate-850 hover:bg-slate-800 text-purple-400 border border-purple-500/20 px-3 py-1 text-[11px] font-bold rounded-sm transition">
                        + Add Book
                    </button>
                @endif
            </div>
            
            {{-- Menggunakan relasi yang baru: $club->currentlyReading --}}
            @if($club->currentlyReading->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($club->currentlyReading as $book)
                        <div class="flex gap-4 bg-slate-850 border border-slate-800 p-4 rounded-sm relative group">
                            
                            {{-- Cover Buku --}}
                            <div class="w-16 h-24 bg-slate-800 border border-slate-700 rounded-sm relative flex-shrink-0 flex items-center justify-center overflow-hidden">
                                <svg class="absolute inset-0 w-full h-full text-slate-700/20 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                                    <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5"/>
                                    <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5"/>
                                </svg>
                                @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" class="absolute inset-0 w-full h-full object-cover">
                                @elseif($book->cover) {{-- Fallback jika ada kolom external link cover --}}
                                    <img src="{{ $book->cover }}" class="absolute inset-0 w-full h-full object-cover">
                                @endif
                            </div>

                            {{-- Data Detail Buku --}}
                            <div class="text-xs text-slate-300 flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="font-bold text-white text-sm line-clamp-1">{{ $book->title }}</h4>
                                    <p class="text-slate-500 mt-0.5">by {{ $book->author_name ?? 'Unknown Author' }}</p>
                                </div>

                                <div class="flex justify-between items-center mt-3 pt-2 border-t border-slate-800/60">
                                    <span class="text-[10px] text-slate-500">Added {{ $book->pivot->created_at->diffForHumans() }}</span>
                                    
                                    {{-- Aksi Cepat Selesaikan Baca (Hanya untuk Moderator) --}}
                                    @if(Auth::check() && $club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists())
                                        <form action="{{ route('clubs.books.status.update', [$club->slug, $book->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="text-[10px] font-bold text-purple-400 hover:text-purple-300 transition uppercase tracking-wide">
                                                Mark Done ✓
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-500 italic">Belum ada buku yang diset untuk dibaca bersama saat ini.</p>
            @endif

            {{-- ─── POP-UP MODAL: ADD BOOK TO CLUB ─── --}}
            <div id="add-book-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
                {{-- Backdrop hitam transparan --}}
                <div onclick="closeModal()" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
                
                {{-- Kotak Dialog --}}
                <div class="bg-slate-900 border border-slate-800 w-full max-w-md rounded-sm p-6 relative z-10 shadow-xl">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-800">
                        <h4 class="text-sm font-bold text-white uppercase tracking-wider">Add Group Reading</h4>
                        <button onclick="closeModal()" class="text-slate-500 hover:text-slate-300 text-sm">&times;</button>
                    </div>

                    <form action="{{ route('clubs.books.add', $club->slug) }}" method="POST" class="space-y-4">
                        @csrf
                        {{-- Input Status otomatis disembunyikan sebagai 'reading' --}}
                        <input type="hidden" name="status" value="reading">

                        <div>
                            <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Select Book Katalog</label>
                            <select name="book_id" required class="w-full bg-slate-900 text-slate-300 text-xs border border-slate-700 rounded-sm px-3 py-2.5 outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer">
                                <option value="" disabled selected>Pilih buku yang tersedia di ReadBond...</option>
                                @foreach($allBooks as $b) {{-- Dilempar via controller --}}
                                    <option value="{{ $b->id }}">{{ $b->title }} ({{ $b->author_name }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-white text-xs font-medium px-3 py-2 transition">
                                Cancel
                            </button>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-4 py-2 rounded-sm transition shadow-md shadow-purple-950/40">
                                Add to List
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── SECTION: DISCUSSIONS BOARD ─── --}}
        <div class="border-t border-slate-800 pt-6">
            <div class="flex justify-between items-center py-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Discussions Board</h3>
                
                {{-- Logika filter tombol New Topic berdasarkan izin internal club --}}
                @php
                    $isModerator = Auth::check() && $club->members()->where('user_id', Auth::id())->where('book_club_members.role', 'moderator')->exists();
                    $canCreateDiscussion = $isModerator || ($isMember && $club->allow_member_add_discussion);
                @endphp

                @if($canCreateDiscussion)
                    <a href="{{ route('clubs.discussion.create', $club->slug) }}" class="inline-flex items-center gap-1 text-purple-400 hover:text-purple-300 text-xs font-bold transition">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        New Topic
                    </a>
                @endif
            </div>

            {{-- List Obrolan Diskusi --}}
            <div class="divide-y divide-slate-800 border-t border-b border-slate-800">
                {{-- Pastikan variabel $club->discussions di-loop dengan benar --}}
                @forelse($club->discussions as $discussion)
                    <div class="py-3.5 flex justify-between items-center group">
                        <div class="min-w-0 pr-4">
                            <a href="{{ route('clubs.discussion.show', ['slug' => $club->slug, 'discussion' => $discussion->id]) }}" class="block text-sm font-semibold text-white group-hover:text-purple-300 transition truncate">
                                {{ $discussion->title }}
                            </a>
                            
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[10px] text-slate-500">Dimulai oleh <span class="text-slate-400">{{ $discussion->user->username ?? 'Anggota' }}</span></span>
                                
                                {{-- Badge Informasi Tambahan jika Diskusi ini nge-mention buku tertentu --}}
                                @if($discussion->book_id && $discussion->book)
                                    <span class="text-[9px] bg-slate-800 text-slate-400 border border-slate-700/60 px-1.5 py-0.2 rounded-sm truncate max-w-[150px]">
                                        📖 {{ $discussion->book->title }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="text-xs font-bold bg-slate-800 text-slate-400 px-2.5 py-1 rounded-sm border border-slate-700/50">
                                {{ $discussion->posts_count ?? $discussion->posts->count() }} posts
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic py-6 text-center">Belum ada topik diskusi dimulai. Jadilah yang pertama berkomentar!</p>
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

function openModal() {
    document.getElementById('add-book-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('add-book-modal').classList.add('hidden');
}
</script>
@endauth

@endsection