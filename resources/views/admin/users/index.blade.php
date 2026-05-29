@extends('admin.layouts.adminlayout') {{-- Sesuaikan dengan nama master layout admin milikmu --}}

@section('content')
<div class="p-6 bg-slate-950 min-h-screen text-white">
    
    {{-- HEADER & FORM PENCARIAN --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Manajemen Pengguna</h1>
            <p class="text-sm text-slate-400 mt-1">Kelola hak akses, pantau aktivitas, dan moderasi akun pengguna ReadBond.</p>
        </div>
        
        {{-- Form Pencarian Komunitas --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                </svg>
            </span>
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}"
                placeholder="Cari nama, username, atau email..." 
                class="w-full bg-slate-900 border border-white/10 rounded-lg pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-slate-500 transition"
            >
        </form>
    </div>

    {{-- KARTU STATISTIK RINGKAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="p-4 bg-slate-900 border border-white/5 rounded-xl flex items-center gap-4 shadow-lg">
            <div class="p-3 bg-purple-500/10 rounded-lg text-purple-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Total Anggota</p>
                <h3 class="text-xl font-bold mt-0.5">{{ $stats['total_users'] ?? 0 }} <span class="text-xs font-normal text-slate-500">Orang</span></h3>
            </div>
        </div>

        <div class="p-4 bg-slate-900 border border-white/5 rounded-xl flex items-center gap-4 shadow-lg">
            <div class="p-3 bg-emerald-500/10 rounded-lg text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Bergabung Bulan Ini</p>
                <h3 class="text-xl font-bold mt-0.5">{{ $stats['new_this_month'] ?? 0 }} <span class="text-xs font-normal text-slate-500">Baru</span></h3>
            </div>
        </div>
    </div>

    {{-- NOTIFIKASI SUKSES / ERROR --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg text-sm">
            ✨ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-lg text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- TABEL DATA USER --}}
    <div class="bg-slate-900 border border-white/5 rounded-xl overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-slate-800 text-slate-300 font-semibold border-b border-slate-700/50">
                <tr>
                    <th class="p-4">Nama Lengkap</th>
                    <th class="p-4">Username</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">Tanggal Bergabung</th>
                    <th class="p-4 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($users as $user)
                <tr class="hover:bg-slate-800/30 transition">
                    <td class="p-4 font-medium text-white flex items-center gap-3">
                        {{-- Avatar Inisial Bulat --}}
                        <div class="w-8 h-8 rounded-full bg-purple-600/20 text-purple-400 font-bold flex items-center justify-center text-xs uppercase shadow-inner">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        {{ $user->name }}
                    </td>
                    <td class="p-4 text-slate-300 font-mono text-xs">@<span>{{ $user->username }}</span></td>
                    <td class="p-4 text-slate-400">{{ $user->email }}</td>
                    <td class="p-4 text-slate-400 text-xs">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                    <td class="p-4">
                        <div class="flex items-center justify-center gap-3">
                            {{-- Mengarah ke halaman DETAIL/SHOW khusus admin --}}
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs font-semibold text-purple-400 hover:text-purple-300 transition">
                                Kelola Profil
                            </a>
                            
                            <span class="text-slate-800">|</span>

                            {{-- Tombol Hapus Cepat --}}
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen akun user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-red-400 transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-sm text-slate-500 italic">
                        Tidak ada data pengguna yang terdaftar atau cocok dengan kata kunci.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION LINKS --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection