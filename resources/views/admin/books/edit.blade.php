@extends('admin.layouts.adminlayout')

@section('title', 'Edit Book: ' . $book->title)

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Edit Book</h1>
            <p class="text-slate-400 text-sm">Update information for "{{ $book->title }}"</p>
        </div>
        <a href="{{ route('admin.books.index') }}" class="text-slate-400 hover:text-white transition text-sm flex items-center gap-2">
            ← Back to List
        </a>
    </div>

    {{-- Route mengarah ke 'update' dengan method PUT --}}
    <form action="{{ route('admin.books.update', $book->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-slate-900 border border-white/5 p-8 rounded-2xl space-y-6">
            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Book Title</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" required 
                    class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500/50 outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Author Name --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Author Name</label>
                    <input type="text" name="author_name" value="{{ old('author_name', $book->author_name) }}" required 
                        class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500/50 outline-none">
                </div>
                {{-- Year --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Release Year</label>
                    <input type="number" name="year" value="{{ old('year', $book->year) }}" required 
                        class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500/50 outline-none">
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Description</label>
                <textarea name="desc" rows="4" 
                    class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500/50 outline-none">{{ old('desc', $book->desc) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Page Count --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Page Count</label>
                    <input type="number" name="pageCount" value="{{ old('pageCount', $book->pageCount) }}" 
                        class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500/50 outline-none">
                </div>
                {{-- Subject --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Subject (comma separated)</label>
                    <input type="text" name="subject" value="{{ old('subject', is_array($book->subject) ? implode(', ', $book->subject) : $book->subject) }}" 
                        class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:ring-2 focus:ring-purple-500/50 outline-none">
                </div>
                {{-- Cover Upload --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Update Cover</label>
                    <input type="file" name="cover" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                </div>
            </div>

            {{-- Preview Cover Saat Ini --}}
            @if($book->cover)
            <div class="pt-4 border-t border-white/5">
                <p class="text-xs text-slate-500 mb-2">Current Cover:</p>
                <img src="{{ asset('storage/' . $book->cover) }}" class="h-32 rounded-lg border border-white/10 shadow-lg">
            </div>
            @endif
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.books.index') }}" 
                class="px-6 py-2.5 rounded-lg border border-white/10 text-white hover:bg-slate-800 transition text-sm font-semibold">
                Cancel
            </a>
            <button type="submit" 
                class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-bold shadow-lg shadow-purple-500/20 transition">
                Update Book Data
            </button>
        </div>
    </form>
</div>
@endsection