@extends('layouts.profile')

@section('title', $user->username . "'s Profile")

@section('content')

<style>
    .input-field:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.1); outline: none; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.06); }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

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
                        <a href="#" class="group block aspect-[3/5] rounded-sm overflow-hidden bg-slate-800 border border-white/5 hover:border-purple-500/50 transition">
                            @if($book->cover)
                                <img src="{{ $book->cover }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $book->title }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs p-3 text-center leading-snug">{{ $book->title }}</div>
                            @endif
                        </a>
                        @endforeach
                        {{-- Empty slots --}}
                        @for($i = count($user->favoriteBooks ?? []); $i < 4; $i++)
                        <div class="aspect-[3/5] rounded-sm bg-slate-800/50 border border-dashed border-white/5"></div>
                        @endfor
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
                                @if($follow->avatar_url)
                                    <img src="{{ $follow->avatar_url }}" alt="{{ $follow->name }}" 
                                        class="w-8 h-8 rounded-full object-cover border border-purple-100/50">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-purple-600/20 text-purple-400 flex items-center justify-center text-sm font-bold uppercase">
                                        {{ substr($follow->name, 0, 2) }}
                                    </div>
                                @endif
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
                        <span class="text-[10px] text-slate-600">{{ $user->readlist_count ?? 0 }}</span>
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
                        <a href="{{ route('profile.activity', $user->username) }}" class="text-[10px] font-semibold uppercase tracking-widest text-slate-600 hover:text-purple-400 transition">All</a>
                    </div>
                    <div class="flex flex-col divide-y divide-white/5">
                        @forelse($recentActivity as $activity)
                        <div class="flex items-start gap-3 p-3 bg-slate-900/50 border border-white/5 transition group">
                            {{-- Activity Content --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white">
                                    {{-- 1. PILIHAN TEKS BERDASARKAN TIPE --}}
                                    @if($activity['type'] === 'like')
                                        liked
                                    @elseif($activity['type'] === 'rating')
                                        rated
                                    @elseif($activity['type'] === 'review')
                                        reviewed
                                    @elseif($activity['type'] === 'club_join')
                                        joined
                                    @elseif($activity['type'] === 'diary')
                                        wrote a diary entry
                                    @elseif($activity['type'] === 'reading_log')
                                        @if($activity['status'] === 'want_to_read')
                                            wants to read
                                        @elseif($activity['status'] === 'reading')
                                            is reading
                                        @elseif($activity['status'] === 'finished')
                                            finished
                                        @endif
                                    @endif

                                    {{-- 2. LINK TARGET BERDASARKAN OBJEK YANG TERSEDIA --}}
                                    @if(isset($activity['book']) && $activity['book'] !== null)
                                        <a href="{{ route('books.show', $activity['book']->external_id) }}" 
                                        class="font-semibold hover:text-purple-400 transition">
                                            {{ $activity['book']->title }}
                                        </a>
                                    @elseif(isset($activity['club']) && $activity['club'] !== null)
                                        <a href="{{ route('clubs.show', $activity['club']->slug) }}" 
                                        class="font-semibold hover:text-purple-400 transition">
                                            {{ $activity['club']->name }}
                                        </a>
                                    @endif
                                </p>
                                
                                @if($activity['rating'])
                                <div class="flex items-center gap-0.5 mt-1">
                                    @for($s = 1; $s <= 5; $s++)
                                        <span class="text-xs {{ $s <= $activity['rating'] ? 'text-yellow-400' : 'text-slate-700' }}">★</span>
                                    @endfor
                                </div>
                                @endif

                                @if($activity['notes'])
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $activity['notes'] }}</p>
                                @endif

                                <p class="text-[10px] text-slate-600 mt-1">
                                    {{ $activity['created_at']->diffForHumans() }}
                                </p>
                            </div>

                            {{-- 3. AMANKAN THUMBNAIL (Hanya muncul jika aktivitas memiliki buku) --}}
                            @if(isset($activity['book']) && $activity['book'] !== null)
                                <a href="{{ route('books.show', $activity['book']->external_id) }}" 
                                class="flex-shrink-0 w-12 h-16 rounded overflow-hidden bg-slate-800 border border-white/10 group-hover:border-purple-500/50 transition">
                                    @if($activity['book']->cover_url)
                                        <img src="{{ $activity['book']->cover_url }}" 
                                            class="w-full h-full object-cover" 
                                            alt="{{ $activity['book']->title }}">
                                    @endif
                                </a>
                            @endif
                            
                        </div>
                        @empty
                        <div class="text-center py-8 text-slate-500 text-sm">
                            No activity yet
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
            {{-- end sidebar --}}

        </div>
    </div>

@endsection