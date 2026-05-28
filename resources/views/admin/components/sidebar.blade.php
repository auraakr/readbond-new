<aside class="w-64 bg-slate-900 border-r border-white/10 overflow-y-auto transition-all duration-300" :class="!sidebarOpen && '-ml-64'">
    <div class="p-6">
        <!-- Logo -->
        <div class="flex items-center gap-2 mb-8">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">R</span>
            </div>
            <div>
                <p class="text-white font-bold">Read<span class="text-purple-500">bond</span></p>
                <p class="text-xs text-slate-500">Admin</p>
            </div>
        </div>

        <!-- Menu Items -->
        <nav class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 4l4 2m-8-2l4-2"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Books Management -->
            <div x-data="{ open: {{ request()->routeIs('admin.books.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z"></path>
                        </svg>
                        <span class="font-medium">Books</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7-7m0 0L5 14m7-7v12"></path>
                    </svg>
                </button>
                <div x-show="open" class="mt-2 ml-2 space-y-1 pl-2 border-l border-slate-700">
                    <!-- Link All Books -->
                    <a href="{{ route('admin.books.index') }}" 
                    class="{{ request()->routeIs('admin.books.*') ? 'bg-purple-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-3 px-4 py-3 rounded-lg transition">
                        <span>Manage Books</span>
                    </a>

                    <!-- Link Add Book (Opsional jika ingin shortcut langsung) -->
                    <a href="{{ route('admin.books.create') }}" 
                    class="{{ request()->routeIs('admin.books.create') ? 'bg-purple-600 text-white' : 'text-slate-400 hover:bg-slate-800' }} flex items-center gap-3 px-4 py-3 rounded-lg transition">
                        <span>Add New Book</span>
                    </a>
                </div>
            </div>

            <!-- Users Management -->
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 4H9m6 16H9m6-7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="font-medium">Users</span>
            </a>

            <!-- Reports -->
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.reports.index') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} transition-colors">
                <x-heroicon-o-flag class="w-5 h-5" />
                <span class="font-medium">Reports</span>
            </a>

            <!-- Settings -->
            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }} transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="font-medium">Settings</span>
            </a>
        </nav>
    </div>

    <!-- Divider -->
    <div class="mx-4 my-4 border-t border-slate-700"></div>

    <!-- Footer Menu -->
    <div class="p-6 border-t border-slate-700">
        <div class="space-y-2">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="font-medium text-sm">Back to Site</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium text-sm">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
