@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-purple-600/20 to-purple-600/10 border border-purple-600/50 rounded-2xl p-8 mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">
                Welcome back, <span class="text-purple-400">{{ Auth::user()->name }}</span>! 📚
            </h1>
            <p class="text-slate-300">Happy to see you. Let's explore some amazing books today.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-4 gap-6 mb-12">
            <!-- Books Read -->
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Books Read</p>
                        <p class="text-3xl font-bold text-white mt-2">12</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-s-book-open class="w-6 h-6 text-purple-400" />
                    </div>
                </div>
            </div>

            <!-- Pages Read -->
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Pages Read</p>
                        <p class="text-3xl font-bold text-white mt-2">3.2K</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-400" />
                    </div>
                </div>
            </div>

            <!-- Current Reading -->
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Currently Reading</p>
                        <p class="text-3xl font-bold text-white mt-2">2</p>
                    </div>
                    <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-o-bolt class="w-6 h-6 text-green-400" />
                    </div>
                </div>
            </div>

            <!-- Reading Streak -->
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Reading Streak</p>
                        <p class="text-3xl font-bold text-white mt-2">7 days</p>
                    </div>
                    <div class="w-12 h-12 bg-red-600/20 rounded-lg flex items-center justify-center">
                        <x-heroicon-s-fire class="w-6 h-6 text-red-400" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-2xl p-8 mb-12">
            <h2 class="text-2xl font-bold text-white mb-6">Quick Actions</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white py-3 px-6 rounded-lg font-semibold text-center transition">
                    ➕ Add Book
                </a>
                <a href="#" class="bg-slate-700 hover:bg-slate-600 text-white py-3 px-6 rounded-lg font-semibold text-center transition">
                    📝 Write Review
                </a>
                <a href="#" class="bg-slate-700 hover:bg-slate-600 text-white py-3 px-6 rounded-lg font-semibold text-center transition">
                    👥 Join Club
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-white mb-6">Recent Activity</h2>
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                    <div class="w-10 h-10 rounded-full bg-purple-600/20 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-s-book-open class="w-5 h-5 text-purple-400" />
                    </div>
                    <div>
                        <p class="text-white font-semibold">Finished "The Great Gatsby"</p>
                        <p class="text-slate-400 text-sm">2 days ago</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                    <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-s-star class="w-5 h-5 text-blue-400" />
                    </div>
                    <div>
                        <p class="text-white font-semibold">Rated "1984" with 5 stars</p>
                        <p class="text-slate-400 text-sm">5 days ago</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                    <div class="w-10 h-10 rounded-full bg-green-600/20 flex items-center justify-center flex-shrink-0">
                        <x-heroicon-o-user-group class="w-5 h-5 text-green-400" />
                    </div>
                    <div>
                        <p class="text-white font-semibold">Joined "Fantasy Lovers" book club</p>
                        <p class="text-slate-400 text-sm">1 week ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
