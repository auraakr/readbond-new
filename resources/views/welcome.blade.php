@extends('layouts.main')

@section('title', 'Readbond - Welcome to Readbond')

@section('content')
    <!-- Hero Section -->
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

    <div class="bg-slate-900 py-20 px-6 lg:px-32">
    
        <section class="mb-20">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-black text-white tracking-tight">Popular Books</h2>
                    <p class="text-slate-400 mt-2">Paling banyak dibaca minggu ini</p>
                </div>
                <a href="/books" class="text-purple-400 hover:text-purple-300 font-bold text-sm">View All →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($popularBooks as $book)
                    <div class="group cursor-pointer">
                        <div class="aspect-[3/4] rounded-sm overflow-hidden bg-slate-800 mb-4 shadow-md group-hover:ring-1 group-hover:ring-purple-200 transition-all">
                            @if(isset($book['cover_i']))
                                <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg" 
                                    class="w-full h-full object-cover transition duration-500" 
                                    alt="{{ $book['title'] }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs p-4 text-center">No Cover Available</div>
                            @endif
                        </div>
                        <h3 class="text-white font-bold text-sm truncate">{{ $book['title'] }}</h3>
                        <p class="text-slate-500 text-xs truncate">{{ $book['author_name'][0] ?? 'Unknown Author' }}</p>
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
                        <div class="aspect-[3/4] rounded-sm overflow-hidden bg-slate-800 mb-4 shadow-md group-hover:ring-1 group-hover:ring-purple-200 transition-all">
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

    <!-- Social Proof Section -->
    <section>
        <div class="rb-section py-20 px-6 lg:px-32">
            <p class="rb-section-label">Testimonials</p>
            <h2 class="rb-section-title" style="margin-bottom:40px;">What Readers Say</h2>
    
            <div class="rb-testimonials-grid">
    
                <div class="rb-testimonial">
                    <div class="rb-testimonial__stars">★★★★★</div>
                    <p class="rb-testimonial__quote">"Readbond has transformed how I track and share my reading. The interface is beautiful and so easy to use!"</p>
                    <div class="rb-testimonial__author">
                        <div class="rb-avatar" style="background:rgba(139,92,246,0.15);color:#8b5cf6;">SM</div>
                        <div>
                            <p class="rb-testimonial__name">Sarah Miller</p>
                            <p class="rb-testimonial__role">Book Enthusiast</p>
                        </div>
                    </div>
                </div>
    
                <div class="rb-testimonial">
                    <div class="rb-testimonial__stars">★★★★★</div>
                    <p class="rb-testimonial__quote">"The book clubs feature is fantastic! I've found my reading community and discovered amazing recommendations."</p>
                    <div class="rb-testimonial__author">
                        <div class="rb-avatar" style="background:rgba(232,180,75,0.12);color:#e8b44b;">JC</div>
                        <div>
                            <p class="rb-testimonial__name">James Chen</p>
                            <p class="rb-testimonial__role">Fiction Lover</p>
                        </div>
                    </div>
                </div>
    
                <div class="rb-testimonial">
                    <div class="rb-testimonial__stars">★★★★★</div>
                    <p class="rb-testimonial__quote">"Finally, a reading platform that gets it. Beautiful design, powerful features, and a welcoming community."</p>
                    <div class="rb-testimonial__author">
                        <div class="rb-avatar" style="background:rgba(29,158,117,0.12);color:#1d9e75;">ER</div>
                        <div>
                            <p class="rb-testimonial__name">Emma Rodriguez</p>
                            <p class="rb-testimonial__role">Mystery Reader</p>
                        </div>
                    </div>
                </div>
    
            </div>
        </div>
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