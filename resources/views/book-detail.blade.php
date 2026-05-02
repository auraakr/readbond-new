@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen">

    {{-- ═══════════════════════════════════════════
         HERO — Full-width blurred cover backdrop
    ════════════════════════════════════════════ --}}
    <div class="relative pt-20 pb-0 overflow-hidden">
        {{-- Blurred background --}}
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

        {{-- ── DESKTOP: 2-column layout ── --}}
        <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-16 pt-12 pb-16
                    flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">

            {{-- Cover --}}
            <div class="mx-auto lg:mx-0 shrink-0">
                <div class="w-44 lg:w-56 aspect-[3/4] rounded-xl shadow-2xl shadow-black/60
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

            {{-- Info utama --}}
            <div class="flex-1 text-center lg:text-left">
                {{-- Subject pills (top, desktop) --}}
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

                {{-- Rating display --}}
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

                {{-- Sinopsis --}}
                <p class="mt-5 text-slate-400 text-sm leading-relaxed max-w-xl mx-auto lg:mx-0">
                    {{ Str::limit($book->desc, 280) }}
                </p>

                {{-- Action buttons — desktop row --}}
                <div class="mt-8 flex flex-wrap gap-3 justify-center lg:justify-start">
                    <button class="flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-500
                                   text-white text-sm font-semibold rounded-lg transition shadow-lg shadow-purple-900/40">
                        <x-heroicon-o-book-open class="w-4 h-4" /> Tandai Dibaca
                    </button>
                    <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                   text-white text-sm font-semibold rounded-lg transition border border-slate-600">
                        <x-heroicon-o-bookmark class="w-4 h-4" /> Readlist
                    </button>
                    <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                   text-white text-sm font-semibold rounded-lg transition border border-slate-600">
                        <x-heroicon-o-squares-plus class="w-4 h-4" /> Koleksi
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600
                                   text-slate-300 text-sm rounded-lg transition border border-slate-600">
                        <x-heroicon-o-heart class="w-4 h-4" />
                    </button>
                    <button class="flex items-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600
                                   text-slate-300 text-sm rounded-lg transition border border-slate-600">
                        <x-heroicon-o-share class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Rate card — desktop sidebar --}}
            <div class="hidden lg:block shrink-0 w-52">
                <div class="bg-slate-800/80 border border-slate-700 rounded-xl p-5 backdrop-blur-sm">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-medium mb-3 text-center">
                        Beri Rating
                    </p>
                    <div class="flex justify-center gap-1" id="star-rating">
                        @foreach(range(1, 5) as $star)
                            <button data-star="{{ $star }}"
                                    class="star-btn text-slate-600 hover:text-yellow-400 transition-colors duration-150">
                                <x-heroicon-s-star class="w-8 h-8" />
                            </button>
                        @endforeach
                    </div>
                    <p class="text-center text-slate-500 text-xs mt-3" id="rating-label">Belum dirating</p>

                    <hr class="border-slate-700 my-4">

                    <button class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600 mb-2">
                        + Reading Log
                    </button>
                    <button class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600">
                        + Ke Readlist
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT — 2 column on desktop
    ════════════════════════════════════════════ --}}
    <div class="max-w-6xl mx-auto px-6 lg:px-16 py-10
                flex flex-col lg:flex-row gap-8 lg:gap-12">

        {{-- ── LEFT: Tabs + Content ── --}}
        <div class="flex-1 min-w-0">

            {{-- Tabs --}}
            <div class="flex border-b border-slate-700 mb-8 gap-1" id="tab-nav">
                <button onclick="switchTab('subjects')" data-tab="subjects"
                        class="tab-btn px-5 py-3 text-sm font-semibold text-white
                               border-b-2 border-purple-500 -mb-px transition">
                    Subjects
                </button>
                <button onclick="switchTab('author')" data-tab="author"
                        class="tab-btn px-5 py-3 text-sm font-medium text-slate-500
                               border-b-2 border-transparent -mb-px hover:text-slate-300 transition">
                    Author
                </button>
                <button onclick="switchTab('details')" data-tab="details"
                        class="tab-btn px-5 py-3 text-sm font-medium text-slate-500
                               border-b-2 border-transparent -mb-px hover:text-slate-300 transition">
                    Details
                </button>
                <button onclick="switchTab('reviews')" data-tab="reviews"
                        class="tab-btn px-5 py-3 text-sm font-medium text-slate-500
                               border-b-2 border-transparent -mb-px hover:text-slate-300 transition">
                    Reviews
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
                        'Judul'     => $book->title,
                        'Penulis'   => $book->author_name ?? '-',
                        'Tahun'     => $book->year ?: '-',
                        'Halaman'   => $book->pageCount ?: '-',
                        'ID'        => $book->external_id,
                    ] as $label => $value)
                        <div class="flex gap-4 py-3">
                            <dt class="w-28 shrink-0 text-slate-500 text-sm">{{ $label }}</dt>
                            <dd class="text-slate-200 text-sm">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Tab: Reviews --}}
            <div id="tab-reviews" class="tab-content hidden space-y-6">

                {{-- Reviews from friends --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">
                        Reviews from Friends
                    </h3>
                    <div class="space-y-4">
                        {{-- Hardcode example — nanti dari DB --}}
                        <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-500/20 rounded-full flex items-center
                                                justify-center text-purple-300 font-bold text-xs">A</div>
                                    <div>
                                        <p class="text-white text-sm font-semibold">auraakr</p>
                                        <div class="flex text-yellow-400 mt-0.5">
                                            @foreach(range(1,5) as $s)
                                                <x-heroicon-s-star class="w-3 h-3" />
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <button class="text-xs text-slate-600 hover:text-red-400 transition">Report</button>
                            </div>
                            <p class="text-slate-300 text-sm italic">"I LOOOVEE MAGICCC!!"</p>
                            <div class="mt-3 flex items-center gap-2 text-slate-600 text-xs">
                                <x-heroicon-o-heart class="w-3.5 h-3.5" />
                                <span>100 likes</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Popular reviews --}}
                <div>
                    <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">
                        Popular Reviews
                    </h3>
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center
                                            justify-center text-blue-300 font-bold text-xs">S</div>
                                <div>
                                    <p class="text-white text-sm font-semibold">starbright</p>
                                    <p class="text-slate-400 text-sm mt-1">Magic truly amazes me!</p>
                                </div>
                            </div>
                            <button class="text-xs text-slate-600 hover:text-red-400 transition">Report</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT sidebar — mobile rate card ── --}}
        <div class="lg:hidden">
            <div class="bg-slate-800 border border-slate-700 rounded-xl p-5">
                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-medium mb-3 text-center">
                    Beri Rating
                </p>
                <div class="flex justify-center gap-1">
                    @foreach(range(1, 5) as $star)
                        <x-heroicon-o-star class="w-8 h-8 text-slate-600 hover:text-yellow-400 cursor-pointer transition" />
                    @endforeach
                </div>
                <div class="mt-4 space-y-2">
                    <button class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600">
                        + Reading Log
                    </button>
                    <button class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white text-xs
                                   font-medium rounded-lg transition border border-slate-600">
                        + Ke Readlist
                    </button>
                </div>
            </div>
        </div>

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

// ── Star rating interaction ──
const starBtns = document.querySelectorAll('.star-btn');
const ratingLabel = document.getElementById('rating-label');
const labels = ['', 'Buruk', 'Kurang', 'Cukup', 'Bagus', 'Luar Biasa!'];

starBtns.forEach(btn => {
    btn.addEventListener('mouseenter', () => {
        const val = +btn.dataset.star;
        starBtns.forEach(b => {
            b.classList.toggle('text-yellow-400', +b.dataset.star <= val);
            b.classList.toggle('text-slate-600',  +b.dataset.star >  val);
        });
    });

    btn.addEventListener('mouseleave', () => {
        // restore to selected state
        const selected = +document.querySelector('.star-btn.selected')?.dataset.star || 0;
        starBtns.forEach(b => {
            b.classList.toggle('text-yellow-400', +b.dataset.star <= selected);
            b.classList.toggle('text-slate-600',  +b.dataset.star >  selected);
        });
    });

    btn.addEventListener('click', () => {
        const val = +btn.dataset.star;
        starBtns.forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');
        if (ratingLabel) ratingLabel.textContent = labels[val];
    });
});
</script>
@endsection