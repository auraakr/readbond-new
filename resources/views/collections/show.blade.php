@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen">

    {{-- ═══════════════════════════════════════
         HERO
    ════════════════════════════════════════ --}}
    <div class="relative pt-20 overflow-hidden">
        <div class="absolute inset-0 z-0 bg-gradient-to-br from-purple-900/30 via-slate-900 to-slate-900"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl z-0"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-16 pt-10 pb-12">

            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
                <a href="{{ route('collections.index') }}" class="hover:text-slate-300 transition">Collections</a>
                <span>/</span>
                <span class="text-slate-300">{{ Str::limit($collection->title, 40) }}</span>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-14 items-start">

                {{-- Cover collage --}}
                <div class="shrink-0 mx-auto lg:mx-0">
                    <div class="w-52 h-52 lg:w-64 lg:h-64 grid grid-cols-2 gap-1.5 p-1.5
                                bg-slate-800 border border-slate-700 rounded-s overflow-hidden
                                shadow-2xl shadow-black/50">
                        @php $previewBooks = $collection->books->take(4); @endphp

                        @foreach($previewBooks as $book)
                            <div class="rounded-sm overflow-hidden bg-slate-700">
                                @if($book->cover)
                                    <img src="{{ $book->cover }}" alt="{{ $book->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-600 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Isi sisa cell kalau buku < 4 --}}
                        @for($i = $previewBooks->count(); $i < 4; $i++)
                            <div class="rounded-sm bg-slate-700/50 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-600 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                        {{ $collection->title }}
                    </h1>

                    <div class="flex items-center gap-2 mt-3">
                        <div class="w-6 h-6 rounded-full bg-purple-500/30 flex items-center justify-center
                                    text-purple-300 text-xs font-bold">
                            {{ strtoupper(substr($collection->curator->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="text-slate-400 text-sm">
                            by <span class="text-slate-200 font-medium">{{ $collection->curator->name ?? 'Unknown' }}</span>
                        </span>
                    </div>

                    @if($collection->description)
                        <p class="mt-4 text-slate-400 text-sm leading-relaxed max-w-lg">
                            {{ $collection->description }}
                        </p>
                    @endif

                    {{-- Stats --}}
                    <div class="flex flex-wrap items-center gap-5 mt-6">
                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span><strong class="text-white">{{ number_format($collection->books_count) }}</strong> buku</span>
                        </div>

                        <form action="{{ route('collections.like', $collection->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 text-sm transition group
                                           {{ $isLiked ? 'text-red-400' : 'text-slate-400 hover:text-red-400' }}">
                                <svg class="w-4 h-4 transition {{ $isLiked ? 'fill-red-400' : 'group-hover:fill-red-400' }}"
                                     fill="{{ $isLiked ? 'currentColor' : 'none' }}"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span><strong class="text-white">{{ number_format($collection->likes_count) }}</strong> likes</span>
                            </button>
                        </form>

                        <div class="flex items-center gap-2 text-slate-400 text-sm">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span><strong class="text-white">{{ number_format($collection->comments_count) }}</strong> comments</span>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="flex flex-wrap gap-3 mt-6">
                        <form action="{{ route('collections.like', $collection->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 px-5 py-2.5 text-white text-sm font-semibold
                                           rounded-sm transition shadow-lg
                                           {{ $isLiked
                                               ? 'bg-red-600 hover:bg-red-500 shadow-red-900/40'
                                               : 'bg-purple-600 hover:bg-purple-500 shadow-purple-900/40' }}">
                                <svg class="w-4 h-4" fill="{{ $isLiked ? 'currentColor' : 'none' }}"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                {{ $isLiked ? 'Liked' : 'Like Collection' }}
                            </button>
                        </form>

                        <button class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-slate-600
                                       text-white text-sm font-semibold rounded-sm transition border border-slate-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Share
                        </button>

                        @auth
                            @if(Auth::id() === $collection->user_id)
                                <form action="{{ route('collections.destroy', $collection->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus koleksi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-2 px-5 py-2.5 bg-slate-700 hover:bg-red-600
                                                   text-slate-400 hover:text-white text-sm font-semibold
                                                   rounded-sm transition border border-slate-600">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete Collection
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT
    ════════════════════════════════════════ --}}
    <div class="max-w-6xl mx-auto px-6 lg:px-16 py-10
                flex flex-col lg:flex-row gap-10 lg:gap-14">

        {{-- ── LEFT: Books grid ── --}}
        <div class="flex-1 min-w-0">

            @if(session('success'))
                <div class="mb-5 bg-green-500/10 border border-green-500/30 text-green-400
                            text-sm rounded-sm px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400
                            text-sm rounded-sm px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <h2 class="text-lg font-bold text-white mb-5 flex items-center justify-between">
                <span>List Books</span>
                <span class="text-slate-500 text-sm font-normal">
                    {{ number_format($collection->books_count) }} buku
                </span>
            </h2>

            @if($collection->books->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center
                            border border-dashed border-slate-700 rounded-s">
                    <svg class="w-12 h-12 text-slate-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-slate-500 text-sm">Belum ada buku di koleksi ini.</p>
                </div>
            @else
                <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                    @foreach($collection->books as $book)
                        <div class="group flex flex-col">
                            <a href="{{ route('books.show', $book->external_id) }}">
                                <div class="aspect-[3/4] bg-slate-800 rounded-sm overflow-hidden mb-2
                                            border border-slate-700
                                            group-hover:border-purple-500 group-hover:-translate-y-1
                                            transition-all duration-300
                                            group-hover:shadow-lg group-hover:shadow-purple-900/30">
                                    @if($book->cover)
                                        <img src="{{ $book->cover }}" alt="{{ $book->title }}"
                                             class="w-full h-full object-cover transition-transform
                                                    duration-500 group-hover:scale-105"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-slate-600 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-white text-[11px] font-medium leading-tight truncate
                                          group-hover:text-purple-300 transition">
                                    {{ $book->title }}
                                </p>
                                <p class="text-slate-500 text-[10px] mt-0.5 truncate">
                                    {{ $book->author_name ?? 'Unknown' }}
                                </p>
                                <div class="flex mt-1 gap-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <svg class="w-2.5 h-2.5 {{ $s <= round($book->averageRating) ? 'text-yellow-400' : 'text-slate-700' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </a>

                            {{-- Hapus buku — hanya pemilik koleksi --}}
                            @auth
                                @if(Auth::id() === $collection->user_id)
                                    <form action="{{ route('collections.books.remove', $collection->id) }}"
                                          method="POST" class="mt-1.5">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="submit"
                                                class="w-full text-[10px] text-slate-600 hover:text-red-400
                                                       transition text-center">
                                            Delete from collection
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── RIGHT: Comments ── --}}
        <div class="w-full lg:w-80 shrink-0">

            @auth
                <div class="bg-slate-800 border border-slate-700 rounded-s p-5 mb-6">
                    <p class="text-white font-semibold text-sm mb-3">Write a Comment</p>
                    <form action="{{ route('collections.comments.store', $collection->id) }}" method="POST">
                        @csrf
                        {{-- Star rating opsional --}}
                        <div class="flex gap-1 mb-3">
                            @foreach(range(1,5) as $s)
                                <button type="button" data-val="{{ $s }}"
                                        class="comment-star text-slate-600 hover:text-yellow-400 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="rating" id="comment-rating" value="">

                        <textarea name="body" rows="3"
                                  placeholder="Bagikan pendapatmu tentang koleksi ini..."
                                  class="w-full bg-slate-900 border border-slate-700 text-white text-sm rounded-sm
                                         px-4 py-3 resize-none outline-none focus:ring-2 focus:ring-purple-500
                                         placeholder-slate-600 transition
                                         @error('body') border-red-500 @enderror">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end mt-3">
                            <button type="submit"
                                    class="flex items-center gap-2 px-5 py-2 bg-purple-600 hover:bg-purple-500
                                           text-white text-sm font-semibold rounded-sm transition
                                           shadow-md shadow-purple-900/40">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                POST
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-slate-800 border border-slate-700 rounded-s p-5 mb-6 text-center">
                    <p class="text-slate-400 text-sm mb-3">Login to write a comment</p>
                    <a href="{{ route('login') }}"
                       class="inline-block px-5 py-2 bg-purple-600 hover:bg-purple-500
                              text-white text-sm font-semibold rounded-sm transition">
                        Login
                    </a>
                </div>
            @endauth

            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">
                    Comments · {{ $collection->comments_count }}
                </h3>

                @forelse($collection->comments as $comment)
                    @php
                        $colors = [
                            'bg-purple-500/20 text-purple-300',
                            'bg-blue-500/20 text-blue-300',
                            'bg-green-500/20 text-green-300',
                            'bg-orange-500/20 text-orange-300',
                            'bg-pink-500/20 text-pink-300',
                        ];
                        $color = $colors[$comment->id % count($colors)];
                    @endphp
                    <div class="bg-slate-800/60 border border-slate-700/60 rounded-sm p-4
                                hover:border-slate-600 transition">
                        <div class="flex items-center gap-2.5 mb-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center
                                        text-xs font-bold shrink-0 {{ $color }}">
                                {{ strtoupper(substr($comment->author->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-white text-xs font-semibold truncate">
                                        {{ $comment->author->name ?? 'Unknown' }}
                                    </span>
                                    @if($comment->rating)
                                        <div class="flex shrink-0">
                                            @for($s = 1; $s <= 5; $s++)
                                                <svg class="w-2.5 h-2.5 {{ $s <= $comment->rating ? 'text-yellow-400' : 'text-slate-700' }}"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    @endif
                                </div>
                                <p class="text-slate-500 text-[10px] mt-0.5">
                                    {{ $comment->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <p class="text-slate-300 text-sm leading-relaxed">{{ $comment->body }}</p>

                        <div class="flex items-center justify-between mt-3">
                            <form action="{{ route('collections.comments.like', $comment->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-1.5 text-xs transition
                                               {{ Auth::check() && $comment->isLikedBy(Auth::user())
                                                   ? 'text-red-400'
                                                   : 'text-slate-600 hover:text-red-400' }}">
                                    <svg class="w-3.5 h-3.5"
                                         fill="{{ Auth::check() && $comment->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                    {{ $comment->likes_count }}
                                </button>
                            </form>

                            @auth
                                @if(Auth::id() === $comment->user_id)
                                    <form action="{{ route('collections.comments.destroy', $comment->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this comment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-slate-600 hover:text-red-400 text-xs transition">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <button class="text-slate-600 hover:text-slate-400 text-xs transition">Report</button>
                                @endif
                            @else
                                <button class="text-slate-600 hover:text-slate-400 text-xs transition">Report</button>
                            @endauth
                        </div>
                    </div>
                @empty
                    <p class="text-slate-600 text-sm text-center py-6">
                        No comments yet. Be the first to comment!
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
const stars        = document.querySelectorAll('.comment-star');
const ratingInput  = document.getElementById('comment-rating');
let selectedRating = 0;

stars.forEach(btn => {
    btn.addEventListener('mouseenter', () => {
        const val = +btn.dataset.val;
        stars.forEach(b => {
            b.classList.toggle('text-yellow-400', +b.dataset.val <= val);
            b.classList.toggle('text-slate-600',  +b.dataset.val >  val);
        });
    });
    btn.addEventListener('mouseleave', () => {
        stars.forEach(b => {
            b.classList.toggle('text-yellow-400', +b.dataset.val <= selectedRating);
            b.classList.toggle('text-slate-600',  +b.dataset.val >  selectedRating);
        });
    });
    btn.addEventListener('click', () => {
        selectedRating    = +btn.dataset.val;
        ratingInput.value = selectedRating;
    });
});
</script>
@endsection