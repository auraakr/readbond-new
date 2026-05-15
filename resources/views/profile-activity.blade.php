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
                            ($activity['type'] === 'review' ? 'bg-blue-600/20 text-blue-400' : 'bg-purple-600/20 text-purple-400')) }}">
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
                        @elseif($activity['type'] === 'reading_log')
                            @if($activity['status'] === 'want_to_read')
                                wants to read
                            @elseif($activity['status'] === 'reading')
                                is reading
                            @elseif($activity['status'] === 'finished')
                                finished
                            @endif
                        @endif
                        <a href="{{ route('books.show', $activity['book']->external_id) }}" 
                        class="font-semibold hover:text-purple-400 transition">
                            {{ $activity['book']->title }}
                        </a>
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

                {{-- Book Cover Thumbnail --}}
                <a href="{{ route('books.show', $activity['book']->external_id) }}" 
                class="flex-shrink-0 w-12 h-16 rounded overflow-hidden bg-slate-800 border border-white/10 group-hover:border-purple-500/50 transition">
                    @if($activity['book']->cover_url)
                        <img src="{{ $activity['book']->cover_url }}" 
                            class="w-full h-full object-cover" 
                            alt="{{ $activity['book']->title }}">
                    @endif
                </a>
            </div>
            @empty
            <div class="text-center py-8 text-slate-500 text-sm">
                No activity yet
            </div>
            @endforelse
        </div>
    </section>

    <!-- <section class="max-w-6xl mx-auto px-6 py-10">
        <div class="flex items-center justify-between mb-4">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500">Recent Activity</p>
            <a href="#" class="text-[10px] font-semibold uppercase tracking-widest text-slate-600 hover:text-purple-400 transition">All</a>
        </div>
        
        <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
            @forelse($allActivity as $activity)
            <div class="group relative">
                {{-- Book Cover --}}
                <a href="{{ route('books.show', $activity['book']->external_id) }}" 
                class="block aspect-[2/3] rounded-sm overflow-hidden bg-slate-800 border border-white/5 group-hover:border-purple-500/40 transition mb-2 relative">
                    @if($activity['book']->cover_url)
                        <img src="{{ $activity['book']->cover_url }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300" 
                            alt="{{ $activity['book']->title }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs p-2 text-center">
                            {{ $activity['book']->title }}
                        </div>
                    @endif

                    {{-- Activity Badge --}}
                    <div class="absolute top-2 right-2 w-7 h-7 rounded-full flex items-center justify-center shadow-lg
                                {{ $activity['type'] === 'like' ? 'bg-red-600/90' : 
                                ($activity['type'] === 'rating' ? 'bg-yellow-600/90' : 
                                ($activity['type'] === 'review' ? 'bg-blue-600/90' : 'bg-purple-600/90')) }}">
                        @if($activity['type'] === 'like')
                            {{-- Heart Icon --}}
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                            </svg>
                        @elseif($activity['type'] === 'rating')
                            {{-- Star Icon --}}
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @elseif($activity['type'] === 'review')
                            {{-- Pencil Icon --}}
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        @else
                            {{-- Book Icon for reading log --}}
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        @endif
                    </div>
                </a>

                {{-- Activity Info --}}
                <div class="space-y-1">
                    {{-- Activity Description --}}
                    <p class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">
                        @if($activity['type'] === 'like')
                            Liked
                        @elseif($activity['type'] === 'rating')
                            Rated
                        @elseif($activity['type'] === 'review')
                            Reviewed
                        @elseif($activity['type'] === 'reading_log')
                            @if($activity['status'] === 'want_to_read')
                                Want to Read
                            @elseif($activity['status'] === 'reading')
                                Currently Reading
                            @elseif($activity['status'] === 'finished')
                                Finished
                            @endif
                        @endif
                    </p>

                    {{-- Rating Stars (if applicable) --}}
                    @if($activity['rating'])
                    <div class="flex items-center gap-0.5">
                        @for($s = 1; $s <= 5; $s++)
                            <span class="text-[11px] {{ $s <= $activity['rating'] ? 'text-yellow-400' : 'text-slate-700' }}">★</span>
                        @endfor
                    </div>
                    @endif

                    {{-- Timestamp --}}
                    <p class="text-[9px] text-slate-600">
                        {{ $activity['created_at']->diffForHumans() }}
                    </p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-sm text-slate-500">No activity yet</p>
                <p class="text-xs text-slate-600 mt-1">Start reading and tracking books to see activity here</p>
            </div>
            @endforelse
        </div>
    </section> -->

@endsection
