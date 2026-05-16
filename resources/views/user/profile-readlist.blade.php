@extends('layouts.profile')

@section('title', $user->username . "'s Readlist")

@section('content')

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

<div class="min-h-screen bg-[#0a0a0f] text-white" style="font-family:'DM Sans',sans-serif;">

    {{-- ── BOOKS GRID ── --}}
    <div class="max-w-6xl mx-auto px-6 py-8">
        <p class="text-[10px] py-3 font-semibold uppercase tracking-widest text-slate-500">Books You Want to Read</p>

        @if($readlistBooks->count() > 0)
        <div class="grid grid-cols-3 lg:grid-cols-8 gap-4">
            @foreach($readlistBooks as $readingLog)
            @php
                $book = $readingLog->book;
                $userRating = $book->ratings->first()?->rating;
            @endphp
            <div class="group">
                <a href="{{ route('books.show', $book->external_id) }}" 
                   class="block rounded-sm overflow-hidden transition">
                    <div class="relative aspect-[2/3] rounded-sm overflow-hidden bg-slate-800 border border-white/5 group-hover:border-purple-100 transition shadow-sm">
                        {{-- Book Cover --}}
                        @if($book->cover_url)
                            <img src="{{ $book->cover_url }}" 
                                 class="w-full h-full object-cover"
                                 alt="{{ $book->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-600 p-4 text-center">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        @endif

                        {{-- Finished Date Badge --}}
                        @if($readingLog->finished_at)
                        <div class="absolute bottom-2 left-2 bg-purple-600/90 backdrop-blur-sm rounded px-2 py-1 text-white text-xs font-semibold shadow-sm">
                            {{ $readingLog->finished_at->format('M Y') }}
                        </div>
                        @endif
                    </div>
                </a>

                {{-- Book Info --}}
                <div class="mt-2 px-1">
                    <h3 class="text-sm font-semibold text-white line-clamp-2 group-hover:text-purple-400 transition">
                        {{ $book->title }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $book->author_name }}
                    </p>

                    {{-- Star Rating Display --}}
                    @if($userRating)
                    <div class="flex items-center gap-0.5 mt-1">
                        @for($s = 1; $s <= 5; $s++)
                            <span class="text-xs {{ $s <= $userRating ? 'text-yellow-400' : 'text-slate-700' }}">★</span>
                        @endfor
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($readlistBooks->hasPages())
        <div class="mt-8 pt-8 border-t border-white/5">
            <div class="flex items-center justify-between">
                <div class="text-sm text-slate-500">
                    Showing {{ $readlistBooks->firstItem() }} to {{ $readlistBooks->lastItem() }} of {{ $readlistBooks->total() }} books
                </div>
                <div class="flex gap-2">
                    {{-- Previous Page --}}
                    @if ($readlistBooks->onFirstPage())
                        <span class="px-4 py-2 bg-slate-800 text-slate-600 rounded-sm cursor-not-allowed">
                            Previous
                        </span>
                    @else
                        <a href="{{ $readlistBooks->previousPageUrl() }}" 
                           class="px-4 py-2 bg-slate-800 text-white rounded-sm hover:bg-purple-600 transition">
                            Previous
                        </a>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach(range(1, $readlistBooks->lastPage()) as $page)
                        @if($page == $readlistBooks->currentPage())
                            <span class="px-4 py-2 bg-purple-600 text-white rounded-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $readlistBooks->url($page) }}" 
                               class="px-4 py-2 bg-slate-800 text-white rounded-sm hover:bg-purple-600 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($readlistBooks->hasMorePages())
                        <a href="{{ $readlistBooks->nextPageUrl() }}" 
                           class="px-4 py-2 bg-slate-800 text-white rounded-sm hover:bg-purple-600 transition">
                            Next
                        </a>
                    @else
                        <span class="px-4 py-2 bg-slate-800 text-slate-600 rounded-sm cursor-not-allowed">
                            Next
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-slate-500 text-lg font-medium">No finished books yet</p>
            <p class="text-slate-600 text-sm mt-2">Start reading and mark books as finished to see them here.</p>
            <a href="{{ route('books') }}" 
               class="mt-6 inline-block px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-sm transition">
                Browse Books
            </a>
        </div>
        @endif
    </div>

</div>

@endsection