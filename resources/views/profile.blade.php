@extends('layouts.main')

@section('title', $user->username . "'s Profile")

@section('content')

<style>
    .input-field:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.1); outline: none; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.06); }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

<div class="min-h-screen bg-[#0a0a0f] text-white" style="font-family:'DM Sans',sans-serif;">

    {{-- ── HEADER / HERO ── --}}
    <div class="bg-slate-900 border-b border-white/5">
        <div class="max-w-6xl mx-auto px-6 py-10">
            <div class="flex items-start gap-6">

                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=8b5cf6&color=fff&size=96' }}"
                        class="w-24 h-24 rounded-full object-cover ring-2 ring-white/10" alt="{{ $user->name }}">
                </div>

                {{-- Name + actions --}}
                <div class="flex-1 min-w-0 pt-1">
                    <div class="flex items-center gap-3 flex-wrap mb-1">
                        <h1 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
                        @auth
                            @if(auth()->id() === $user->id)
                                <a href="#"
                                    class="px-3 py-1 text-[11px] font-semibold uppercase tracking-wider border border-white/15 text-slate-400 hover:border-white/30 hover:text-white rounded-sm transition">
                                    Edit Profile
                                </a>
                            @else
                                <button class="px-4 py-1.5 text-[11px] font-semibold uppercase tracking-wider bg-purple-600 hover:bg-purple-700 text-white rounded-sm transition">
                                    Follow
                                </button>
                            @endif
                        @endauth
                    </div>
                    @if($user->bio)
                        <p class="text-sm text-slate-400 font-light max-w-lg mt-2">{{ $user->bio }}</p>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="hidden sm:flex items-center gap-8 pt-1 flex-shrink-0">
                    @foreach([
                        ['label' => 'Books', 'value' => $user->books_count ?? 0],
                        ['label' => 'This Year', 'value' => $user->books_this_year ?? 0],
                        ['label' => 'Following', 'value' => $user->following_count ?? 0],
                        ['label' => 'Followers', 'value' => $user->followers_count ?? 0],
                    ] as $stat)
                    <div class="text-center">
                        <p class="text-xl font-semibold">{{ $stat['value'] }}</p>
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Mobile stats --}}
            <div class="flex sm:hidden items-center gap-6 mt-6 pt-6 border-t border-white/5">
                @foreach([
                    ['label' => 'Books', 'value' => $user->books_count ?? 0],
                    ['label' => 'This Year', 'value' => $user->books_this_year ?? 0],
                    ['label' => 'Following', 'value' => $user->following_count ?? 0],
                    ['label' => 'Followers', 'value' => $user->followers_count ?? 0],
                ] as $stat)
                <div class="text-center">
                    <p class="text-lg font-semibold">{{ $stat['value'] }}</p>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mt-0.5">{{ $stat['label'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Nav tabs --}}
        <div class="max-w-6xl mx-auto px-6">
            <nav class="flex gap-1 overflow-x-auto scrollbar-hide">
                @foreach(['Profile','Activity','Books','Diary','Reviews','Readlist','Lists','Network'] as $tab)
                    <a href="#"
                        class="px-4 py-3 text-xs font-semibold uppercase tracking-widest whitespace-nowrap transition border-b-2
                        {{ $loop->first
                            ? 'text-white border-purple-500'
                            : 'text-slate-500 border-transparent hover:text-slate-300 hover:border-white/20' }}">
                        {{ $tab }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex gap-8 items-start">

            {{-- ── MAIN COLUMN ── --}}
            <div class="flex-1 min-w-0 flex flex-col gap-10">

                {{-- Favorite Books --}}
                <section>
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-4">Favorite Books</p>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($user->favoriteBooks ?? [] as $book)
                        <a href="#" class="group block aspect-[3/4] rounded-sm overflow-hidden bg-slate-800 border border-white/5 hover:border-purple-500/50 transition">
                            @if($book->cover_url)
                                <img src="{{ $book->cover_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $book->title }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs p-3 text-center leading-snug">{{ $book->title }}</div>
                            @endif
                        </a>
                        @endforeach
                        {{-- Empty slots --}}
                        @for($i = count($user->favoriteBooks ?? []); $i < 4; $i++)
                        <div class="aspect-[3/4] rounded-sm bg-slate-800/50 border border-dashed border-white/5"></div>
                        @endfor
                    </div>
                </section>

                {{-- Recent Activity --}}
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Recent Activity</p>
                        <a href="#" class="text-[10px] font-semibold uppercase tracking-widest text-slate-600 hover:text-purple-400 transition">All</a>
                    </div>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($recentActivity ?? [] as $entry)
                        <div class="group">
                            <div class="aspect-[3/4] rounded-sm overflow-hidden bg-slate-800 border border-white/5 group-hover:border-purple-500/40 transition mb-2">
                                @if($entry->book->cover_url ?? null)
                                    <img src="{{ $entry->book->cover_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="">
                                @endif
                            </div>
                            {{-- Rating stars --}}
                            @if($entry->rating ?? null)
                            <div class="flex items-center gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                    <span class="text-[10px] {{ $s <= $entry->rating ? 'text-purple-400' : 'text-slate-700' }}">★</span>
                                @endfor
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </section>

                {{-- Recent Reviews --}}
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Recent Reviews</p>
                        <a href="#" class="text-[10px] font-semibold uppercase tracking-widest text-slate-600 hover:text-purple-400 transition">More</a>
                    </div>

                    <div class="flex flex-col gap-px rounded-sm overflow-hidden">
                        @forelse($recentReviews ?? [] as $review)
                        <div class="flex gap-4 p-4 border-b border-white/5 hover:bg-slate-800/60 transition">
                            <a href="#" class="flex-shrink-0 w-14 aspect-[3/4] max-h-20 rounded-sm overflow-hidden bg-slate-800">
                                @if($review->book->cover_url ?? null)
                                    <img src="{{ $review->book->cover_url }}" class="w-full h-full object-cover" alt="">
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline gap-2 flex-wrap mb-1">
                                    <a href="#" class="font-semibold text-sm hover:text-purple-400 transition">{{ $review->book->title ?? '' }}</a>
                                    <span class="text-xs text-slate-600">{{ $review->book->year ?? '' }}</span>
                                    @if($review->rewatched ?? false)
                                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500 border border-white/10 px-1.5 py-0.5 rounded-sm">Reread</span>
                                    @endif
                                    @if($review->created_at)
                                        <span class="text-[10px] text-slate-600">{{ $review->created_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 mb-2">
                                    @for($s = 1; $s <= 5; $s++)
                                        <span class="text-xs {{ $s <= ($review->rating ?? 0) ? 'text-purple-400' : 'text-slate-700' }}">★</span>
                                    @endfor
                                </div>
                                <p class="text-sm text-slate-400 font-light leading-relaxed line-clamp-2">{{ $review->notes ?? '' }}</p>
                                <p class="text-[11px] text-slate-600 mt-2">{{ $review->likes_count ?? 0 }} likes</p>
                            </div>
                        </div>
                        @empty
                        <div class="p-6 text-center text-sm text-slate-600">No reviews yet.</div>
                        @endforelse
                    </div>
                </section>

                {{-- Tags + Following row --}}
                <div class="grid grid-cols-2 gap-8">

                    {{-- Tags --}}
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Tags</p>
                            <span class="text-[10px] text-slate-600">{{ count($user->tags ?? []) }}</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @forelse($user->tags ?? [] as $tag)
                            <a href="#" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 border border-white/5 rounded-sm text-xs text-slate-400 hover:text-white transition">
                                {{ $tag->name }}
                            </a>
                            @empty
                            <p class="text-xs text-slate-600">No tags yet.</p>
                            @endforelse
                        </div>
                    </section>

                    {{-- Following --}}
                    <section>
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Following</p>
                            <span class="text-[10px] text-slate-600">{{ $user->following_count ?? 0 }}</span>
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($user->following ?? [] as $follow)
                            <a href="#" title="{{ $follow->name }}">
                                <img src="{{ $follow->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($follow->name).'&background=1e1e2e&color=8b5cf6&size=40' }}"
                                    class="w-10 h-10 rounded-full object-cover ring-1 ring-white/10 hover:ring-purple-500/50 transition" alt="{{ $follow->name }}">
                            </a>
                            @endforeach
                        </div>
                    </section>

                </div>

            </div>

            {{-- ── SIDEBAR ── --}}
            <div class="hidden lg:flex flex-col gap-6 w-64 flex-shrink-0">

                {{-- Readlist --}}
                <div class="border border-white/5 rounded-sm overflow-hidden bg-slate-900">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Readlist</p>
                        <span class="text-[10px] text-slate-600">{{ count($user->readlist ?? []) }}</span>
                    </div>
                    <div class="grid grid-cols-4">
                        @forelse($readlist as $item)
                            {{-- Mengakses data buku melalui relasi 'book' --}}
                            <a href="{{ route('books.show', $item->book->id) }}" 
                            class="group block aspect-[3/4] rounded-sm overflow-hidden bg-slate-800 border border-white/5 hover:border-purple-500/50 transition shadow-lg"
                            title="{{ $item->book->title }}">
                                
                                <img src="{{ $item->book->cover_url }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300" 
                                    alt="{{ $item->book->title }}">
                            </a>
                        @empty
                            {{-- Tampilan jika list kosong --}}
                            <div class="col-span-4 py-8 text-center border border-dashed border-white/10 rounded-sm">
                                <p class="text-xs text-slate-500 uppercase tracking-widest">No books in readlist</p>
                            </div>
                        @endforelse

                        {{-- Opsional: Menambah placeholder jika buku kurang dari 4 agar grid tetap rapi --}}
                        @if($readlist->count() > 0 && $readlist->count() < 4)
                            @for($i = $readlist->count(); $i < 4; $i++)
                                <div class="aspect-[3/4] rounded-sm bg-slate-800/30 border border-dashed border-white/5 flex items-center justify-center">
                                    <x-heroicon-o-plus class="w-5 h-5 text-slate-800" />
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>

                {{-- Diary --}}
                <div class="border border-white/5 rounded-sm overflow-hidden bg-slate-900">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Diary</p>
                        <span class="text-[10px] text-slate-600">{{ count($user->diaryEntries ?? []) }}</span>
                    </div>
                    <div class="flex flex-col divide-y divide-white/5">
                        @forelse(array_slice($user->diaryEntries ?? [], 0, 5) as $entry)
                        <div class="flex items-start gap-3 px-4 py-3">
                            <div class="flex-shrink-0 w-8 text-center">
                                <p class="text-[9px] font-semibold uppercase tracking-wider text-purple-400">{{ $entry->read_at?->format('M') }}</p>
                                <p class="text-sm font-semibold text-white">{{ $entry->read_at?->format('j') }}</p>
                            </div>
                            <p class="text-xs text-slate-400 leading-snug pt-0.5 line-clamp-2">{{ $entry->book->title ?? '' }}</p>
                        </div>
                        @empty
                        <p class="px-4 py-4 text-xs text-slate-600">No diary entries yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Ratings --}}
                <div class="border border-white/5 rounded-sm overflow-hidden bg-slate-900">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Ratings</p>
                        <span class="text-[10px] text-slate-600">{{ $user->books_count ?? 0 }}</span>
                    </div>
                    <div class="px-4 py-4">
                        @php
                            $ratings = $ratingsDistribution ?? [1=>0,2=>0,3=>0,4=>0,5=>0];
                            $max = max(array_values($ratings)) ?: 1;
                        @endphp
                        <div class="flex items-end gap-1 h-14">
                            @foreach($ratings as $star => $count)
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-purple-500/70 rounded-sm transition-all"
                                    style="height: {{ round(($count / $max) * 48) }}px; min-height: 2px;"></div>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-[9px] text-slate-600">★</span>
                            <span class="text-[9px] text-slate-600">★★★★★</span>
                        </div>
                    </div>
                </div>

                {{-- Activity --}}
                <div class="border border-white/5 rounded-sm overflow-hidden bg-slate-900">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/5">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Activity</p>
                        <a href="#" class="text-[10px] font-semibold uppercase tracking-widest text-slate-600 hover:text-purple-400 transition">All</a>
                    </div>
                    <div class="flex flex-col divide-y divide-white/5">
                        @forelse($activityFeed ?? [] as $item)
                        <div class="px-4 py-3 flex gap-2 items-start">
                            <div class="w-1.5 h-1.5 rounded-full bg-purple-500/50 flex-shrink-0 mt-1.5"></div>
                            <p class="text-xs text-slate-400 leading-snug">{{ $item->description ?? '' }}
                                <span class="text-slate-600 ml-1">{{ $item->created_at?->diffForHumans(null, true) }}</span>
                            </p>
                        </div>
                        @empty
                        <p class="px-4 py-4 text-xs text-slate-600">No activity yet.</p>
                        @endforelse
                    </div>
                </div>

            </div>
            {{-- end sidebar --}}

        </div>
    </div>

</div>

@endsection