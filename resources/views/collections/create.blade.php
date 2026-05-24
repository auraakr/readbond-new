@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">

    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="mb-10">
            <a href="{{ route('collections.index') }}"
               class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-300 text-sm transition mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Collections
            </a>
            <p class="text-slate-500 text-xs uppercase tracking-widest font-medium mb-1">Baru</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Buat Koleksi</h1>
        </div>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-sm p-4">
                <ul class="text-red-400 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center gap-2">
                            <span class="w-1 h-1 rounded-sml bg-red-400 shrink-0"></span>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('collections.store') }}" method="POST" id="collection-form">
            @csrf

            {{-- ── SECTION 1: Info Koleksi ── --}}
            <div class="bg-slate-800/60 border border-slate-700 rounded-sm p-6 mb-6">
                <h2 class="text-white font-bold text-base mb-5 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-sm bg-purple-500/20 text-purple-300 text-xs
                                 flex items-center justify-center font-black">1</span>
                    Info Koleksi
                </h2>

                {{-- Judul --}}
                <div class="mb-5">
                    <label class="block text-slate-300 text-sm font-medium mb-2">
                        Judul Koleksi <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           placeholder="cth: My Favorite Fantasy Books"
                           maxlength="255"
                           class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-sm
                                  px-4 py-3 outline-none focus:ring-2 focus:ring-purple-500
                                  focus:border-purple-500 placeholder-slate-600 transition
                                  @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1.5 text-red-400 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-slate-300 text-sm font-medium mb-2">
                        Deskripsi
                        <span class="text-slate-500 font-normal">(opsional)</span>
                    </label>
                    <textarea name="description" rows="3"
                              placeholder="Ceritakan tentang koleksimu..."
                              maxlength="1000"
                              class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-sm
                                     px-4 py-3 resize-none outline-none focus:ring-2 focus:ring-purple-500
                                     focus:border-purple-500 placeholder-slate-600 transition
                                     @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    <div class="flex justify-between mt-1.5">
                        @error('description')
                            <p class="text-red-400 text-xs">{{ $message }}</p>
                        @else
                            <span></span>
                        @enderror
                        <p class="text-slate-600 text-xs" id="desc-counter">0 / 1000</p>
                    </div>

                    {{-- Visibility --}}
                    <label for="visibility" class="block text-slate-300 text-sm font-medium mb-2">
                        Visibilitas
                    </label>
                    <select name="visibility" id="visibility"
                            class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-sm
                                px-4 py-3 outline-none focus:ring-2 focus:ring-purple-500
                                focus:border-purple-500 placeholder-slate-600 transition">
                        <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>
                            Publik
                        </option>
                        <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>
                            Pribadi
                        </option>
                    </select>
                </div>
            </div>

            {{-- ── SECTION 2: Tambah Buku ── --}}
            <div class="bg-slate-800/60 border border-slate-700 rounded-sm p-6 mb-8">
                <h2 class="text-white font-bold text-base mb-1 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-sm bg-purple-500/20 text-purple-300 text-xs
                                 flex items-center justify-center font-black">2</span>
                    Tambah Buku<span class="text-red-400">*</span>
                </h2>
                <p class="text-slate-500 text-xs mb-5 ml-8">Tambahkan buku yang ingin kamu tambahkan ke dalam koleksimu minimal 1 buku.</p>
                
                {{-- Search buku --}}
                <div class="relative mb-4" autocomplete="off">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5 pointer-events-none z-10"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" id="book-search"
                           placeholder="Cari judul buku untuk ditambahkan..."
                           class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-sm
                                  py-3 pl-10 pr-4 outline-none focus:ring-2 focus:ring-purple-500
                                  placeholder-slate-600 transition">

                    {{-- Dropdown hasil --}}
                    <div id="book-search-results"
                         class="absolute z-50 w-full mt-2 bg-slate-800 border border-slate-700
                                rounded-sm shadow-2xl hidden overflow-hidden max-h-72 overflow-y-auto">
                    </div>
                </div>

                {{-- Buku yang sudah dipilih --}}
                <div id="selected-books" class="space-y-2">
                    {{-- Diisi JS --}}
                </div>

                {{-- Empty state --}}
                <div id="empty-books" class="flex flex-col items-center justify-center py-8 text-center">
                    <svg class="w-10 h-10 text-slate-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-slate-600 text-sm">Belum ada buku ditambahkan</p>
                </div>

                {{-- Hidden inputs untuk book_ids --}}
                <div id="book-inputs"></div>
            </div>

            {{-- ── Submit ── --}}
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('collections.index') }}"
                   class="px-6 py-3 border border-slate-700 text-slate-400 hover:text-white
                          hover:border-slate-500 text-sm font-medium rounded-sm transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex items-center gap-2 px-8 py-3 bg-purple-600 hover:bg-purple-500
                               text-white text-sm font-bold rounded-sm transition
                               shadow-lg shadow-purple-900/40 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    Buat Koleksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Desc counter ──
