@extends('layouts.main')

@section('title', 'Ajukan Tambah Buku - ReadBond')

@section('content')
<div class="min-h-screen bg-slate-950 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 bg-slate-900 border border-white/5 p-8 rounded-2xl shadow-2xl">
        
        {{-- Header Form --}}
        <div class="text-center">
            <div class="mx-auto h-12 w-12 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-400 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Ajukan Tambah Buku</h2>
            <p class="mt-2 text-sm text-slate-400">
                Buku yang kamu cari tidak terdaftar? Isi formulir di bawah ini agar Admin bisa menambahkannya ke sistem.
            </p>
        </div>

        {{-- Form Mulai --}}
        <form action="{{ route('books.request.store') }}" method="POST" class="mt-8 space-y-6" autocomplete="off">
            @csrf

            <div class="space-y-4 text-sm">
                <div>
                    <label for="title" class="block font-medium text-slate-300 mb-1.5">Judul Buku <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title" 
                        value="{{ old('title') }}"
                        placeholder="Contoh: Bumi Manusia"
                        class="w-full bg-slate-800 border @error('title') border-red-500 focus:ring-red-500/20 focus:border-red-500 @else border-slate-700 focus:ring-purple-500/20 focus:border-purple-500 @enderror text-white rounded-lg py-2.5 px-4 outline-none transition placeholder-slate-500"
                        required
                    >
                    @error('title')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author" class="block font-medium text-slate-300 mb-1.5">Penulis / Author <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="author" 
                        id="author" 
                        value="{{ old('author') }}"
                        placeholder="Contoh: Pramoedya Ananta Toer"
                        class="w-full bg-slate-800 border @error('author') border-red-500 focus:ring-red-500/20 focus:border-red-500 @else border-slate-700 focus:ring-purple-500/20 focus:border-purple-500 @enderror text-white rounded-lg py-2.5 px-4 outline-none transition placeholder-slate-500"
                        required
                    >
                    @error('author')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="isbn" class="block font-medium text-slate-300 mb-1.5">ISBN <span class="text-xs text-slate-500">(Opsional)</span></label>
                    <input 
                        type="text" 
                        name="isbn" 
                        id="isbn" 
                        value="{{ old('isbn') }}"
                        placeholder="Contoh: 9789799731234"
                        class="w-full bg-slate-800 border @error('isbn') border-red-500 focus:ring-red-500/20 focus:border-red-500 @else border-slate-700 focus:ring-purple-500/20 focus:border-purple-500 @enderror text-white rounded-lg py-2.5 px-4 outline-none transition placeholder-slate-500"
                    >
                    @error('isbn')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block font-medium text-slate-300 mb-1.5">Catatan Tambahan <span class="text-xs text-slate-500">(Opsional)</span></label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        rows="3"
                        placeholder="Sebutkan penerbit, link referensi, atau sinopsis singkat jika ada..."
                        class="w-full bg-slate-800 border @error('notes') border-red-500 focus:ring-red-500/20 focus:border-red-500 @else border-slate-700 focus:ring-purple-500/20 focus:border-purple-500 @enderror text-white rounded-lg py-2.5 px-4 outline-none transition placeholder-slate-500 resize-none leading-relaxed"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-400 text-xs mt-1.5 flex items-center gap-1">⚠️ {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-4 pt-2">
                <a href="{{ url()->previous() }}" class="w-1/3 text-center bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-semibold py-2.5 rounded-lg border border-white/5 transition">
                    Kembali
                </a>

                <button 
                    type="submit" 
                    class="w-2/3 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2.5 rounded-lg transition shadow-lg shadow-purple-500/20 flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                    Kirim Pengajuan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection