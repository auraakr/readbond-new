@extends('layouts.profile')

@section('title', $user->username . "'s Activity")

@section('content')

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

    {{-- ── REVIEW LIST ── --}}
    <section class="max-w-6xl mx-auto px-6 py-10">
        <p class="text-[10px] py-3 font-semibold uppercase tracking-widest text-slate-500">Recent Reviews</p>

        <div class="flex flex-col gap-px rounded-sm overflow-hidden">
            @forelse($allReviews ?? [] as $review)
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

@endsection
