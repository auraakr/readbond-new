@extends('admin.layouts.adminlayout') {{-- Sesuaikan nama master layout adminmu --}}

@section('content')
<div class="p-6 bg-slate-950 min-h-screen text-white">
    
    {{-- Tombol Kembali --}}
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Manajemen Pengguna
        </a>
    </div>

    {{-- Layout Grid Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- KARTU KIRI: BIODATA PROFIL UTAMA --}}
        <div class="bg-slate-900 border border-white/5 rounded-xl p-6 shadow-xl flex flex-col items-center text-center h-fit">
            <div class="w-24 h-24 rounded-full bg-purple-600/20 text-purple-400 font-bold flex items-center justify-center text-3xl uppercase border border-purple-500/30 shadow-inner mb-4">
                {{ substr($user->name, 0, 2) }}
            </div>
            
            <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
            <p class="text-sm text-slate-400 font-mono mt-0.5">@<span>{{ $user->username }}</span></p>
            
            <div class="mt-4 px-3 py-1 bg-purple-500/10 text-purple-400 text-xs font-semibold rounded-full border border-purple-500/20 uppercase tracking-wider">
                {{ $user->role ?? 'Member' }}
            </div>

            <hr class="w-full border-slate-800 my-6">

            {{-- Detail Info Akun --}}
            <div class="w-full space-y-4 text-left text-sm text-slate-300">
                <div>
                    <span class="block text-xs text-slate-500 font-medium uppercase tracking-wider">Alamat Email</span>
                    <span class="font-medium text-white break-all">{{ $user->email }}</span>
                </div>
                <div>
                    <span class="block text-xs text-slate-500 font-medium uppercase tracking-wider">Tanggal Bergabung</span>
                    <span class="font-medium text-white">{{ $user->created_at->translatedFormat('d F Y (H:i)') }} WIB</span>
                </div>
                <div>
                    <span class="block text-xs text-slate-500 font-medium uppercase tracking-wider">Bio Pengguna</span>
                    <p class="text-slate-400 mt-1 italic leading-relaxed text-xs">
                        {{ $user->bio ?? 'Tidak ada bio tertulis.' }}
                    </p>
                </div>
            </div>

            <hr class="w-full border-slate-800 my-6">

            {{-- Tombol Aksi Moderasi --}}
            <div class="w-full">
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN? Menghapus akun ini akan melenyapkan seluruh riwayat ulasan, diary, dan daftar bacaan mereka secara permanen!')" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-950/20 border border-red-500/30 hover:bg-red-600 hover:text-white text-red-400 font-semibold py-2.5 rounded-lg text-sm transition shadow-lg shadow-red-950/20 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        Hapus Akun Pengguna
                    </button>
                </form>
            </div>
        </div>

        {{-- BLOK KANAN: STATISTIK & REKAP AKTIVITAS --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Grid Mini Widget Aktivitas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 bg-slate-900 border border-white/5 rounded-xl">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Ulasan Ditulis</span>
                    <h3 class="text-2xl font-bold text-white mt-1">{{ $user->reviews_count ?? $user->reviews()->count() }}</h3>
                </div>
                <div class="p-4 bg-slate-900 border border-white/5 rounded-xl">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Koleksi Dibuat</span>
                    <h3 class="text-2xl font-bold text-purple-400 mt-1">{{ $user->collections_count ?? $user->collections()->count() }}</h3>
                </div>
                <div class="p-4 bg-slate-900 border border-white/5 rounded-xl">
                    <span class="text-xs text-slate-500 font-medium uppercase tracking-wider block">Pengikut / Followers</span>
                    <h3 class="text-2xl font-bold text-emerald-400 mt-1">{{ $user->followers_count ?? $user->followers()->count() }}</h3>
                </div>
            </div>

            {{-- Komponen Ulasan Terbaru dari User Ini --}}
            <div class="bg-slate-900 border border-white/5 rounded-xl p-6 shadow-xl">
                <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-purple-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379L12 21l3.12-3.133a49.093 49.093 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v5.018Z" />
                    </svg>
                    Riwayat Ulasan Buku Terbaru
                </h3>

                <div class="space-y-4">
                    {{-- Lakukan pemanggilan relasi reviews jika di-eagerload --}}
                    @forelse($user->reviews()->latest()->take(5)->get() as $review)
                        <div class="p-4 bg-slate-950/50 border border-white/5 rounded-lg flex flex-col sm:flex-row gap-4 items-start">
                            {{-- Sampul Buku mikro (opsional jika skema tabel kamu memiliki cover) --}}
                            <div class="w-10 h-14 bg-slate-800 rounded shrink-0 flex items-center justify-center text-[10px] text-slate-500 font-mono shadow">
                                @if(isset($review->book->cover))
                                    <img src="{{ $review->book->cover }}" class="w-full h-full object-cover rounded">
                                @else
                                    BOOK
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <h4 class="text-sm font-semibold text-white truncate">{{ $review->book->title ?? 'Judul Buku Tidak Diketahui' }}</h4>
                                    <span class="text-[10px] text-slate-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                {{-- Skor Rating Bintang Komponen Mikro --}}
                                <div class="flex items-center gap-1 text-amber-400 mt-0.5 text-xs">
                                    🌟 <span class="font-bold text-slate-300 ml-0.5">{{ $review->rating ?? '0' }}/5</span>
                                </div>
                                <p class="text-slate-400 text-xs mt-2 leading-relaxed line-clamp-3">
                                    "{{ $review->review }}"
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-xs text-slate-500 italic border border-dashed border-slate-800 rounded-lg">
                            User ini belum pernah mempublikasikan ulasan buku.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection