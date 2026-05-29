@extends('admin.layouts.adminlayout') {{-- Sesuaikan master layout adminmu --}}

@section('content')
<div class="p-6 bg-slate-950 min-h-screen text-white">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Daftar Pengajuan Buku</h1>
            <p class="text-sm text-slate-400 mt-1">Daftar buku yang diajukan oleh komunitas ReadBond.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-slate-900 border border-white/5 rounded-xl overflow-hidden shadow-2xl">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-slate-800 text-slate-300 font-semibold border-b border-slate-700/50">
                <tr>
                    <th class="p-4">User</th>
                    <th class="p-4">Info Buku</th>
                    <th class="p-4">ISBN</th>
                    <th class="p-4">Catatan User</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @foreach($requests as $req)
                <tr class="hover:bg-slate-800/30 transition">
                    <td class="p-4 font-medium">{{ $req->user->name ?? 'User Terhapus' }}</td>
                    <td class="p-4">
                        <div class="font-semibold text-white">{{ $req->title }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">Oleh: {{ $req->author }}</div>
                    </td>
                    <td class="p-4 text-slate-300 font-mono text-xs">{{ $req->isbn ?? '-' }}</td>
                    <td class="p-4 text-slate-400 max-w-xs truncate" title="{{ $req->notes }}">{{ $req->notes ?? '-' }}</td>
                    <td class="p-4">
                        @if($req->status === 'pending')
                            <span class="px-2.5 py-1 bg-amber-500/10 text-amber-400 text-xs font-semibold rounded-full border border-amber-500/20">Pending</span>
                        @elseif($req->status === 'approved')
                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-semibold rounded-full border border-emerald-500/20">Disetujui</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-500/10 text-red-400 text-xs font-semibold rounded-full border border-red-500/20">Ditolak</span>
                        @endif
                    </td>
                    <td class="p-4">
                        @if($req->status === 'pending')
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Setujui: Mengarahkan ke form tambah buku bawaan admin sambil membawa id request --}}
                            <a href="{{ route('admin.books.create', ['request_id' => $req->id]) }}" 
                               class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition">
                                Setujui
                            </a>

                            {{-- Tombol Tolak --}}
                            <form action="{{ route('admin.book-requests.reject', $req->id) }}" method="POST" onsubmit="return confirm('Tolak pengajuan ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-slate-800 hover:bg-red-950/40 hover:text-red-400 text-slate-400 text-xs font-semibold px-3 py-1.5 rounded border border-white/5 transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="text-center text-xs text-slate-500 italic">Selesai</div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $requests->links() }}
    </div>
</div>
@endsection