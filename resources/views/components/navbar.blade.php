<nav x-data="{ open: false, profileOpen: false }" 
     @keydown.window.escape="open = false; profileOpen = false"
     class="fixed w-full z-50 bg-slate-900/80 backdrop-blur-md border-b border-white/5 shadow-2xl">
    
    <div class="max-w-7xl mx-auto px-6 lg:px-32">
        <div class="flex justify-between h-20 items-center">
            
            <div class="shrink-0 flex items-center">
                <a href="/" class="font-brand text-2xl font-black tracking-tighter text-white hover:opacity-80 transition">
                    READ<span class="text-purple-500">BOND</span>
                </a>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-6">
                <x-nav-link href="/" :active="request()->is('/')">Home</x-nav-link>
                <x-nav-link href="/books" :active="request()->is('books*')">Books</x-nav-link>
                <x-nav-link href="/collections" :active="request()->is('collection*')">Collection</x-nav-link>
                <x-nav-link href="/friends" :active="request()->is('friends*')">Friends</x-nav-link>
                <x-nav-link href="/#" :active="request()->is('#')">Clubs</x-nav-link>
                
                @auth
                    @if(auth()->user()->role === 'admin')
                        <x-nav-link href="/admin/dashboard" :active="request()->is('admin*')">Dashboard</x-nav-link>
                    @endif
                @endauth
                
                <div class="h-6 w-[1px] bg-slate-700 mx-2"></div>

                @auth
                    <div class="flex items-center gap-3">
                        <button onclick="openReadingLogModal()" class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold py-2 px-4 rounded-sm transition-all shadow-lg shadow-purple-500/20">
                            <x-heroicon-o-plus class="h-4 w-4" />
                            <span>READING LOG</span>
                        </button>

                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="p-2 rounded-full text-slate-300 hover:text-white transition bg-slate-800 border border-white/5">
                                <x-heroicon-o-user class="h-5 w-5" />
                            </button>

                            <div x-show="profileOpen" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-3 w-48 bg-slate-800 border border-white/10 rounded-xl shadow-2xl py-2 z-50">
                                
                                <div class="px-4 py-2 border-b border-white/5">
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Username</p>
                                    <p class="text-sm text-white truncate">{{ Auth::user()->username }}</p>
                                </div>

                                <a href="{{ route('profile', ['user' => auth()->user()->username]) }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition">My Profile</a>
                                <a href="/settings" class="block px-4 py-2 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition">Settings</a>
                                
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-red-400/10 transition">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="/login" class="p-2 text-slate-300 hover:text-purple-400 transition" title="Sign In">
                        <x-heroicon-o-lock-closed class="h-6 w-6" />
                    </a>
                @endauth
            </div>

            <div class="flex items-center gap-2 sm:hidden">
                @auth
                    <a href="#" onclick="openReadingLogModal(); return false;" class="p-2 text-purple-400">
                        <x-heroicon-o-plus-circle class="h-7 w-7" />
                    </a>
                @else
                    <a href="/login" class="p-2 text-slate-300 hover:text-purple-400 transition">
                        <x-heroicon-o-lock-closed class="h-6 w-6" />
                    </a>
                @endauth

                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-all">
                    <x-heroicon-o-bars-3 x-show="!open" class="h-7 w-7" />
                    <x-heroicon-o-x-mark x-show="open" x-cloak class="h-7 w-7" />
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak @click.away="open = false"
         class="absolute top-full left-0 w-full bg-slate-900 border-b border-white/10 px-6 py-8 space-y-4 sm:hidden shadow-2xl z-40">
        
        <div class="flex flex-col gap-2">
            <x-responsive-nav-link href="/" :active="request()->is('/')">Home</x-responsive-nav-link>
            <x-responsive-nav-link href="/books" :active="request()->is('books*')">Books</x-responsive-nav-link>
            <x-responsive-nav-link href="/collections" :active="request()->is('collection*')">Collection</x-responsive-nav-link>
            <x-responsive-nav-link href="/#" :active="request()->is('#')">Clubs</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link href="{{ route('profile', ['user' => auth()->user()->username]) }}" :active="request()->is('profile*')">My Profile</x-responsive-nav-link>
                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link href="/admin/dashboard" :active="request()->is('admin*')">Dashboard</x-responsive-nav-link>
                @endif
            @endauth
        </div>

        @auth
            <div class="pt-6 border-t border-white/10 text-center">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-red-400 font-bold text-sm bg-red-400/10 px-4 py-3 rounded-xl">Logout</button>
                </form>
            </div>
        @endauth
    </div>
</nav>