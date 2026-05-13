@extends('admin.layouts.adminlayout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Dashboard Overview</h1>
            <p class="text-slate-400 text-sm">Welcome back, {{ auth()->user()->name }}. Here's what's happening today.</p>
        </div>
        <div class="flex gap-3">
            <button class="bg-slate-800 hover:bg-slate-700 text-white px-4 py-2 rounded-lg border border-white/10 text-sm transition">
                Download Report
            </button>
            <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-lg shadow-purple-500/20">
                + New Entry
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $stats = [
                ['label' => 'Total Users', 'value' => '1,284', 'icon' => 'user', 'trend' => '+12%'],
                ['label' => 'Total Books', 'value' => '452', 'icon' => 'book-open', 'trend' => '+5%'],
                ['label' => 'Active Logs', 'value' => '89', 'icon' => 'chart-bar', 'trend' => '+18%'],
                ['label' => 'Revenue', 'value' => '$12.4k', 'icon' => 'currency-dollar', 'trend' => '+2%'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-500/10 rounded-lg text-purple-500">
                    {{-- Ganti dengan icon heroicon sesuai kebutuhan --}}
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-full">
                    {{ $stat['trend'] }}
                </span>
            </div>
            <h3 class="text-slate-400 text-sm font-medium">{{ $stat['label'] }}</h3>
            <p class="text-2xl font-bold text-white mt-1">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-slate-900 border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Recent Activities</h2>
                <a href="#" class="text-purple-400 text-sm hover:underline">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-800/50 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-medium">User</th>
                            <th class="px-6 py-4 font-medium">Action</th>
                            <th class="px-6 py-4 font-medium">Status</th>
                            <th class="px-6 py-4 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @for($i = 0; $i < 5; $i++)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-purple-500 to-blue-500 flex items-center justify-center text-xs font-bold text-white">
                                        JD
                                    </div>
                                    <span class="text-sm font-medium text-slate-200">John Doe</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-400">Review added: "The Great Gatsby"</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold bg-blue-500/10 text-blue-500 rounded uppercase">New</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">2 mins ago</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-purple-600 to-indigo-700 p-6 rounded-2xl text-white shadow-xl">
                <h3 class="font-bold text-lg mb-2">System Update</h3>
                <p class="text-purple-100 text-sm mb-4">A new version of ReadBond Admin is available with improved analytics.</p>
                <button class="w-full bg-white text-purple-600 py-2 rounded-lg font-bold text-sm hover:bg-slate-100 transition">
                    Update Now
                </button>
            </div>

            <div class="bg-slate-900 border border-white/5 p-6 rounded-2xl">
                <h3 class="text-white font-semibold mb-4">Active Users Now</h3>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-bold text-white">42</span>
                    <span class="text-emerald-400 text-sm mb-1 font-medium">● Live</span>
                </div>
                <div class="mt-4 h-2 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-purple-500 w-2/3"></div>
                </div>
                <p class="text-slate-500 text-xs mt-3">60% of capacity reached</p>
            </div>
        </div>
    </div>
</div>
@endsection