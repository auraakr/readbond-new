@extends('admin.layouts.adminlayout')

@section('title', 'Manage Reports')

@section('content')
{{-- State Alpine.js sekarang dibuat kosong dan bersih --}}
<div class="p-6" x-data="{ 
    openModal: false, 
    selectedReport: {
        id: '', reporter: '', bookTitle: '', reviewer: '', 
        reviewText: '', reason: '', notes: '', status: '', 
        dismissUrl: '', deleteUrl: ''
    }
}">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Manage Reports</h1>
            <p class="text-slate-400 text-sm">Tinjau laporan masuk secara ringkas melalui sistem tinjauan modal.</p>
        </div>
    </div>

    {{-- Alert Success Notification --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Container --}}
    <div class="bg-slate-900 border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-medium">Pelapor</th>
                        <th class="px-6 py-4 font-medium">Alasan Laporan</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($reports as $report)
                    <tr class="hover:bg-white/[0.02] transition text-slate-300">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white">{{ $report->user->name }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ $report->created_at->diffForHumans() }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <span class="inline-block bg-red-950/30 text-red-400 text-[11px] font-semibold px-2 py-0.5 rounded border border-red-500/10">
                                {{ $report->reason }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm">
                            @if($report->status === 'pending')
                                <span class="text-amber-400 font-medium text-xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    Pending
                                </span>
                            @else
                                <span class="text-slate-500 text-xs flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                    Dismissed
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <button 
                                type="button"
                                data-id="{{ $report->id }}"
                                data-reporter="{{ $report->user->name }}"
                                data-book="{{ $report->bookReview->book->title ?? 'Unknown Book' }}"
                                data-reviewer="{{ $report->bookReview->user->name ?? 'Deleted User' }}"
                                data-review="{{ $report->bookReview->review ?? 'Tidak ada teks ulasan.' }}"
                                data-reason="{{ $report->reason }}"
                                data-notes="{{ $report->notes ?? '-' }}"
                                data-status="{{ $report->status }}"
                                data-dismiss="{{ route('admin.reports.dismiss', $report->id) }}"
                                data-delete="{{ route('admin.reports.destroyReview', $report->id) }}"
                                @click="
                                    let btn = $el;
                                    selectedReport = {
                                        id: btn.getAttribute('data-id'),
                                        reporter: btn.getAttribute('data-reporter'),
                                        bookTitle: btn.getAttribute('data-book'),
                                        reviewer: btn.getAttribute('data-reviewer'),
                                        reviewText: btn.getAttribute('data-review'),
                                        reason: btn.getAttribute('data-reason'),
                                        notes: btn.getAttribute('data-notes'),
                                        status: btn.getAttribute('data-status'),
                                        dismissUrl: btn.getAttribute('data-dismiss'),
                                        deleteUrl: btn.getAttribute('data-delete')
                                    };
                                    openModal = true;
                                "
                                class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-purple-400 hover:text-purple-300 text-xs font-semibold rounded transition border border-white/5">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-500 text-sm">
                            Tidak ada laporan review yang masuk saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Block --}}
        @if(method_exists($reports, 'hasPages') && $reports->hasPages())
            <div class="p-4 bg-slate-950 border-t border-white/5">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    {{-- ── GLOBAL MODAL DETAIL REPORT VIA ALPINE.JS ── --}}
    <div x-show="openModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
         
        <div class="bg-slate-900 border border-white/5 w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl" @click.away="openModal = false">
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-slate-800/40 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    🚨 Detail Laporan Pelanggaran
                </h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-white transition text-lg">&times;</button>
            </div>

            {{-- Modal Content Body --}}
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4 bg-slate-950/40 p-3 rounded-xl border border-white/5">
                    <div>
                        <span class="text-xs text-slate-500 block">Dilaporkan Oleh:</span>
                        <strong class="text-white text-xs" x-text="selectedReport.reporter"></strong>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block">Kategori Alasan:</span>
                        <span class="inline-block bg-red-950/40 text-red-400 text-[10px] font-bold px-2 py-0.5 rounded border border-red-500/20 mt-0.5" x-text="selectedReport.reason"></span>
                    </div>
                </div>

                <div>
                    <span class="text-xs text-slate-500 block mb-1">Catatan Tambahan Pelapor:</span>
                    <p class="text-slate-300 text-xs bg-slate-950/20 p-2.5 rounded border border-white/5 whitespace-pre-line" x-text="selectedReport.notes"></p>
                </div>

                <div class="border-t border-white/5 pt-4">
                    <span class="text-xs text-slate-500 block mb-1">Buku Terkait:</span>
                    <span class="inline-block bg-purple-950/40 text-purple-400 text-xs font-semibold px-2 py-0.5 rounded border border-purple-500/10 mb-2" x-text="selectedReport.bookTitle"></span>
                    
                    <span class="text-xs text-slate-500 block mb-1">Isi Review (ditulis oleh <span x-text="selectedReport.reviewer" class="text-slate-400"></span>):</span>
                    <p class="text-slate-200 text-xs italic bg-slate-950/60 p-3 rounded-xl border border-white/5 leading-relaxed">
                        "<span x-text="selectedReport.reviewText"></span>"
                    </p>
                </div>
            </div>

            {{-- Modal Footer / Action Buttons --}}
            <div class="px-6 py-4 bg-slate-800/20 border-t border-white/5 flex items-center justify-end gap-3">
                <button @click="openModal = false" class="px-4 py-2 text-slate-400 hover:text-white text-xs font-semibold uppercase tracking-wider transition">
                    Tutup
                </button>

                <template x-if="selectedReport.status === 'pending'">
                    <div class="flex gap-3">
                        <form :action="selectedReport.dismissUrl" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg transition border border-white/5">
                                Abaikan Laporan
                            </button>
                        </form>

                        <form :action="selectedReport.deleteUrl" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-950/20 hover:bg-red-600 text-red-400 hover:text-white text-xs font-semibold rounded-lg transition border border-red-500/10 hover:border-transparent">
                                Hapus Review
                            </button>
                        </form>
                    </div>
                </template>
                
                <template x-if="selectedReport.status !== 'pending'">
                    <span class="text-xs text-slate-600 font-semibold uppercase tracking-wider">Laporan Selesai Diproses</span>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection