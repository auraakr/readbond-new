@extends('layouts.profile')

@section('title', $user->username . "'s Activity")

@section('content')

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-hide: none; }
</style>

    {{-- ── ACTIVITY LIST ── --}}
    <section class="max-w-6xl mx-auto px-6 py-10">
        <p class="text-[10px] py-3 font-semibold uppercase tracking-widest text-slate-500">Recent Activity</p>
        
        <div class="space-y-3">
            @forelse($allActivity as $activity)
            <div class="flex items-start gap-3 p-3 border-b border-white/5 hover:border-purple-500/30 transition group">
                {{-- Activity Icon --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                    {{ $activity['type'] === 'like' ? 'bg-red-600/20 text-red-400' : 
                    ($activity['type'] === 'rating' ? 'bg-yellow-600/20 text-yellow-400' : 
                    ($activity['type'] === 'review' ? 'bg-blue-600/20 text-blue-400' : 
                    ($activity['type'] === 'club_join' ? 'bg-emerald-600/20 text-emerald-400' : 
                    ($activity['type'] === 'diary' ? 'bg-cyan-600/20 text-cyan-400' : 'bg-purple-600/20 text-purple-400')))) }}">
                    
                    @if($activity['type'] === 'like')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($activity['type'] === 'rating')
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @elseif($activity['type'] === 'review')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    @elseif($activity['type'] === 'club_join')
                        {{-- Ikon Users untuk Club --}}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    @elseif($activity['type'] === 'diary')
                        {{-- Ikon Journal/Calendar untuk Diary --}}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 3V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    @endif
                </div>

                {{-- Activity Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-white">
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

                        {{-- Render Link Target Berdasarkan Objek Eksis --}}
                        @if(isset($activity['book']) && $activity['book'] !== null)
                            <a href="{{ route('books.show', $activity['book']->external_id) }}" 
                            class="font-semibold hover:text-purple-400 transition">
                                {{ $activity['book']->title }}
                            </a>
                        @elseif(isset($activity['club']) && $activity['club'] !== null)
                            <a href="{{ route('clubs.show', $activity['club']->slug) }}" 
                            class="font-semibold hover:text-emerald-400 transition">
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

                {{-- Book Cover Thumbnail (Hanya dirender jika relasi book tidak null) --}}
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
    </section>

@endsection
