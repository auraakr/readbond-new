@extends('admin.layouts.adminlayout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Dashboard Overview</h1>
            <p class="text-slate-400 text-sm">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg border border-white/10 text-sm transition">
                Print Report
            </button>
            <a href="{{ route('admin.books.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-lg shadow-purple-500/20">
                + New Book
            </a>
        </div>
    </div>

    {{-- Grid Statistik Utama (Dinamis & Ber-Ikon Khusus) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl shadow-sm hover:border-purple-500/20 transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500 group-hover:bg-purple-500/20 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-400">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>

                </div>
                <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-slate-400 text-sm font-medium">Total Users</h3>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalUsers) }}</p>
        </div>

        <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl shadow-sm hover:border-purple-500/20 transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500 group-hover:bg-purple-500/20 transition">
                    <x-heroicon-o-book-open class="w-6 h-6 text-purple-400" />
                </div>
                <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-full">+5%</span>
            </div>
            <h3 class="text-slate-400 text-sm font-medium">Total Books</h3>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($totalBooks) }}</p>
        </div>

        <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl shadow-sm hover:border-purple-500/20 transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500 group-hover:bg-purple-500/20 transition">
                    <x-heroicon-o-chat-bubble-left-ellipsis class="w-6 h-6 text-purple-400" />
                </div>
                <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-full">+18%</span>
            </div>
            <h3 class="text-slate-400 text-sm font-medium">Community Reviews</h3>
            <p class="text-2xl font-bold text-white mt-1">{{ number_format($activeReviewsCount) }}</p>
        </div>

        <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl shadow-sm hover:border-purple-500/20 transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500 group-hover:bg-purple-500/20 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                {{-- Berubah warna indikator jika ada urgent report masuk --}}
                <span class="text-xs font-bold {{ $totalReports > 0 ? 'text-red-400 bg-red-400/10 animate-pulse' : 'text-slate-500 bg-slate-800' }} px-2 py-1 rounded-full">
                    {{ $totalReports > 0 ? 'Urgent' : 'Safe' }}
                </span>
            </div>
            <h3 class="text-slate-400 text-sm font-medium">Active Reports</h3>
            <p class="text-2xl font-bold {{ $totalReports > 0 ? 'text-red-400' : 'text-white' }} mt-1">{{ $totalReports }}</p>
        </div>

    </div>

    {{-- Section Bawah: Recent Activities & Sidebar Widget --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 bg-slate-900 border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Recent Activities</h2>
                <a href="{{ route('admin.reports.index') }}" class="text-purple-400 text-sm hover:underline">Manage Reports</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-medium">User</th>
                            <th class="px-6 py-4 font-medium">Action</th>
                            <th class="px-6 py-4 font-medium">Book</th>
                            <th class="px-6 py-4 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($recentActivities as $activity)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-xs font-bold text-white uppercase">
                                        {{ Str::limit($activity->user->name, 2, '') }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-200">{{ $activity->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-400 max-w-[200px] truncate" title="{{ $activity->review }}">
                                Added Review: "{{ $activity->review }}"
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold bg-purple-500/10 text-purple-400 rounded uppercase border border-purple-500/10">
                                    {{ Str::limit($activity->book->title ?? 'Unknown Book', 20) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $activity->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">
                                Belum ada aktivitas ulasan baru dari komunitas ReadBond.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Info Shortcut Modul Management --}}
            <div class="bg-gradient-to-br from-purple-600 to-indigo-700 p-6 rounded-2xl text-white shadow-xl">
                <h3 class="font-bold text-lg mb-2">Review Moderation</h3>
                <p class="text-purple-100 text-sm mb-4">Gunakan panel kontrol laporan untuk memantau ulasan negatif atau aktivitas spam di platform ReadBond.</p>
                <a href="{{ route('admin.reports.index') }}" class="block w-full text-center bg-white text-purple-600 py-2.5 rounded-lg font-bold text-sm hover:bg-slate-100 transition shadow-md shadow-black/10">
                    Buka Riwayat Laporan
                </a>
            </div>

            {{-- Live Indicator (Data Di-render Berdasarkan Waktu Request Terkini) --}}
            <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl">
                <h3 class="text-white font-semibold mb-4">Active Users Now</h3>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-bold text-white tracking-tight" x-data="{ count: {{ $activeUsersNow }} }" x-text="count"></span>
                    <span class="text-emerald-400 text-sm mb-1.5 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Live
                    </span>
                </div>
                <div class="mt-4 h-2 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 rounded-full transition-all duration-500" style="width: 45%;"></div>
                </div>
                <p class="text-slate-500 text-xs mt-3">Platform traffic stability is under normal conditions (45%).</p>
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    /* 1. SEMBUNYIKAN SIDEBAR, NAVBAR, DAN TOMBOL AKSI */
    aside,
    nav,
    .sidebar,
    #sidebar,
    .no-print,
    button,
    a,
    .bg-gradient-to-br { /* Menyembunyikan widget update */
        display: none !important;
    }

    @page {
        margin: 1.5cm;
    }
}
</style>

@endsection