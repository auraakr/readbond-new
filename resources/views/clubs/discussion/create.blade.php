@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16 text-slate-300">
    <div class="max-w-3xl mx-auto">

        {{-- NAVIGATION HEADER --}}
        <div class="mb-8 border-b border-slate-800 pb-4">
            <a href="{{ route('clubs.show', $club->slug) }}" class="text-slate-400 hover:text-white transition flex items-center gap-2 text-xs font-semibold uppercase tracking-wider">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to {{ $club->name }}
            </a>
            <h1 class="text-2xl font-black text-white tracking-tight mt-4">Start a New Discussion</h1>
            <p class="text-slate-500 text-xs mt-1">Lemparkan topik, pertanyaan, atau teori konspirasi buku baru untuk didiskusikan bersama anggota lain.</p>
        </div>

        {{-- FORM CREATE DISCUSSION --}}
        <form action="{{ route('clubs.discussion.store', $club->slug) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Field Pilih Buku (Optional Mention) --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider">Mention a Book</label>
                    <span class="text-[10px] text-slate-500 font-medium uppercase tracking-wider">Optional</span>
                </div>
                <select name="book_id" 
                        class="w-full bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2.5 text-sm outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer transition">
                    <option value="" selected>-- Pilih buku yang ingin dibahas (Tidak ada) --</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }} {{ $book->author ? ' - ' . $book->author : '' }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Input Judul Thread --}}
            <div>
                <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Discussion Title</label>
                <input type="text" name="title" required placeholder="Apakah ending Chapter 5 masuk akal menurut kalian?"
                       class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-1 focus:ring-purple-500 outline-none placeholder-slate-600 transition">
            </div>

            {{-- Input Post Pertama --}}
            <div>
                <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">First Post Content</label>
                <textarea name="content" rows="6" required placeholder="Tulis argumen atau pembuka obrolanmu di sini..."
                          class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-1 focus:ring-purple-500 outline-none placeholder-slate-600 transition"></textarea>
            </div>

            {{-- BUTTON ACTIONS --}}
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800/60">
                <a href="{{ route('clubs.show', $club->slug) }}" class="text-slate-400 hover:text-slate-200 text-xs font-medium transition">
                    Cancel
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-5 py-2.5 rounded-sm transition shadow-md shadow-purple-950/40">
                    Publish Topic
                </button>
            </div>
        </form>

    </div>
</div>
@endsection