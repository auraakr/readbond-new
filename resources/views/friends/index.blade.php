@extends('layouts.main')

@section('content')
<div class="bg-slate-950 min-h-screen text-slate-300 font-sans selection:bg-purple-500 selection:text-white">
    <div class="max-w-6xl mx-auto px-6 py-12 space-y-16">
        
        {{-- Header Halaman --}}
        <div class="border-b border-white/5 pb-4">
            <h1 class="text-xl font-normal text-slate-100 tracking-wide">
                Book lovers, critics and friends — <span class="text-slate-400">find popular members.</span>
            </h1>
        </div>

        {{-- ── SECTION 1: FEATURED MEMBERS ── --}}
        <section>
            <h2 class="text-xs font-semibold tracking-widest text-slate-500 uppercase border-b border-white/5 pb-2 mb-8">
                Featured Members
            </h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($featuredMembers as $member)
                <div class="flex flex-col items-center text-center group">
                    {{-- Avatar Wrapper --}}
                    <div class="relative w-28 h-28 mb-4" 
                        x-data="{ 
                            isFollowed: {{ $member->is_followed ? 'true' : 'false' }},
                            loading: false,
                            async toggleFollow() {
                                @guest
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                @endguest

                                if (this.loading) return;
                                this.loading = true;

                                try {
                                    const response = await fetch('{{ route('user.follow', $member->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    });

                                    const data = await response.json();
                                    
                                    if (data.success) {
                                        this.isFollowed = data.is_following;
                                        
                                        // Optional: Show toast notification
                                        console.log(data.message);
                                    } else {
                                        console.error('Follow failed:', data.error);
                                        alert(data.error || 'Failed to follow/unfollow');
                                    }
                                } catch (error) {
                                    console.error('Network error:', error);
                                    alert('Network error. Please try again.');
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }">
                        
                        @if($member->avatar_url)
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" 
                                class="w-full h-full rounded-full object-cover border-2 border-slate-800 group-hover:border-purple-500/50 transition duration-300">
                        @else
                            <div class="w-full h-full rounded-full bg-slate-800 text-slate-400 border-2 border-slate-700 flex items-center justify-center text-2xl font-bold uppercase group-hover:border-purple-500/50 transition duration-300">
                                {{ substr($member->name ?? $member->username, 0, 1) }}
                            </div>
                        @endif

                        {{-- Follow Button --}}
                        @if(Auth::id() !== $member->id)
                            <button @click="toggleFollow()" 
                                    :disabled="loading"
                                    :class="isFollowed ? 'bg-purple-600 border-purple-400 text-white' : 'bg-slate-800 hover:bg-purple-600 border-white/10 text-white'"
                                    class="absolute bottom-1 right-1 border w-6 h-6 rounded-full flex items-center justify-center transition shadow-lg z-10 disabled:opacity-50 disabled:cursor-not-allowed">
                                
                                {{-- Loading Spinner --}}
                                <svg x-show="loading" class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                
                                {{-- Plus Icon --}}
                                <span x-show="!isFollowed && !loading" class="text-sm font-bold">+</span>
                                
                                {{-- Checkmark Icon --}}
                                <svg x-show="isFollowed && !loading" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Informasi Profil --}}
                    <a href="{{ route('profile', ['user' => $member->username]) }}" class="font-semibold text-slate-200 hover:text-purple-400 transition text-sm truncate w-full px-2">
                        {{ $member->name ?? $member->username }}
                    </a>
                    <p class="text-[11px] text-slate-500 mt-0.5 tracking-wide">
                        {{ number_format($member->books_count) }} books • {{ number_format($member->reviews_count) }} reviews
                    </p>

                    {{-- Mini Grid Cover Buku (4 Buku Terakhir) --}}
                    <div class="grid grid-cols-4 gap-1 mt-4 w-full px-1">
                        @foreach($member->readingLogs as $log)
                            @if($log->book)
                            <a href="{{ route('books.show', $log->book->external_id) }}" 
                               class="aspect-[2/3] bg-slate-900 rounded overflow-hidden border border-white/5 hover:border-purple-500/40 transition shadow-md block"
                               title="{{ $log->book->title }}">
                                @if($log->book->cover_url)
                                    <img src="{{ $log->book->cover_url }}" class="w-full h-full object-cover" alt="{{ $log->book->title }}">
                                @endif
                            </a>
                            @endif
                        @endforeach
                        {{-- Placeholder jika buku yang diselesaikan kurang dari 4 --}}
                        @for($i = count($member->readingLogs); $i < 4; $i++)
                            <div class="aspect-[2/3] bg-slate-900/40 border border-dashed border-white/5 rounded"></div>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ── SECTION 2: POPULAR THIS WEEK ── --}}
        <section>
            <div class="flex justify-between items-baseline border-b border-white/5 pb-2 mb-8">
                <h2 class="text-xs font-semibold tracking-widest text-slate-500 uppercase">
                    Popular This Week
                </h2>
                <a href="#" class="text-[11px] text-slate-500 hover:text-white uppercase tracking-wider font-semibold transition">More</a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                @foreach($popularMembers as $member)
                <div class="flex flex-col items-center text-center group">
                    {{-- Avatar Wrapper --}}
                    <div class="relative w-28 h-28 mb-4" 
                        x-data="{ 
                            isFollowed: {{ $member->is_followed ? 'true' : 'false' }},
                            loading: false,
                            async toggleFollow() {
                                @guest
                                    window.location.href = '{{ route('login') }}';
                                    return;
                                @endguest

                                if (this.loading) return;
                                this.loading = true;

                                try {
                                    const response = await fetch('{{ route('user.follow', $member->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        }
                                    });

                                    const data = await response.json();
                                    
                                    if (data.success) {
                                        this.isFollowed = data.is_following;
                                        
                                        // Optional: Show toast notification
                                        console.log(data.message);
                                    } else {
                                        console.error('Follow failed:', data.error);
                                        alert(data.error || 'Failed to follow/unfollow');
                                    }
                                } catch (error) {
                                    console.error('Network error:', error);
                                    alert('Network error. Please try again.');
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }">
                        
                        @if($member->avatar_url)
                            <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" 
                                class="w-full h-full rounded-full object-cover border-2 border-slate-800 group-hover:border-purple-500/50 transition duration-300">
                        @else
                            <div class="w-full h-full rounded-full bg-slate-800 text-slate-400 border-2 border-slate-700 flex items-center justify-center text-2xl font-bold uppercase group-hover:border-purple-500/50 transition duration-300">
                                {{ substr($member->name ?? $member->username, 0, 1) }}
                            </div>
                        @endif

                        {{-- Follow Button --}}
                        @if(Auth::id() !== $member->id)
                            <button @click="toggleFollow()" 
                                    :disabled="loading"
                                    :class="isFollowed ? 'bg-purple-600 border-purple-400 text-white' : 'bg-slate-800 hover:bg-purple-600 border-white/10 text-white'"
                                    class="absolute bottom-1 right-1 border w-6 h-6 rounded-full flex items-center justify-center transition shadow-lg z-10 disabled:opacity-50 disabled:cursor-not-allowed">
                                
                                {{-- Loading Spinner --}}
                                <svg x-show="loading" class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                
                                {{-- Plus Icon --}}
                                <span x-show="!isFollowed && !loading" class="text-sm font-bold">+</span>
                                
                                {{-- Checkmark Icon --}}
                                <svg x-show="isFollowed && !loading" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    {{-- Informasi Profil --}}
                    <a href="{{ route('profile', ['user' => $member->username]) }}" class="font-semibold text-slate-200 hover:text-purple-400 transition text-sm truncate w-full px-2">
                        {{ $member->name ?? $member->username }}
                    </a>
                    <p class="text-[11px] text-slate-500 mt-0.5 tracking-wide">
                        {{ number_format($member->books_count) }} books • {{ number_format($member->reviews_count) }} reviews
                    </p>

                    {{-- Mini Grid Cover Buku --}}
                    <div class="grid grid-cols-4 gap-1 mt-4 w-full px-1">
                        @foreach($member->readingLogs as $log)
                            @if($log->book)
                            <a href="{{ route('books.show', $log->book->external_id) }}" 
                               class="aspect-[2/3] bg-slate-900 rounded overflow-hidden border border-white/5 hover:border-purple-500/40 transition shadow-md block"
                               title="{{ $log->book->title }}">
                                @if($log->book->cover_url)
                                    <img src="{{ $log->book->cover_url }}" class="w-full h-full object-cover" alt="{{ $log->book->title }}">
                                @endif
                            </a>
                            @endif
                        @endforeach
                        @for($i = count($member->readingLogs); $i < 4; $i++)
                            <div class="aspect-[2/3] bg-slate-900/40 border border-dashed border-white/5 rounded"></div>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>
        </section>

    </div>
</div>
@endsection