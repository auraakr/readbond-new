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
                class="w-full bg-slate-800 border border-slate-700 text-white rounded-sm py-3 pl-12 pr-4
                       focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition placeholder-slate-500"
            >

            {{-- Autocomplete Dropdown --}}
            <div id="autocomplete-box"
                 class="absolute z-50 w-full mt-2 bg-slate-800 border border-slate-700 rounded-sm shadow-2xl hidden overflow-hidden">
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
                    <div class="aspect-[2/3] bg-slate-800 rounded-sm overflow-hidden mb-3
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
                    <div class="flex justify-between items-center mb-1">
                        {{-- book rating --}}
                        @if(!empty($book['averageRating']))
                            <div class="flex items-center tracking-tighter">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $book['averageRating'])
                                        <x-heroicon-s-star class="w-3 h-3 text-amber-400" />
                                    @else
                                        <x-heroicon-o-star class="w-3 h-3 text-slate-400" />
                                    @endif
                                @endfor
                                <span class="text-slate-400 text-[10px] mx-1">{{ number_format($book->averageRating, 1) }}</span>
                            </div>
                        @else
                            <div class="flex items-center text-slate-400 text-[10px] tracking-tighter">
                                @for($i = 1; $i <= 5; $i++)
                                    <x-heroicon-o-star class="w-3 h-3" />
                                @endfor
                                <span class="text-slate-400 text-[10px] mx-1">{{ number_format($book->averageRating, 1) }}</span>
                            </div>
                        @endif
                        {{-- likes count --}}
                        {{-- Sisi Kanan: Jumlah Orang yang Like --}}
                        <div class="flex items-center gap-1 text-slate-400 group">
                            {{-- Icon Hati Semacam Letterboxd --}}
                            <svg class="w-3.5 h-3.5 {{ $book->likes_count > 0 ? 'text-pink-600 fill-pink-600' : 'text-slate-600' }}" 
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{-- Teks Angka --}}
                            <span class="text-[11px] font-medium text-slate-400">{{ $book->likes_count ?? 0 }}</span>
                        </div>
                    </div>
                    <h3 class="text-white text-xs font-semibold leading-tight truncate group-hover:text-purple-300 transition">
                        {{ $book->title }}
                        @if($book->year)
                            <i class="text-slate-600 text-[10px] mt-0.5">({{ $book->year }})</i>
                        @endif
                    </h3>
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
    <div class="grid grid-cols-3 md:grid-cols-6 lg:grid-cols-10 gap-2">
        @forelse($recentReviews as $review)
            <div class="flex flex-col gap-2 group">
                {{-- Cover Buku Container --}}
                <div class="relative aspect-[2/3] w-full rounded-sm overflow-hidden border border-white/5 bg-slate-900 shadow-md group-hover:border-purple-100 group-hover:shadow-lg group-hover:shadow-purple-500/5 transition-all duration-300">
                    @if(!empty($review['book_cover']))
                        <img src="{{ $review['book_cover'] }}" alt="{{ $review['book_title'] }}" class="w-full h-full object-cover">
                    @else
                        {{-- Fallback Cover jika cover kosong --}}
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-gradient-to-b from-slate-800 to-slate-900">
                            <span class="text-xs font-bold text-slate-400 line-clamp-3 leading-tight">{{ $review['book_title'] ?? 'Untitled' }}</span>
                        </div>
                    @endif
                </div>

                {{-- Meta Reviewer & Rating --}}
                <div class="flex flex-col gap-1 px-1">
                    {{-- User Avatar & Name --}}
                    <div class="flex items-center gap-2 min-w-0">
                        {{-- Default Avatar bulatan inisial nama ala ReadBond --}}
                        <div class="w-4 h-4 rounded-full bg-purple-600/30 flex items-center justify-center text-[8px] text-purple-400 font-bold uppercase flex-shrink-0">
                            {{ substr($review['user_name'] ?? 'U', 0, 1) }}
                        </div>
                        <span class="text-xs text-slate-400 font-medium truncate group-hover:text-slate-200 transition-colors" title="{{ $review['user_name'] }}">
                            {{ $review['user_name'] ?? 'Anonymous' }}
                        </span>
                    </div>

                    {{-- Rating Stars & Like Dropdown --}}
                    <div class="flex items-center justify-between gap-1">
                        @if(!empty($review['rating']))
                            <div class="flex items-center text-amber-400 text-[10px] tracking-tighter">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review['rating'])
                                        <x-heroicon-s-star class="w-3 h-3 text-amber-400" />
                                    @else
                                        <x-heroicon-o-star class="w-3 h-3 text-slate-400" />
                                    @endif
                                @endfor
                            </div>
                        @endif

                        {{-- Icon Heart jika review di-like --}}
                        @if(!empty($review['is_liked']))
                            <span class="text-rose-500 text-[10px]">❤️</span>
                        @endif
                    </div>
                    
                    {{-- Waktu Review (Opsional, kecil samar di bawah) --}}
                    <span class="text-[9px] text-slate-600 font-light">{{ $review['created_at'] }}</span>
                </div>
            </div>
        @empty
            <p class="text-slate-600 text-sm col-span-full py-8 text-center border border-dashed border-slate-800 rounded-2xl">
                Belum ada review terbaru.
            </p>
        @endforelse
    </div>

    {{-- ─── 5. POPULAR REVIEWS ─── --}}
    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2 mt-12">
        <x-heroicon-o-fire class="w-5 h-5 text-orange-500" />
        Popular Reviews
    </h2>

    <div class="grid grid-cols-1 gap-5">
        @forelse($popularReviews as $review)
            <div class="flex gap-2 group items-start">
                {{-- Cover Buku Container --}}
                <div class="w-32 aspect-[2/3] rounded-sm overflow-hidden border border-white/5 bg-slate-900 shadow-md group-hover:border-purple-500/40 group-hover:shadow-lg group-hover:shadow-orange-500/5 transition-all duration-300">
                    @if(!empty($review['book_cover']))
                    <a href="{{ $review['book_url'] }}">
                        <img src="{{ $review['book_cover'] }}" alt="{{ $review['book_title'] }}" class="w-full h-full object-cover">
                    </a>
                    @else
                        {{-- Fallback Cover --}}
                        <div class="w-full h-full flex flex-col items-center justify-center p-3 text-center bg-gradient-to-b from-slate-800 to-slate-900">
                            <span class="text-xs font-bold text-slate-400 line-clamp-3 leading-tight">{{ $review['book_title'] }}</span>
                        </div>
                    @endif
                </div>

                {{-- Meta Reviewer & Rating --}}
                <div class="w-full px-1">
                    {{-- User Avatar & Name --}}
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-orange-600/30 flex items-center justify-center text-[8px] text-orange-400 font-bold uppercase flex-shrink-0">
                                {{ substr($review['user_name'] ?? 'U', 0, 1) }}
                            </div>
                            <span class="text-sm text-slate-400 font-medium truncate group-hover:text-slate-200 transition-colors" title="{{ $review['user_name'] }}">
                                {{ $review['user_name'] }}
                            </span>
                        </div>
                        {{-- Rating Stars & Total Likes Info --}}
                        <div class="flex items-center gap-3 mt-0.5">
                            @if(!empty($review['rating']))
                                <div class="flex items-center text-amber-400 text-md tracking-tighter">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review['rating'])
                                            <x-heroicon-s-star class="w-3 h-3 text-amber-400" />
                                        @else
                                            <x-heroicon-o-star class="w-3 h-3 text-slate-400" />
                                        @endif
                                    @endfor
                                </div>
                            @endif

                            {{-- Jumlah Like Terbanyak --}}
                            <div class="flex items-center text-rose-400 text-md font-semibold">
                                <span>❤️</span>
                                <span>{{ $review['likes_count'] }} Likes</span>
                            </div>
                        </div>
                        <p class="text-md text-slate-300 font-light leading-relaxed line-clamp-6">
                            "{{ $review['review'] ?? 'No thoughts recorded.' }}"
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-slate-600 text-sm col-span-full py-8 text-center border border-dashed border-slate-800 rounded-2xl">
                Belum ada review populer saat ini.
            </p>
        @endforelse
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