@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen">

    {{-- ═══════════════════════════════════════════
         HERO
    ════════════════════════════════════════════ --}}
    <div class="relative pt-20 pb-0 overflow-hidden">
        @if($book->cover)
            <div class="absolute inset-0 z-0"
                 style="background-image: url('{{ $book->cover }}');
                        background-size: cover; background-position: center;
                        filter: blur(40px) brightness(0.25); transform: scale(1.1);">
            </div>
        @else
            <div class="absolute inset-0 z-0 bg-slate-800"></div>
        @endif
        <div class="absolute inset-0 z-0 bg-gradient-to-b from-slate-900/40 via-slate-900/60 to-slate-900"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-16 pt-12 pb-16
                    flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">

            {{-- Cover --}}
            <div class="mx-auto lg:mx-0 shrink-0">
                <div class="w-44 lg:w-56 rounded-xl shadow-2xl shadow-black/60
                            overflow-hidden border border-white/10">
                    @if($book->cover)
                        <img src="{{ $book->cover }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-700 flex items-center justify-center text-slate-500">
                            <x-heroicon-o-book-open class="w-12 h-12" />
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 text-center lg:text-left">
                @if(!empty($book->subject))
                    <div class="hidden lg:flex flex-wrap gap-2 mb-4">
                        @foreach(array_slice($book->subject, 0, 4) as $subject)
                            <a href="{{ route('books', ['genre' => Str::slug($subject)]) }}"
                               class="px-3 py-1 bg-purple-500/20 text-purple-300 border border-purple-500/30
                                      rounded-full text-[11px] font-medium tracking-wide hover:bg-purple-500/30 transition">
                                {{ $subject }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <h1 class="text-3xl lg:text-4xl font-black text-white leading-tight tracking-tight">
                    {{ $book->title }}
                </h1>
                <p class="text-slate-300 mt-2 text-base">
                    by <span class="text-purple-300 font-semibold">{{ $book->author_name ?? 'Unknown Author' }}</span>
                    @if($book->year) <span class="text-slate-500">· {{ $book->year }}</span> @endif
                    @if($book->pageCount) <span class="text-slate-500">· {{ $book->pageCount }} halaman</span> @endif
                </p>

                <div class="flex items-center gap-2 mt-3 justify-center lg:justify-start">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($book->averageRating))
                                <x-heroicon-s-star class="w-4 h-4" />
                            @else
                                <x-heroicon-o-star class="w-4 h-4 text-slate-600" />
                            @endif
                        @endfor
                    </div>
                    <span class="text-slate-400 text-sm">{{ number_format($book->averageRating, 1) }} / 5</span>
                </div>

                <p class="mt-5 text-slate-400 text-sm leading-relaxed max-w-xl mx-auto lg:mx-0">
                    {{ Str::limit($book->desc, 280) }}
                </p>

                {{-- Action Buttons --}}
                <div class="mt-8 flex flex-wrap gap-3 justify-center lg:justify-start">

                    {{-- Reading Log --}}
                    @auth
                        <button onclick="openReadingLogModalWithBook({{ $book->id }}, '{{ $book->external_id }}', '{{ addslashes($book->title) }}', '{{ addslashes($book->author_name) }}', '{{ $book->cover }}')"
                                class="flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-500
                                       text-white text-sm font-semibold rounded-lg transition shadow-lg shadow-purple-900/40">
                            <x-heroicon-o-book-open class="w-4 h-4" />
                            @if($userReadingLog)
                                {{ ['want_to_read' => 'Ingin Baca', 'reading' => 'Sedang Baca', 'finished' => 'Selesai Baca'][$userReadingLog->status] }}
                            @else
                                Tandai Dibaca
                            @endif
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-500
                                  text-white text-sm font-semibold rounded-lg transition shadow-lg shadow-purple-900/40">
                            <x-heroicon-o-book-open class="w-4 h-4" /> Tandai Dibaca
                        </a>
                    @endauth

                    {{-- Readlist --}}
                    @auth
                        <button id="readlist-btn" onclick="toggleReadlist()"
                                class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold
                                       rounded-lg transition border
                                       {{ $userInReadlist
                                           ? 'bg-blue-600 border-blue-500 hover:bg-blue-500'
                                           : 'bg-slate-700 border-slate-600 hover:bg-slate-600' }}">
                            <x-heroicon-o-bookmark class="w-4 h-4" />
                            <span id="readlist-label">{{ $userInReadlist ? 'Di Readlist' : 'Readlist' }}</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                  text-white text-sm font-semibold rounded-lg transition border border-slate-600">
                            <x-heroicon-o-bookmark class="w-4 h-4" /> Readlist
                        </a>
                    @endauth

                    {{-- Tambah ke Koleksi --}}
                    @auth
                        <button onclick="openModal('modal-collection')"
                                class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                       text-white text-sm font-semibold rounded-lg transition border border-slate-600">
                            <x-heroicon-o-squares-plus class="w-4 h-4" /> Koleksi
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                  text-white text-sm font-semibold rounded-lg transition border border-slate-600">
                            <x-heroicon-o-squares-plus class="w-4 h-4" /> Koleksi
                        </a>
                    @endauth

                    {{-- Like --}}
                    @auth
                        <button id="like-btn" onclick="toggleLike()"
                                class="flex items-center gap-2 px-4 py-2.5 text-sm rounded-lg transition border
                                       {{ $userLiked
                                           ? 'bg-red-600 border-red-500 text-white hover:bg-red-500'
                                           : 'bg-slate-700 border-slate-600 text-slate-300 hover:bg-slate-600' }}">
                            <x-heroicon-o-heart class="w-4 h-4" />
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex items-center gap-2 px-4 py-2.5 bg-slate-700 border border-slate-600
                                  text-slate-300 text-sm rounded-lg transition hover:bg-slate-600">
                            <x-heroicon-o-heart class="w-4 h-4" />
                        </a>
                    @endauth

                    <button class="flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600
                                   text-slate-300 text-sm rounded-lg transition border border-slate-600">
                        <x-heroicon-o-share class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Rate Card — desktop --}}
            <div class="hidden lg:block shrink-0 w-52">
                <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-5 backdrop-blur-sm">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-medium mb-3 text-center">
                        Beri Rating
                    </p>
                    @auth
                        <div class="flex justify-center gap-1">
                            @foreach(range(1, 5) as $star)
                                <button data-star="{{ $star }}"
                                        class="star-btn transition-colors duration-150
                                               {{ $userRating && $star <= $userRating ? 'text-yellow-400' : 'text-slate-600' }}
                                               hover:text-yellow-400">
                                    <x-heroicon-s-star class="w-8 h-8" />
                                </button>
                            @endforeach
                        </div>
                        <p class="text-center text-slate-500 text-xs mt-3" id="rating-label">
                            {{ $userRating ? ['','Buruk','Kurang','Cukup','Bagus','Luar Biasa!'][$userRating] : 'Belum dirating' }}
                        </p>
                    @else
                        <div class="flex justify-center gap-1">
                            @foreach(range(1,5) as $star)
                                <a href="{{ route('login') }}">
                                    <x-heroicon-s-star class="w-8 h-8 text-slate-600 hover:text-yellow-400 transition" />
                                </a>
                            @endforeach
                        </div>
                        <p class="text-center text-slate-600 text-xs mt-3">Login untuk rating</p>
                    @endauth

                    <hr class="border-slate-700 my-4">

                    @auth
                        <button onclick="openReadingLogModalWithBook({{ $book->id }}, '{{ $book->external_id }}', '{{ addslashes($book->title) }}', '{{ addslashes($book->author_name) }}', '{{ $book->cover }}')"
                                class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                       font-medium rounded-lg transition border border-slate-600 mb-2">
                            + Reading Log
                        </button>
                        <button onclick="openModal('modal-collection')"
                                class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                       font-medium rounded-lg transition border border-slate-600 mb-2">
                            + Koleksi
                        </button>
                        <button onclick="toggleReadlist()"
                                class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                       font-medium rounded-lg transition border border-slate-600">
                            {{ $userInReadlist ? '✓ Di Readlist' : '+ Ke Readlist' }}
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="block w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                  font-medium rounded-lg transition border border-slate-600 mb-2 text-center">
                            + Reading Log
                        </a>
                        <a href="{{ route('login') }}"
                           class="block w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                  font-medium rounded-lg transition border border-slate-600 mb-2 text-center">
                            + Koleksi
                        </a>
                        <a href="{{ route('login') }}"
                           class="block w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                  font-medium rounded-lg transition border border-slate-600 text-center">
                            + Ke Readlist
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════════════ --}}
    <div class="max-w-6xl mx-auto px-6 lg:px-16 py-10
                flex flex-col lg:flex-row gap-8 lg:gap-12">

        <div class="flex-1 min-w-0">
            {{-- Tabs --}}
            <div class="flex border-b border-slate-700 mb-8 gap-1">
                <button onclick="switchTab('subjects')" data-tab="subjects"
                        class="tab-btn px-5 py-3 text-sm font-semibold text-white
                               border-b-2 border-purple-500 -mb-px transition">Subjects</button>
                <button onclick="switchTab('author')" data-tab="author"
                        class="tab-btn px-5 py-3 text-sm font-medium text-slate-500
                               border-b-2 border-transparent -mb-px hover:text-slate-300 transition">Author</button>
                <button onclick="switchTab('details')" data-tab="details"
                        class="tab-btn px-5 py-3 text-sm font-medium text-slate-500
                               border-b-2 border-transparent -mb-px hover:text-slate-300 transition">Details</button>
                <button onclick="switchTab('reviews')" data-tab="reviews"
                        class="tab-btn px-5 py-3 text-sm font-medium text-slate-500
                               border-b-2 border-transparent -mb-px hover:text-slate-300 transition">
                    Reviews 
                    @if($stats['reviews_count'] > 0)
                        <span class="ml-1 px-2 py-0.5 bg-purple-600 text-white text-xs rounded-full">{{ $stats['reviews_count'] }}</span>
                    @endif
                </button>
            </div>

            {{-- Tab: Subjects --}}
            <div id="tab-subjects" class="tab-content">
                <div class="flex flex-wrap gap-2">
                    @forelse($book->subject ?? [] as $subject)
                        <a href="{{ route('books', ['genre' => Str::slug($subject)]) }}"
                           class="px-4 py-2 bg-slate-800 text-slate-300 border border-slate-700
                                  rounded-lg text-xs font-medium uppercase tracking-tight
                                  hover:border-purple-500 hover:text-purple-300 hover:bg-purple-500/10 transition">
                            {{ $subject }}
                        </a>
                    @empty
                        <p class="text-slate-500 text-sm">Tidak ada subject tersedia.</p>
                    @endforelse
                </div>
            </div>

            {{-- Tab: Author --}}
            <div id="tab-author" class="tab-content hidden">
                <div class="flex items-center gap-4 p-5 bg-slate-800 rounded-xl border border-slate-700">
                    <div class="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center
                                text-slate-400 text-xl font-bold shrink-0">
                        {{ strtoupper(substr($book->author_name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-white font-bold text-base">{{ $book->author_name ?? 'Unknown Author' }}</p>
                        <p class="text-slate-400 text-sm mt-1">Informasi author belum tersedia.</p>
                    </div>
                </div>
            </div>

            {{-- Tab: Details --}}
            <div id="tab-details" class="tab-content hidden">
                <dl class="divide-y divide-slate-800">
                    @foreach([
                        'Judul'   => $book->title,
                        'Penulis' => $book->author_name ?? '-',
                        'Tahun'   => $book->year ?: '-',
                        'Halaman' => $book->pageCount ?: '-',
                        'ID'      => $book->external_id,
                    ] as $label => $value)
                        <div class="flex gap-4 py-3">
                            <dt class="w-28 shrink-0 text-slate-500 text-sm">{{ $label }}</dt>
                            <dd class="text-slate-200 text-sm">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Tab: Reviews --}}
            <div id="tab-reviews" class="tab-content hidden space-y-5">
                @forelse($reviews as $review)
                    <div class="bg-slate-800 border border-slate-700 rounded-lg p-5 hover:border-slate-600 transition">
                        {{-- Header --}}
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                @if($review['user_avatar'])
                                    <img src="{{ $review['user_avatar'] }}" alt="{{ $review['user_name'] }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 text-sm font-bold">
                                        {{ strtoupper(substr($review['user_name'], 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="text-white font-semibold text-sm">{{ $review['user_name'] }}</p>
                                    <p class="text-slate-500 text-xs">{{ $review['created_at'] }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Rating Stars --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= $review['rating'] ? 'text-yellow-400' : 'text-slate-600' }}" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-slate-400 text-xs">{{ $review['rating'] }}/5</span>
                        </div>

                        {{-- Review Text --}}
                        @if($review['review'])
                            <p class="text-slate-300 text-sm leading-relaxed">
                                {{ $review['review'] }}
                            </p>
                        @endif

                        {{-- Dynamic Interaction Button (Like Review) --}}
                        <div>
                            @auth
                                <button onclick="toggleLikeReview({{ $review['id'] }})" 
                                        id="review-like-btn-{{ $review['id'] }}"
                                        class="flex items-center gap-1.5 py-3 text-xs font-semibold transition
                                        {{ $review['is_liked'] 
                                            ? 'font-bold' 
                                            : 'text-slate-400 hover:text-slate-300' }}">
                                    <svg class="w-5 h-5 {{ $review['is_liked'] ? 'fill-current' : 'fill-none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                    <span id="review-like-count-{{ $review['id'] }}">{{ $review['likes_count'] ?? 0 }} Likes</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" 
                                    class="flex items-center gap-1.5 px-3 py-1.5 border text-slate-400 rounded-full text-xs hover:text-slate-300 transition">
                                    <svg class="w-5 h-5 fill-none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                    <span>{{ $review['likes_count'] ?? 0 }} Likes</span>
                                </a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm">Belum ada review untuk buku ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Mobile rate card --}}
        <div class="lg:hidden">
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-medium mb-3 text-center">
                    Beri Rating
                </p>
                @auth
                    <div class="flex justify-center gap-1">
                        @foreach(range(1, 5) as $star)
                            <button data-star="{{ $star }}"
                                    class="star-btn transition-colors
                                           {{ $userRating && $star <= $userRating ? 'text-yellow-400' : 'text-slate-600' }}
                                           hover:text-yellow-400">
                                <x-heroicon-s-star class="w-8 h-8" />
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="flex justify-center gap-1">
                        @foreach(range(1,5) as $star)
                            <a href="{{ route('login') }}">
                                <x-heroicon-s-star class="w-8 h-8 text-slate-600 hover:text-yellow-400 transition" />
                            </a>
                        @endforeach
                    </div>
                @endauth
                <div class="mt-4 space-y-2">
                    <button onclick="openReadingLogModalWithBook({{ $book->id }}, '{{ $book->external_id }}', '{{ addslashes($book->title) }}', '{{ addslashes($book->author_name) }}', '{{ $book->cover }}')"
                            class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600">
                        + Reading Log
                    </button>
                    <button onclick="openModal('modal-collection')"
                            class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600">
                        + Koleksi
                    </button>
                    <button onclick="toggleReadlist()"
                            class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600">
                        {{ $userInReadlist ? '✓ Di Readlist' : '+ Ke Readlist' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Collection --}}
<div id="modal-collection"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-white font-bold text-lg">Tambah ke Koleksi</h3>
            <button onclick="closeModal('modal-collection')" class="text-slate-500 hover:text-white transition">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>

        @if($userCollections->isEmpty())
            <div class="text-center py-6">
                <p class="text-slate-400 text-sm mb-4">Kamu belum punya koleksi.</p>
                <a href="{{ route('collections.create') }}"
                   class="inline-block px-5 py-2 bg-purple-600 hover:bg-purple-500
                          text-white text-sm font-semibold rounded-lg transition">
                    Buat Koleksi Baru
                </a>
            </div>
        @else
            <div class="space-y-2 mb-4 max-h-60 overflow-y-auto">
                @foreach($userCollections as $col)
                    <form action="{{ route('books.add-to-collection', $book->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="collection_id" value="{{ $col->id }}">
                        <button type="submit"
                                class="w-full flex items-center justify-between px-4 py-3
                                       bg-slate-900 hover:bg-slate-700 border border-slate-700
                                       hover:border-purple-500 rounded-xl transition text-left">
                            <div>
                                <p class="text-white text-sm font-medium">{{ $col->title }}</p>
                                <p class="text-slate-500 text-xs mt-0.5">{{ $col->books_count ?? 0 }} buku</p>
                            </div>
                            <x-heroicon-o-plus class="w-4 h-4 text-slate-500" />
                        </button>
                    </form>
                @endforeach
            </div>
            <a href="{{ route('collections.create') }}"
               class="block w-full py-2.5 border border-dashed border-slate-600 text-slate-400
                      hover:border-purple-500 hover:text-purple-300 text-sm text-center
                      rounded-xl transition">
                + Buat Koleksi Baru
            </a>
        @endif
    </div>
</div>

<script>
// ── Tab switching ──
function switchTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('text-white', 'border-purple-500');
        btn.classList.add('text-slate-500', 'border-transparent');
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    const activeBtn = document.querySelector(`[data-tab="${name}"]`);
    activeBtn.classList.add('text-white', 'border-purple-500');
    activeBtn.classList.remove('text-slate-500', 'border-transparent');
}

// ── Modal helpers ──
function openModal(id)  { document.getElementById(id).classList.replace('hidden', 'flex'); }
function closeModal(id) { document.getElementById(id).classList.replace('flex', 'hidden'); }

['modal-reading-log', 'modal-collection'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

// ── Star rating (AJAX) ──
const starBtns    = document.querySelectorAll('.star-btn');
const ratingLabel = document.getElementById('rating-label');
const ratingLabels = ['', 'Buruk', 'Kurang', 'Cukup', 'Bagus', 'Luar Biasa!'];
let currentRating = {{ $userRating ?? 0 }};

starBtns.forEach(btn => {
    btn.addEventListener('mouseenter', () => {
        const val = +btn.dataset.star;
        starBtns.forEach(b => {
            b.classList.toggle('text-yellow-400', +b.dataset.star <= val);
            b.classList.toggle('text-slate-600',  +b.dataset.star >  val);
        });
    });
    btn.addEventListener('mouseleave', () => {
        starBtns.forEach(b => {
            b.classList.toggle('text-yellow-400', +b.dataset.star <= currentRating);
            b.classList.toggle('text-slate-600',  +b.dataset.star >  currentRating);
        });
    });
    btn.addEventListener('click', async () => {
        const val = +btn.dataset.star;
        try {
            await fetch("{{ route('books.rate', $book->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ rating: val }),
            });
            currentRating = val;
            if (ratingLabel) ratingLabel.textContent = ratingLabels[val];
        } catch(e) { console.error(e); }
    });
});

// ── Toggle Like Buku (AJAX) ──
let isLiked = {{ $userLiked ? 'true' : 'false' }};
async function toggleLike() {
    try {
        const res  = await fetch("{{ route('books.like', $book->id) }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        const data = await res.json();
        isLiked    = data.liked;
        const btn  = document.getElementById('like-btn');
        if (!btn) return;
        btn.classList.toggle('bg-red-600',    isLiked);
        btn.classList.toggle('border-red-500', isLiked);
        btn.classList.toggle('text-white',    isLiked);
        btn.classList.toggle('bg-slate-700',  !isLiked);
        btn.classList.toggle('border-slate-600', !isLiked);
        btn.classList.toggle('text-slate-300', !isLiked);
    } catch(e) { console.error(e); }
}

// ── Toggle Like Review (AJAX) ──
async function toggleLikeReview(reviewId) {
    try {
        const res = await fetch(`/reviews/${reviewId}/like`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        });
        
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        const data = await res.json();
        
        if (data.success) {
            const btn = document.getElementById(`review-like-btn-${reviewId}`);
            const countLabel = document.getElementById(`review-like-count-${reviewId}`);
            const svg = btn.querySelector('svg');
            
            // Update teks jumlah likes tanpa menghilangkan kata "Likes"
            if (countLabel) {
                countLabel.textContent = `${data.likes_count} Likes`;
            }
            
            // Sinkronisasi class Tailwind dengan state is_liked terbaru
            if (data.is_liked) {
                // Hapus class default (slate)
                btn.classList.remove('text-slate-400', 'hover:text-slate-300');
                // Tambahkan class aktif (rose/pink)
                btn.classList.add('text-rose-400', 'font-bold');
                
                // Warnai icon SVG heart
                svg.classList.remove('fill-none');
                svg.classList.add('fill-current');
            } else {
                // Hapus class aktif (rose/pink)
                btn.classList.add('text-rose-400', 'font-bold');
                // Kembalikan ke class default (slate)
                btn.classList.add('text-slate-400', 'hover:text-slate-300');
                
                // Kosongkan kembali warna icon SVG heart
                svg.classList.remove('fill-current');
                svg.classList.add('fill-none');
            }
        }
    } catch(e) { 
        console.error("Gagal mengeksekusi like review:", e); 
    }
}

// ── Toggle Readlist (AJAX) ──
let inReadlist = {{ $userInReadlist ? 'true' : 'false' }};
async function toggleReadlist() {
    try {
        const res  = await fetch("{{ route('books.readlist', $book->id) }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        const data = await res.json();
        inReadlist = data.inReadlist;

        const btn   = document.getElementById('readlist-btn');
        const label = document.getElementById('readlist-label');
        if (label) label.textContent = inReadlist ? 'Di Readlist' : 'Readlist';
        if (btn) {
            btn.classList.toggle('bg-blue-600',    inReadlist);
            btn.classList.toggle('border-blue-500', inReadlist);
            btn.classList.toggle('bg-slate-700',   !inReadlist);
            btn.classList.toggle('border-slate-600', !inReadlist);
        }
    } catch(e) { console.error(e); }
}
</script>
@endsection