const descArea    = document.querySelector('textarea[name="description"]');
const descCounter = document.getElementById('desc-counter');
descArea.addEventListener('input', () => {
    descCounter.textContent = `${descArea.value.length} / 1000`;
});

// ── Book search & select ──
const bookSearchInput   = document.getElementById('book-search');
const bookSearchResults = document.getElementById('book-search-results');
const selectedBooksEl   = document.getElementById('selected-books');
const emptyBooksEl      = document.getElementById('empty-books');
const bookInputsEl      = document.getElementById('book-inputs');
const autocompleteUrl   = "{{ route('books.autocomplete') }}";

let selectedBooks = []; // { id, title, author, cover }
let debounce;

bookSearchInput.addEventListener('input', () => {
    clearTimeout(debounce);
    const q = bookSearchInput.value.trim();
    if (q.length < 2) { bookSearchResults.classList.add('hidden'); return; }

    debounce = setTimeout(async () => {
        const res  = await fetch(`${autocompleteUrl}?q=${encodeURIComponent(q)}`);
        const data = await res.json();

        if (!data.length) { bookSearchResults.classList.add('hidden'); return; }

        bookSearchResults.innerHTML = data.map(b => `
            <button type="button"
                    onclick="addBook(${JSON.stringify(b).replace(/"/g, '&quot;')})"
                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-700
                           transition text-left border-b border-slate-700/50 last:border-0">
                ${b.cover
                    ? `<img src="${b.cover}" class="w-8 h-11 object-cover rounded shrink-0">`
                    : `<div class="w-8 h-11 bg-slate-700 rounded shrink-0"></div>`
                }
                <div class="overflow-hidden">
                    <p class="text-white text-sm font-medium truncate">${b.title}</p>
                    <p class="text-slate-400 text-xs">${b.author}</p>
                </div>
            </button>
        `).join('');

        bookSearchResults.classList.remove('hidden');
    }, 300);
});

// Tutup dropdown klik luar
document.addEventListener('click', e => {
    if (!bookSearchInput.contains(e.target) && !bookSearchResults.contains(e.target)) {
        bookSearchResults.classList.add('hidden');
    }
});

// GANTI addBook — cek duplikat pakai external_id
function addBook(book) {
    if (selectedBooks.find(b => b.external_id === book.external_id)) {
        bookSearchResults.classList.add('hidden');
        bookSearchInput.value = '';
        return;
    }
    selectedBooks.push(book);
    renderSelected();
    bookSearchResults.classList.add('hidden');
    bookSearchInput.value = '';
}

// GANTI removeBook — hapus pakai external_id
function removeBook(externalId) {
    selectedBooks = selectedBooks.filter(b => b.external_id !== externalId);
    renderSelected();
}

function checkSubmit() {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (selectedBooks.length === 0) {
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

// GANTI renderSelected — hidden input pakai book_external_ids[]
function renderSelected() {
    emptyBooksEl.classList.toggle('hidden', selectedBooks.length > 0);

    selectedBooksEl.innerHTML = selectedBooks.map(b => `
        <div class="flex items-center gap-3 bg-slate-900 border border-slate-700 rounded-sm px-4 py-3">
            ${b.cover
                ? `<img src="${b.cover}" class="w-8 h-11 object-cover rounded shrink-0">`
                : `<div class="w-8 h-11 bg-slate-700 rounded shrink-0"></div>`
            }
            <div class="flex-1 overflow-hidden">
                <p class="text-white text-sm font-medium truncate">${b.title}</p>
                <p class="text-slate-500 text-xs">${b.author}</p>
            </div>
            <button type="button"
                    onclick="removeBook('${b.external_id}')"
                    class="text-slate-600 hover:text-red-400 transition ml-2 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `).join('');

    // ✅ Kunci utama — pakai book_external_ids[] bukan book_titles[]
    bookInputsEl.innerHTML = selectedBooks.map(b =>
        `<input type="hidden" name="book_external_ids[]" value="${b.external_id}">`
    ).join('');

    checkSubmit(); 
}
</script>
@endsection