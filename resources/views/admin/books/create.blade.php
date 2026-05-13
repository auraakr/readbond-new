@extends('admin.layouts.adminlayout')

@section('title', 'Add New Book')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white tracking-tight">Add New Book</h1>
        <p class="text-slate-400 text-sm">Fill in the details to add a new book to the database.</p>
    </div>

    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="bg-slate-900 border border-white/5 p-8 rounded-2xl space-y-6">
            {{-- Judul Buku --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Book Title</label>
                <input type="text" name="title" required class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white" placeholder="The Great Gatsby">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Author Name --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Author Name</label>
                    <input type="text" name="author_name" required class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white">
                </div>
                {{-- Year --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Release Year</label>
                    <input type="number" name="year" required class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white">
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Description (Desc)</label>
                <textarea name="desc" rows="4" class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Page Count --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Page Count</label>
                    <input type="number" name="pageCount" class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white">
                </div>
                {{-- Subject (Kategori) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Subject (Pisahkan dengan koma)</label>
                    <input type="text" name="subject" class="w-full bg-slate-800 border border-white/10 rounded-lg px-4 py-2.5 text-white" placeholder="Fiction, Drama">
                </div>
                {{-- Cover --}}
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Cover Image</label>
                    <input type="file" name="cover" class="w-full text-sm text-slate-400 file:bg-purple-600 file:text-white file:rounded-lg file:border-0 file:px-4 file:py-2">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 rounded-lg border border-white/10 text-white hover:bg-slate-800 transition text-sm font-semibold">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-bold shadow-lg shadow-purple-500/20 transition">Save Book</button>
        </div>
    </form>
</div>
@endsection