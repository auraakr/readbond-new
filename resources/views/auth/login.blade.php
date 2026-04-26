@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="min-h-screen bg-slate-950 flex items-stretch">

    <!-- Left decorative panel -->
    <div class="hidden lg:flex lg:w-5/12 flex-col relative bg-slate-900 border-r border-white/5 overflow-hidden">
        <div class="rb-login-left__grid">
            @foreach(range(1, 4) as $col)
            <div>
                @foreach(range(1, 5) as $row)
                    @php $idx = ($col - 1) * 5 + $row; @endphp
                    @if($idx <= 12)
                        <img src="{{ asset('images/hero/book-cover-' . $idx . '.jpg') }}" alt="">
                    @else
                        <div class="rb-mock"></div>
                    @endif
                @endforeach
            </div>
            @endforeach
        </div>
        <div class="rb-login-left__fade"></div>

        <div class="relative z-10 mt-auto p-12">
            <p class="text-2xl italic text-white leading-relaxed mb-4">"A reader lives a thousand lives before he dies."</p>
            <p class="text-xs font-medium tracking-widest uppercase text-slate-500">— George R.R. Martin</p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">

            <!-- Brand -->
            <div class="mb-12">
                <p class="font-brand text-white text-2xl uppercase font-black">Read<span class="text-purple-500">bond</span></p>
                <p class="text-xs text-slate-500 font-light mt-1">Welcome back, please fill this form to sign in. <a href="{{ url('/') }}" class="hover:text-white underline">Or back to Home</a></p>
            </div>

            <p class="text-xl font-semibold text-white mb-8">Sign In</p>

            <!-- Error message -->
            @if ($errors->any())
                <div class="mb-6 p-3 bg-red-500/10 border border-red-500/20 rounded">
                    <p class="text-xs text-red-400">{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                @csrf

                <!-- Email field -->
                <div>
                    <label for="email" class="block text-xs font-semibold tracking-wider uppercase text-slate-400 mb-2">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="you@example.com"
                        class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded text-white text-sm placeholder-slate-700 focus:outline-none focus:border-purple-500 transition-colors"
                    >
                    @error('email')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div>
                    <label for="password" class="block text-xs font-semibold tracking-wider uppercase text-slate-400 mb-2">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded text-white text-sm placeholder-slate-700 focus:outline-none focus:border-purple-500 transition-colors"
                    >
                    @error('password')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember checkbox -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded accent-purple-500 cursor-pointer">
                    <label for="remember" class="text-xs text-slate-400 cursor-pointer">Remember me</label>
                </div>

                <button type="submit" class="w-full px-4 py-3 mt-1 bg-purple-600 text-white text-xs font-semibold tracking-wider uppercase rounded hover:bg-purple-700 transition-colors hover:-translate-y-0.5">
                    Sign In
                </button>
            </form>

            <!-- Divider -->
            <div class="rb-divider my-6">
                <span class="text-xs text-slate-500 whitespace-nowrap">New to Readbond?</span>
            </div>

            <!-- Register link -->
            <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 border border-purple-500/30 text-purple-500 text-xs font-semibold tracking-wider uppercase rounded hover:bg-purple-500/10 hover:border-purple-500 transition-colors">
                Create an Account
            </a>

            <!-- Forgot password link -->
            <div class="flex justify-center mt-3">
                <a href="#" class="text-xs text-slate-500 hover:text-purple-500 transition-colors">Forgot your password?</a>
            </div>

        </div>
    </div>

</div>

@endsection