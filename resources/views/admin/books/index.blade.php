@extends('admin.layouts.adminlayout')

@section('title', 'All Books')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Book Management</h1>
            <p class="text-slate-400 text-sm">Manage your library collection and availability.</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-lg shadow-purple-500/20">
            + Add New Book
        </a>
    </div>

    <div class="bg-slate-900 border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Book Info</th>
                        <th class="px-6 py-4 font-medium">Category</th>
                        <th class="px-6 py-4 font-medium">Rating</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($books as $book)
                    <tr class="hover:bg-white/[0.02] transition text-slate-300">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-9 bg-slate-800 rounded flex-shrink-0 overflow-hidden">
                                    @if($book->cover)
                                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full text-[10px]">No Cover</div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-white">{{ $book->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $book->author_name }} ({{ $book->year }})</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{-- Karena subject adalah array (cast), kita tampilkan dengan implode --}}
                            {{ is_array($book->subject) ? implode(', ', $book->subject) : $book->subject }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-1 text-yellow-500">
                                <span>★</span>
                                <span>{{ $book->averageRating ?? '0' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('admin.books.edit', $book->id) }}" class="text-purple-400 hover:text-purple-300 text-sm">Edit</a>
                            <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-300 text-sm" onclick="return confirm('Delete this book?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection