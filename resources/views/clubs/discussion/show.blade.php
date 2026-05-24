@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16 text-slate-300">
    <div class="max-w-3xl mx-auto">

        {{-- BREADCRUMB HEADER --}}
        <div class="mb-8 border-b border-slate-800 pb-4">
            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="{{ route('clubs.show', $club->slug) }}" class="hover:text-slate-300 transition">{{ $club->name }}</a>
                <span>/</span>
                <span class="text-slate-600">Discussions</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight mt-2 leading-tight">{{ $discussion->title }}</h1>
            <p class="text-slate-500 text-xs mt-1">
                Started by <span class="text-purple-400 font-medium">{{ $discussion->user->username }}</span> 
                • {{ $discussion->created_at->diffForHumans() }}
            </p>
        </div>

        {{-- ─── TIMELINE POSTS (DAFTAR BALASAN) ─── --}}
        <div class="space-y-4 mb-10">
            @foreach($discussion->posts as $index => $post)
                <div class="bg-slate-850 border {{ $index === 0 ? 'border-purple-900/40 bg-purple-950/5' : 'border-slate-800' }} rounded-sm p-4 relative">
                    
                    {{-- Metadata Penulis Post --}}
                    <div class="flex justify-between items-center mb-3 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-[10px] text-slate-300 font-bold uppercase">
                                {{ substr($post->user->username, 0, 2) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-200">{{ $post->user->username }}</span>
                                @if($post->user_id === $club->moderator_id)
                                    <span class="text-[9px] bg-purple-950 text-purple-400 border border-purple-900/50 px-1.5 py-0.2 rounded-sm ml-1 uppercase font-black">Staff</span>
                                @endif
                            </div>
                        </div>
                        <span class="text-slate-500 text-[11px]">{{ $post->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Isi Pesan Post --}}
                    <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $post->content }}
                    </div>

                    {{-- Label Penanda Thread Starter --}}
                    @if($index === 0)
                        <div class="absolute top-0 right-4 transform -translate-y-1/2 bg-purple-600 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-sm tracking-wider">
                            Topic Creator
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- ─── BOX FORM: CREATE POST (BALASAN BARU) ─── --}}
        @if($isMember || Auth::id() === $club->moderator_id)
            <div class="border-t border-slate-800 pt-6">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Post a Reply</p>
                
                <form action="{{ route('clubs.discussion.posts.store', [$club->slug, $discussion->id]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <textarea name="content" rows="4" required placeholder="Tulis tanggapan atau opini balasanmu mengenai topik ini..."
                                  class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-1 focus:ring-purple-500 border-none outline-none placeholder-slate-600 transition"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-5 py-2.5 rounded-sm transition shadow-md shadow-purple-950/40">
                            Submit Reply
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-slate-850 border border-slate-800 text-center p-4 rounded-sm">
                <p class="text-xs text-slate-500 italic">Kamu harus bergabung menjadi anggota klub ini terlebih dahulu untuk ikut serta dalam ruang diskusi.</p>
            </div>
        @endif

    </div>
</div>
@endsection