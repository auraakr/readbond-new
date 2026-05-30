@extends('layouts.profile')

@section('title', 'Edit Profile — ' . $user->name)

@section('content')
<div class="min-h-screen bg-[#0a0a0f] text-white font-sans">
    <div class="max-w-3xl mx-auto px-6 py-10">
        
        {{-- Judul Halaman --}}
        <div class="mb-8 pb-3 border-b border-slate-800">
            <h2 class="text-xl font-bold tracking-tight text-white">Profile Settings</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola informasi publik akun ReadBond kamu.</p>
        </div>

        {{-- Flash Session Message --}}
        @if(session('status') === 'profile-updated')
            <div class="mb-6 p-3 bg-emerald-950/40 border border-emerald-900/60 text-emerald-400 text-xs rounded-sm flex items-center gap-2">
                <span>✨</span> Profil kamu berhasil diperbarui!
            </div>
        @endif

        {{-- Form Utama --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('patch')

            {{-- Row 1: Upload Avatar --}}
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-sm p-5 flex flex-col sm:flex-row items-center gap-6">
                <div class="relative shrink-0">
                    <img id="avatar-preview" 
                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=8b5cf6&color=fff&size=96' }}"
                        class="w-20 h-20 rounded-full object-cover ring-2 ring-purple-500/30 bg-slate-800" 
                        alt="Avatar preview">
                </div>
                <div class="flex-1 text-center sm:text-left space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Profile Picture</label>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('avatar-input').click()" 
                            class="px-3 py-1.5 text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-sm border border-slate-700 transition cursor-pointer">
                        Choose New Image
                    </button>
                    <p class="text-[10px] text-slate-500">Mendukung file JPG, PNG. Maksimal 2MB.</p>
                    @error('avatar')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Row 2: Form Fields (Name, Username, Bio) --}}
            {{-- Menggunakan x-data Alpine untuk menghitung batasan karakter bio --}}
            <div x-data="{ bioText: '{{ old('bio', $user->bio ?? '') }}', maxBio: 160 }" class="bg-slate-900/40 border border-slate-800/80 rounded-sm p-6 space-y-5">
                
                {{-- Input Name --}}
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Display Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-slate-950 text-slate-200 text-sm border border-slate-800 rounded-sm px-3 py-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                    @error('name')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input Username --}}
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Username</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-slate-600 text-sm select-none">@</span>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                               class="w-full bg-slate-950 text-slate-200 text-sm border border-slate-800 rounded-sm pl-7 pr-3 py-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                    </div>
                    @error('username')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input Bio --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-400">Bio</label>
                        {{-- Karakter counter dinamis --}}
                        <span class="text-[10px] font-mono text-slate-500" x-text="maxBio - bioText.length + ' characters left'"></span>
                    </div>
                    <textarea id="bio" name="bio" x-model="bioText" :maxlength="maxBio" rows="3" placeholder="Tulis sedikit tentang dirimu atau kutipan buku favoritmu..."
                              class="w-full bg-slate-950 text-slate-200 text-sm border border-slate-800 rounded-sm px-3 py-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition resize-none scrollbar-hide"></textarea>
                    @error('bio')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Row 3: Favorite Books dengan State Alpine.js --}}
            <div x-data="{ 
                openModal: false, 
                activeSlot: null, 
                searchQuery: '', 
                searchResults: [],
                
                searchBooks() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    fetch(`{{ route('books.autocomplete') }}?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = data.map(book => ({
                                id: book.id || null,
                                external_id: book.external_id,
                                title: book.title,
                                author: book.author || 'Unknown',
                                cover: book.cover || null,
                            }));
                        });
                },
                selectBook(bookId, externalId) {
                    const payload = {
                        book_id: bookId || null,
                        external_id: externalId || null,
                        position: this.activeSlot,
                    };

                    fetch('{{ route('profile.favorite.add') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) { window.location.reload(); }
                        else { alert(data.message || 'Gagal menyimpan buku favorit.'); }
                    });
                },
                removeBook(bookId) {
                    if(!confirm('Hapus buku ini dari daftar favorit?')) return;
                    fetch(`/settings/favorite-books/${bookId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) { window.location.reload(); }
                    });
                }
            }" class="bg-slate-900/40 border border-slate-800/80 rounded-sm p-6 space-y-4">
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Favorite Books</label>
                    <p class="text-[10px] text-slate-500 mt-0.5">Pilih hingga 4 buku favorit untuk ditampilkan di etalase profil utamamu.</p>
                </div>

                {{-- Grid Tampilan 4 Slot Buku --}}
                <div class="grid grid-cols-4 gap-3">
                    @for($i = 0; $i < 4; $i++)
                        @php $book = $favoriteBooks->get($i) ?? null; @endphp
                        
                        <div class="group relative aspect-[2/3] bg-slate-950 border border-slate-800/60 rounded flex flex-col items-center justify-center transition hover:border-purple-500/40 overflow-hidden">
                            @if($book)
                                <img src="{{ $book->cover }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                
                                {{-- Overlay Hapus Buku --}}
                                <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-2">
                                    <button type="button" @click="removeBook('{{ $book->id }}')"
                                            class="p-2 bg-red-600/20 hover:bg-red-600 border border-red-500 text-white rounded-full text-xs transition transform scale-90 group-hover:scale-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                {{-- Tombol tambah memicu pembukaan Modal --}}
                                <button type="button" @click="openModal = true; activeSlot = {{ $i }}; searchQuery = ''; searchResults = [];"
                                        class="w-full h-full border-2 border-dashed border-slate-800/80 flex flex-col items-center justify-center text-slate-600 hover:text-purple-400 hover:border-purple-500/30 transition gap-1 bg-slate-950/20">
                                    <span class="text-xl font-light">+</span>
                                    <span class="text-[9px] font-medium uppercase tracking-wider">Add</span>
                                </button>
                            @endif
                        </div>
                    @endfor
                </div>

                <div class="flex items-center text-[10px] text-slate-500 pt-1">
                    <span class="font-mono text-slate-600">{{ $favoriteBooks->count() }}/4 Slots filled</span>
                </div>

                {{-- ── COMPONENT: MODAL PENCARIAN BUKU ── --}}
                <div x-show="openModal" 
                    x-transition
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
                    x-cloak>
                    
                    <div @click.away="openModal = false" class="bg-[#0f0f15] border border-slate-800 w-full max-w-md rounded-md p-5 space-y-4 shadow-2xl">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <h3 class="text-sm font-bold text-slate-200 uppercase tracking-wider">Pick a Favorite Book</h3>
                            <button type="button" @click="openModal = false" class="text-slate-500 hover:text-white text-xs">✕</button>
                        </div>

                        {{-- Input Search --}}
                        <div class="relative">
                            <input type="text" 
                                x-model="searchQuery" 
                                @input.debounce.300ms="searchBooks()" 
                                placeholder="Ketik judul buku untuk mencari..."
                                class="w-full bg-slate-950 text-slate-200 text-xs border border-slate-800 rounded-sm px-3 py-2.5 outline-none focus:border-purple-500 transition">
                        </div>

                        {{-- Tempat Menampilkan Hasil Pencarian --}}
                        <div class="space-y-1 max-h-60 overflow-y-auto pr-1">
                            <template x-for="book in searchResults" :key="book.external_id">
                                <div @click="selectBook(book.id, book.external_id)"
                                    class="flex items-center gap-3 p-2 bg-slate-900/40 hover:bg-purple-950/30 border border-slate-800/40 hover:border-purple-500/30 rounded cursor-pointer transition group">
                                    <template x-if="book.cover">
                                        <img :src="book.cover" class="w-8 h-12 object-cover rounded bg-slate-800 shrink-0">
                                    </template>
                                    <template x-if="!book.cover">
                                        <div class="w-8 h-12 bg-slate-800 rounded shrink-0 flex items-center justify-center text-slate-600 text-xs">?</div>
                                    </template>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-medium text-slate-300 group-hover:text-white transition truncate" x-text="book.title"></p>
                                        <p class="text-[10px] text-slate-500 truncate" x-text="book.author"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- State saat tidak ada hasil atau query kosong --}}
                            <div x-show="searchResults.length === 0 && searchQuery.length >= 2" class="text-center py-6 text-xs text-slate-600">
                                Buku tidak ditemukan.
                            </div>
                            <div x-show="searchQuery.length < 2" class="text-center py-6 text-xs text-slate-600">
                                Masukkan minimal 2 karakter.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('profile', $user->username) }}" class="text-slate-400 hover:text-white text-xs font-medium transition px-1">
                    Cancel & Back
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-5 py-2.5 rounded-sm transition shadow-md shadow-purple-950/40 cursor-pointer">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fitur Instant Preview Image saat file avatar dipilih
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatar-preview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection