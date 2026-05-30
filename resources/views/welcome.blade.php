@extends('layouts.main')

@section('title', 'Readbond - Welcome to Readbond')

@section('content')
    <!-- Hero Section — hanya untuk guest -->
    @guest
    <div class="relative min-h-[600px] flex items-center justify-center overflow-hidden bg-slate-900 pt-16 pb-32">
        
        <div class="absolute inset-0 z-0 opacity-25 pointer-events-none">
            <div class="grid grid-cols-4 md:grid-cols-6 gap-4 transform -rotate-12 scale-125">
                @foreach(range(1, 12) as $i)
                    <div class="space-y-4">
                        <x-book-cover-mock src="{{ asset('images/hero/book-cover-' . $i . '.jpg') }}" color="{{ 'bg-slate-800' }}" />
                    </div>
                @endforeach
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900 via-transparent to-slate-900"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="lg:mt-40 max-w-3xl mx-auto">
                <h1 class="font-brand text-5xl md:text-7xl font-black text-white leading-tight">
                    Track, Note, Share <br> 
                    <span class="text-purple-500 italic">Your Favorite Stories</span>
                </h1>
                <p class="mt-8 text-xl text-slate-300 leading-relaxed">
                    Readbond is your personal book companion for tracking what you read, sharing your thoughts, and connecting with fellow book lovers.
                </p>
                <div class="mt-12">
                    <a href="/register" class="uppercase mt-5 px-10 py-4 bg-purple-600 text-white rounded-sm font-bold text-lg hover:bg-purple-700 transition-all hover:scale-105 shadow-xl shadow-purple-500/30">
                        Get Started Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endguest

    {{-- ── Aktivitas Teman (hanya untuk user login) ── --}}
    @auth
    <div class="bg-slate-900 py-10 px-6 lg:px-32">
        <h2 class="text-3xl font-black text-white tracking-tight">Welcome back, {{ Auth::user()->name }}! See what your friends are reading.</h2>
        <p class="text-slate-400 mt-2">Here are the latest updates from your friends.</p>
        <div class="grid grid-cols-2 py-4 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($friendsActivity as $activity)
            @php
                $statusLabel = match($activity->status) {
                    'reading' => 'Sedang membaca',
                    'finished' => 'Selesai membaca',
                    'want_to_read' => 'Ingin membaca',
                };
                $statusColor = match($activity->status) {
                    'reading' => 'text-blue-400',
                    'finished' => 'text-green-400',
                    'want_to_read' => 'text-purple-400',
                };
            @endphp

            <a href="{{ route('books.show', $activity->book->external_id) }}"
            class="group aspect-[3/5] rounded-sm overflow-hidden border border-slate-700 bg-slate-800 transition-all hover:border-purple-500/50 hover:shadow-lg hover:shadow-purple-500/10">
                <div class="relative w-full h-full">

                    {{-- Book Cover as Background --}}
                    @if($activity->book->cover_url)
                        <img src="{{ $activity->book->cover_url }}" alt="{{ $activity->book->title }}"
                            class="absolute inset-0 w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-slate-700 text-slate-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168 0.477 4 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332 0.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332 0.477-4 1.253"></path></svg>
                        </div>
                    @endif

                    {{-- Gradient Overlays --}}
                    {{-- Top gradient for user info --}}
                    <div class="absolute inset-x-0 top-0 h-1/7 lg:h-1/5 bg-purple-600"></div>
                    {{-- Bottom gradient for text readability --}}
                    <div class="absolute inset-x-0 bottom-0 h-1/5 lg:h-1/4 bg-black/40"></div>

                    {{-- Content Container --}}
                    <div class="absolute inset-0 p-3 flex flex-col justify-between z-10">
                        
                        {{-- TOP: User Info Overlay --}}
                        <div class="flex items-center gap-2">
                            @if($activity->user->avatar)
                                <img src="{{ asset('storage/' . $activity->user->avatar) }}"
                                    class="w-6 h-6 rounded-full object-cover border border-slate-600 shrink-0">
                            @else
                                <div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0 border border-slate-600">
                                    {{ strtoupper(substr($activity->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-white text-xs font-medium truncate group-hover:text-purple-300 transition-colors">
                                {{ $activity->user->name }}
                            </span>
                        </div>

                        {{-- BOTTOM: Book Title and Status --}}
                        <div class="space-y-1.5">
                            {{-- Status Label and Time --}}
                            <div class="flex items-center justify-between gap-2 text-[10px] font-medium {{ $statusColor }}">
                                <span class="truncate">
                                    {{ $statusLabel }}
                                </span>
                                <span class="text-slate-400 shrink-0">
                                    {{ $activity->updated_at->diffForHumans(short: true) }}
                                </span>
                            </div>

                            {{-- Book title --}}
                            <h3 class="text-white text-sm font-semibold leading-snug line-clamp-1 group-hover:text-purple-200 transition-colors">
                                {{ $activity->book->title }}
                            </h3>
                            
                            {{-- Author (Optional) --}}
                            {{-- @if($activity->book->author_name)
                            <p class="text-slate-300 text-xs truncate">{{ $activity->book->author_name }}</p>
                            @endif --}}
                        </div>
                    </div>

                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endauth

    <div class="bg-slate-900 py-10 px-6 lg:px-32">
    
        <section class="mb-20">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">Popular Books</h2>
                    <p class="text-slate-400 mt-2">Paling banyak dibaca minggu ini</p>
                </div>
                <a href="/books" class="text-purple-400 hover:text-purple-300 font-bold text-sm">View All →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($popularBooks as $book)
                    <div class="group cursor-pointer">
                        <div class="aspect-[2/3] rounded-sm overflow-hidden bg-slate-800 mb-4 shadow-md group-hover:ring-1 group-hover:ring-purple-200 transition-all">
                            @if($book->cover)
                                <img 
                                    src="{{ $book->cover_url }}"
                                    alt="{{ $book->title }}"
                                    class="w-full h-full object-cover transition duration-500" 
                                />
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs p-4 text-center">No Cover Available</div>
                            @endif
                        </div>
                        <h3 class="text-white font-bold text-sm truncate">{{ $book->title }}</h3>
                        <p class="text-slate-500 text-xs truncate">{{ $book->author_name ?? 'Unknown Author' }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section>
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">Most Reviewed</h2>
                    <p class="text-slate-400 mt-2">Buku dengan diskusi paling hangat</p>
                </div>
                <a href="/books" class="text-purple-400 hover:text-purple-300 font-bold text-sm">View All →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-2">
                @foreach($mostReviewed as $book)
                    <div class="group cursor-pointer">
                        <div class="aspect-[2/3] rounded-sm overflow-hidden bg-slate-800 mb-4 shadow-md group-hover:ring-1 group-hover:ring-purple-200 transition-all">
                            @if(isset($book['cover_i']))
                                <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg" 
                                    class="w-full h-full object-cover transition duration-500" 
                                    alt="{{ $book['title'] }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs p-4 text-center">No Cover Available</div>
                            @endif
                        </div>
                        <h3 class="text-white font-bold text-sm truncate">{{ $book['title'] }} - {{ $book['author_name'][0] ?? 'Unknown Author' }}</h3>
                        <p class="text-slate-500 text-xs truncate">{{ number_format(rand(100, 999)) }} reviews</p>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

    <!-- Features Section -->
    <section>
        <div class="rb-features-wrap py-20 px-6 lg:px-32">
            <div style="max-width:1200px;margin:0 auto;">
                <p class="rb-section-label">Features</p>
                <h2 class="rb-section-title" style="margin-bottom:4px;">Everything You Need</h2>
                <p class="rb-section-sub" style="margin-bottom:48px;">All in one beautiful platform</p>
            </div>
            <div style="max-width:1200px;margin:0 auto;">
                <div class="rb-features-grid">
    
                    <div class="rb-feature-item">
                        <div class="rb-feature-icon">
                            <x-heroicon-s-book-open class="w-5 h-5" />
                        </div>
                        <p class="rb-feature-title">Track Your Reading</p>
                        <p class="rb-feature-desc">Keep a beautiful log of all the books you've read, are reading, and want to read.</p>
                    </div>
    
                    <div class="rb-feature-item">
                        <div class="rb-feature-icon">
                            <x-heroicon-s-star class="w-5 h-5" />
                        </div>
                        <p class="rb-feature-title">Rate &amp; Review</p>
                        <p class="rb-feature-desc">Share honest thoughts and ratings. Help other readers find their next favorite book.</p>
                    </div>
    
                    <div class="rb-feature-item">
                        <div class="rb-feature-icon">
                            <x-heroicon-o-user-group class="w-5 h-5" />
                        </div>
                        <p class="rb-feature-title">Join Book Clubs</p>
                        <p class="rb-feature-desc">Connect with fellow readers in virtual clubs and discuss your favorite titles.</p>
                    </div>
    
                    <div class="rb-feature-item">
                        <div class="rb-feature-icon">
                            <x-heroicon-o-bolt class="w-5 h-5" />
                        </div>
                        <p class="rb-feature-title">Get Recommendations</p>
                        <p class="rb-feature-desc">Discover personalized picks based on your reading history and preferences.</p>
                    </div>
    
                    <div class="rb-feature-item">
                        <div class="rb-feature-icon">
                            <x-heroicon-o-chart-bar class="w-5 h-5" />
                        </div>
                        <p class="rb-feature-title">Daily Streaks</p>
                        <p class="rb-feature-desc">Maintain reading momentum with diary logs, daily streaks, and reading goals.</p>
                    </div>
    
                    <div class="rb-feature-item">
                        <div class="rb-feature-icon">
                            <x-heroicon-o-face-smile class="w-5 h-5" />
                        </div>
                        <p class="rb-feature-title">Share &amp; Connect</p>
                        <p class="rb-feature-desc">Follow friends, share your journey, and celebrate reading milestones together.</p>
                    </div>
    
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Reviews Section -->
    <section class="bg-slate-900 py-16 px-6 lg:px-32">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">Popular Reviews</h2>
                <p class="text-slate-400 mt-2">Ulasan terpopuler dari komunitas pembaca</p>
            </div>
            <a href="/books" class="text-purple-400 hover:text-purple-300 font-bold text-sm">View All →</a>
        </div>

        @if($popularReviews->isEmpty())
            <p class="text-slate-500 text-sm">Belum ada ulasan. Jadilah yang pertama!</p>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($popularReviews as $review)
            <a href="{{ route('books.show', $review->book->external_id) }}"
               class="group flex flex-col bg-slate-800 rounded-sm border border-slate-700 hover:border-purple-500/50 hover:shadow-lg hover:shadow-purple-500/10 transition-all overflow-hidden">

                {{-- Book header --}}
                <div class="flex items-center gap-3 p-4 border-b border-slate-700">
                    <div class="w-10 h-14 rounded-sm overflow-hidden bg-slate-700 shrink-0">
                        @if($review->book && $review->book->cover_url)
                            <img src="{{ $review->book->cover_url }}" alt="{{ $review->book->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-500">
                                <x-heroicon-o-book-open class="w-4 h-4" />
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-white font-semibold text-sm truncate group-hover:text-purple-300 transition-colors">
                            {{ $review->book->title ?? 'Unknown Book' }}
                        </p>
                        <p class="text-slate-500 text-xs truncate">{{ $review->book->author_name ?? '' }}</p>
                        {{-- Stars --}}
                        <div class="flex items-center gap-0.5 mt-1">
                            @for($s = 1; $s <= 5; $s++)
                                <span class="{{ $s <= $review->rating ? 'text-yellow-400' : 'text-slate-600' }} text-xs">★</span>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- Review body --}}
                <div class="p-4 flex-1">
                    <p class="text-slate-300 text-sm leading-relaxed line-clamp-4">{{ $review->review }}</p>
                </div>

                {{-- Reviewer footer --}}
                <div class="px-4 pb-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($review->user->avatar)
                            <img src="{{ asset('storage/' . $review->user->avatar) }}"
                                 class="w-7 h-7 rounded-full object-cover border border-slate-600">
                        @else
                            <div class="w-7 h-7 rounded-full bg-purple-600 flex items-center justify-center text-white text-[10px] font-bold border border-slate-600">
                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-slate-400 text-xs">{{ $review->user->name }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-slate-500 text-xs">
                        <x-heroicon-o-heart class="w-3.5 h-3.5" />
                        <span>{{ number_format($review->likes_count) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif
    </section>

    <!-- Popular Collections Section -->
    <section class="bg-slate-900 py-16 px-6 lg:px-32 border-t border-slate-800">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">Popular Collections</h2>
                <p class="text-slate-400 mt-2">Book collections curated by our community</p>
            </div>
            <a href="/collections" class="text-purple-400 hover:text-purple-300 font-bold text-sm">View All →</a>
        </div>

        @if($popularCollections->isEmpty())
            <p class="text-slate-500 text-sm">No popular collections found.</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($popularCollections as $collection)
            @php $covers = $collection->books->pluck('cover_url')->filter()->values(); @endphp
            <a href="/collections/{{ $collection->id }}"
               class="group bg-slate-800 rounded-sm border border-slate-700 hover:border-purple-500/50 hover:shadow-lg hover:shadow-purple-500/10 transition-all overflow-hidden flex flex-col">

                {{-- Cover collage --}}
                <div class="relative aspect-[16/9] bg-slate-700 overflow-hidden">
                    @if($covers->count() >= 4)
                        <div class="grid grid-cols-4 h-full">
                            @foreach($covers->take(4) as $cover)
                                <img src="{{ $cover }}" class="w-full h-full object-cover" alt="">
                            @endforeach
                        </div>
                    @elseif($covers->count() >= 1)
                        <img src="{{ $covers->first() }}" class="w-full h-full object-cover" alt="{{ $collection->title }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-500">
                            <x-heroicon-o-rectangle-stack class="w-10 h-10" />
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 right-3">
                        <p class="text-white font-bold text-base leading-snug line-clamp-2 group-hover:text-purple-300 transition-colors">
                            {{ $collection->title }}
                        </p>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($collection->curator && $collection->curator->avatar)
                            <img src="{{ asset('storage/' . $collection->curator->avatar) }}"
                                 class="w-6 h-6 rounded-full object-cover border border-slate-600">
                        @else
                            <div class="w-6 h-6 rounded-full bg-purple-600 flex items-center justify-center text-white text-[10px] font-bold">
                                {{ strtoupper(substr($collection->curator->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-slate-400 text-xs">{{ $collection->curator->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-500 text-xs">
                        <span>{{ number_format($collection->books_count) }} books</span>
                        <div class="flex items-center gap-1">
                            <x-heroicon-o-heart class="w-3.5 h-3.5" />
                            <span>{{ number_format($collection->likes_count) }}</span>
                        </div>
                    </div>
                </div>

                @if($collection->description)
                <p class="px-4 pb-4 text-slate-500 text-xs line-clamp-2">{{ $collection->description }}</p>
                @endif
            </a>
            @endforeach
        </div>
        @endif
    </section>

    <!-- CTA Section -->
    <section>
        <!-- ── CTA ── -->
        <div class="rb-cta-wrap">
            <div class="rb-cta-inner">
                <p class="rb-section-label" style="margin-bottom:16px;">Start Today</p>
                <h2 class="rb-serif rb-cta-title">Ready for Your<br>Reading Journey?</h2>
                <p class="rb-cta-sub">Join thousands of book lovers tracking, reviewing, and sharing their favorite reads on Readbond.</p>
                <div class="rb-cta-actions">
                    <a href="/register" class="rb-btn-primary">Get Started Free</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <p class="text-xl font-bold text-purple-500 mb-2">Readbond</p>
                    <p class="text-slate-400 text-sm">Your personal book companion for tracking and sharing reads.</p>
                </div>
                <div>
                    <p class="font-semibold text-white mb-4">Product</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-purple-400 transition">Features</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Mobile App</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-white mb-4">Community</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-purple-400 transition">Book Clubs</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Forums</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-white mb-4">Legal</p>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-purple-400 transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Terms</a></li>
                        <li><a href="#" class="hover:text-purple-400 transition">Contact</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 text-center text-slate-400 text-sm">
                <p>&copy; 2026 Readbond. All rights reserved. Made with coffee and matcha for book lovers.</p>
            </div>
        </div>
    </footer>
@endsection