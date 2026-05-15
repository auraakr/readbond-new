<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&family=Shadows+Into+Light&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-950 text-slate-100">
   <x-navbar /> 
   <x-reading-log-modal />
    <main class="pt-20">
        <div class="min-h-screen bg-[#0a0a0f] text-white" style="font-family:'DM Sans',sans-serif;">
            <!-- header -->
            <div class="bg-slate-900 border-b border-white/5">
                <div class="max-w-6xl mx-auto px-6 py-10">
                    <div class="flex items-start gap-6">

                        {{-- Avatar --}}
                        <div class="relative flex-shrink-0">
                            <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=8b5cf6&color=fff&size=96' }}"
                                class="w-24 h-24 rounded-full object-cover ring-2 ring-white/10" alt="{{ $user->name }}">
                        </div>

                        {{-- Name + actions --}}
                        <div class="flex-1 min-w-0 pt-1">
                            <div class="flex items-center gap-3 flex-wrap mb-1">
                                <h1 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
                                @auth
                                    @if(auth()->id() === $user->id)
                                        <a href="#"
                                            class="px-3 py-1 text-[11px] font-semibold uppercase tracking-wider border border-white/15 text-slate-400 hover:border-white/30 hover:text-white rounded-sm transition">
                                            Edit Profile
                                        </a>
                                    @else
                                        <button class="px-4 py-1.5 text-[11px] font-semibold uppercase tracking-wider bg-purple-600 hover:bg-purple-700 text-white rounded-sm transition">
                                            Follow
                                        </button>
                                    @endif
                                @endauth
                            </div>
                            @if($user->bio)
                                <p class="text-sm text-slate-400 font-light max-w-lg mt-2">{{ $user->bio }}</p>
                            @endif
                        </div>

                        {{-- Stats --}}
                        <div class="hidden sm:flex items-center gap-8 pt-1 flex-shrink-0">
                            @foreach([
                                ['label' => 'Books', 'value' => $user->books_count ?? 0],
                                ['label' => 'This Year', 'value' => $user->books_this_year ?? 0],
                                ['label' => 'Following', 'value' => $user->following_count ?? 0],
                                ['label' => 'Followers', 'value' => $user->followers_count ?? 0],
                            ] as $stat)
                            <div class="text-center">
                                <p class="text-xl font-semibold">{{ $stat['value'] }}</p>
                                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mt-0.5">{{ $stat['label'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Mobile stats --}}
                    <div class="flex sm:hidden items-center gap-6 mt-6 pt-6 border-t border-white/5">
                        @foreach([
                            ['label' => 'Books', 'value' => $user->books_count ?? 0],
                            ['label' => 'This Year', 'value' => $user->books_this_year ?? 0],
                            ['label' => 'Following', 'value' => $user->following_count ?? 0],
                            ['label' => 'Followers', 'value' => $user->followers_count ?? 0],
                        ] as $stat)
                        <div class="text-center">
                            <p class="text-lg font-semibold">{{ $stat['value'] }}</p>
                            <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-500 mt-0.5">{{ $stat['label'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Nav tabs --}}
                <div class="max-w-6xl mx-auto px-6">
                    <nav class="flex gap-1 overflow-x-auto scrollbar-hide">
                        @foreach(['Profile' => 'profile', 'Activity' => 'profile.activity', 'Books' => 'profile.books', 'Diary' => '#', 'Reviews' => 'profile.reviews', 'Readlist' => '#', 'Collections' => '#', 'Network' => '#'] as $tab => $routeName)
                            @php
                                $isActive = ($routeName !== '#') && request()->routeIs($routeName);
                            @endphp
                            <a href="{{ $routeName !== '#' ? route($routeName, $user->username) : '#' }}"
                                class="px-4 py-3 text-xs font-semibold uppercase tracking-widest whitespace-nowrap transition border-b-2
                                {{ $isActive
                                    ? 'text-white border-purple-500'
                                    : 'text-slate-500 border-transparent hover:text-slate-300 hover:border-white/20' }}">
                                {{ $tab }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
        @yield('content')
        </div>
    </main>
</body>
</html>