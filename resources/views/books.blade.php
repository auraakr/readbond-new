@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-32">

    {{-- ─── 1. SEARCH BAR ─── --}}
    <div class="max-w-2xl mx-auto mb-10">
        <form action="/books" method="GET" id="search-form" class="relative" autocomplete="off">
            {{-- Pertahankan filter aktif saat search --}}
            @if($filters['genre']) <input type="hidden" name="genre" value="{{ $filters['genre'] }}"> @endif
            @if($filters['year'])  <input type="hidden" name="year"  value="{{ $filters['year'] }}"> @endif
            @if($filters['sort'])  <input type="hidden" name="sort"  value="{{ $filters['sort'] }}"> @endif

            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-slate-400 absolute left-4 top-3.5 z-10 pointer-events-none" />
            <input
                type="text"
                name="search"
                id="search-input"
                value="{{ $filters['search'] }}"
                placeholder="Cari judul, penulis, atau ISBN..."
                class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl py-3 pl-12 pr-4
                       focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition placeholder-slate-500"
            >

            {{-- Autocomplete Dropdown --}}
            <div id="autocomplete-box"
                 class="absolute z-50 w-full mt-2 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl hidden overflow-hidden">
            </div>
        </form>
    </div>

    {{-- ─── 2. HEADER + FILTERS ─── --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        
        {{-- Judul dinamis --}}
        <div>
            @if($filters['search'])
                <p class="text-slate-400 text-sm mb-1 uppercase tracking-widest font-medium">Hasil pencarian</p>
                <h2 class="text-2xl font-black text-white italic">
                    "{{ $filters['search'] }}"
                    <span class="text-slate-500 font-normal text-base not-italic">
                        — {{ $books->total() }} buku ditemukan
                    </span>
                </h2>
            @else
                <p class="text-slate-400 text-sm mb-1 uppercase tracking-widest font-medium">Semua Buku</p>
                <h2 class="text-2xl font-black text-white italic">Books</h2>
            @endif

            {{-- Active filter badges --}}
            <div class="flex gap-2 mt-2 flex-wrap">
                @if($filters['genre'])
                    <a href="{{ request()->fullUrlWithoutQuery(['genre']) }}"
                       class="inline-flex items-center gap-1 px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/40 rounded-full text-xs hover:bg-purple-500/30 transition">
                        Genre: {{ $filters['genre'] }}
                        <x-heroicon-o-x-mark class="w-3 h-3" />
                    </a>
                @endif
                @if($filters['year'])
                    <a href="{{ request()->fullUrlWithoutQuery(['year']) }}"
                       class="inline-flex items-center gap-1 px-3 py-1 bg-blue-500/20 text-blue-300 border border-blue-500/40 rounded-full text-xs hover:bg-blue-500/30 transition">
                        Tahun: {{ $filters['year'] }}
                        <x-heroicon-o-x-mark class="w-3 h-3" />
                    </a>
                @endif
            </div>
        </div>

        {{-- Filters — semua dalam satu form agar tidak konflik --}}
        <form action="/books" method="GET" id="filter-form" class="flex gap-3 flex-wrap">
            @if($filters['search']) <input type="hidden" name="search" value="{{ $filters['search'] }}"> @endif

            {{-- Filter Sorting --}}
            <select name="sort" onchange="document.getElementById('filter-form').submit()"
                    class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2 text-sm outline-none
                           focus:ring-2 focus:ring-purple-500 hover:border-slate-500 transition cursor-pointer">
                <option value="popular" {{ $filters['sort'] == 'popular' ? 'selected' : '' }}>Popular</option>
                <option value="top_rated" {{ $filters['sort'] == 'top_rated' ? 'selected' : '' }}>Top Rated</option>
                <option value="recent" {{ $filters['sort'] == 'recent' ? 'selected' : '' }}>Newest</option>
                <option value="year_desc" {{ $filters['sort'] == 'year_desc' ? 'selected' : '' }}>Latest Year</option>
            </select>

            {{-- Filter Tahun --}}
            <select name="year" onchange="document.getElementById('filter-form').submit()"
                    class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2 text-sm outline-none
                           focus:ring-2 focus:ring-purple-500 hover:border-slate-500 transition cursor-pointer">
                <option value="">Semua Tahun</option>
                @foreach(range(date('Y'), 1950, -1) as $y)
                    <option value="{{ $y }}" {{ $filters['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            {{-- Filter Genre --}}
            <select name="genre" onchange="document.getElementById('filter-form').submit()"
                    class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2 text-sm outline-none
                           focus:ring-2 focus:ring-purple-500 hover:border-slate-500 transition cursor-pointer">
                <option value="">Semua Genre</option>
                @foreach($trendingGenres as $gen)
                    <option value="{{ $gen }}" {{ $filters['genre'] == $gen ? 'selected' : '' }}>{{ ucfirst($gen) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ─── 3. GRID BUKU ─── --}}
    @if($books->count() > 0)
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5 mb-20">
            @foreach($books as $book)
                <a href="{{ route('books.show', $book->external_id) }}" class="group flex flex-col">
                    {{-- Cover --}}
                    <div class="aspect-[3/5] bg-slate-800 rounded-sm overflow-hidden mb-3
                                border border-slate-700
                                group-hover:border-purple-100">
                        @if($book->cover)
                            <img
                                src="{{ $book->cover }}"
                                alt="{{ $book->title }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-600 p-3 text-center">
                                <x-heroicon-o-book-open class="w-8 h-8 mb-2 opacity-40" />
                                <span class="text-[10px] leading-tight opacity-60">{{ Str::limit($book->title, 40) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <h3 class="text-white text-xs font-semibold leading-tight truncate group-hover:text-purple-300 transition">
                        {{ $book->title }}
                    </h3>
                    <p class="text-slate-500 text-[11px] mt-0.5 truncate">
                        {{ $book->author_name ?? 'Unknown Author' }}
                    </p>
                    @if($book->year)
                        <p class="text-slate-600 text-[10px] mt-0.5">{{ $book->year }}</p>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mb-20">
            {{ $books->appends(request()->query())->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <x-heroicon-o-face-frown class="w-16 h-16 text-slate-700 mb-4" />
            <h3 class="text-slate-400 text-lg font-semibold mb-2">Tidak ada buku ditemukan</h3>
            <p class="text-slate-600 text-sm mb-6">Coba kata kunci atau filter yang berbeda</p>
            <a href="/books" class="px-5 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-sm text-sm transition">
                Reset Pencarian
            </a>
        </div>
    @endif

    {{-- ─── 4. JUST REVIEWED ─── --}}
    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
        <x-heroicon-o-chat-bubble-left-ellipsis class="w-5 h-5 text-purple-400" />
        Just Reviewed
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
        {{-- Loop data review nanti --}}
        <p class="text-slate-600 text-sm col-span-full">Belum ada review terbaru.</p>
    </div>

</div>

{{-- ─── AUTOCOMPLETE SCRIPT ─── --}}
<script>
const input       = document.getElementById('search-input');
const box         = document.getElementById('autocomplete-box');
const autocompleteUrl = "{{ route('books.autocomplete') }}";
let debounceTimer;

input.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = input.value.trim();

    if (q.length < 2) { box.classList.add('hidden'); box.innerHTML = ''; return; }

    debounceTimer = setTimeout(async () => {
        try {
            const res  = await fetch(`${autocompleteUrl}?q=${encodeURIComponent(q)}`);
            const data = await res.json();

            if (!data.length) { box.classList.add('hidden'); return; }

            box.innerHTML = data.map(item => `
                <a href="${item.url}"
                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-700 transition text-left border-b border-slate-700/50 last:border-0">
                    ${item.cover
                        ? `<img src="${item.cover}" class="w-8 h-11 object-cover rounded shrink-0">`
                        : `<div class="w-8 h-11 bg-slate-700 rounded shrink-0 flex items-center justify-center">...</div>`
                    }
                    <div class="overflow-hidden">
                        <p class="text-white text-sm font-medium truncate">${item.title}</p>
                        <p class="text-slate-400 text-xs truncate">${item.author}</p>
                    </div>
                </a>
            `).join('');

            box.classList.remove('hidden');
        } catch (e) {
            console.error('Autocomplete error:', e);
        }
    }, 300);
});

// Tutup dropdown kalau klik di luar
document.addEventListener('click', (e) => {
    if (!input.contains(e.target) && !box.contains(e.target)) {
        box.classList.add('hidden');
    }
});

// Navigasi keyboard
input.addEventListener('keydown', (e) => {
    const items = box.querySelectorAll('a');
    const active = box.querySelector('a.bg-slate-700');
    let idx = Array.from(items).indexOf(active);

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (active) active.classList.remove('bg-slate-700');
        items[Math.min(idx + 1, items.length - 1)]?.classList.add('bg-slate-700');
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (active) active.classList.remove('bg-slate-700');
        items[Math.max(idx - 1, 0)]?.classList.add('bg-slate-700');
    } else if (e.key === 'Enter' && active) {
        e.preventDefault();
        window.location.href = active.getAttribute('href'); // ✅
    } else if (e.key === 'Escape') {
        box.classList.add('hidden');
    }
});
</script>
@endsection