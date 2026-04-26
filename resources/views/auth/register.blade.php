@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<style>
    .book-grid { transform: rotate(-6deg) scale(1.2); }
    .panel-fade { background: linear-gradient(to bottom, #0f172a 10%, transparent 40%, transparent 60%, #0f172a 90%); }
    .input-field { transition: border-color .15s, box-shadow .15s; }
    .input-field:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.1); outline: none; }
    .input-field::placeholder { color: #3a3a4e; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,.06); }
</style>

<div class="flex min-h-screen bg-[#0a0a0f]">

    {{-- Left decorative panel --}}
    <div class="hidden lg:flex flex-col w-[45%] flex-shrink-0 relative bg-slate-900 overflow-hidden border-r border-white/5">

        <div class="absolute inset-0 grid grid-cols-4 gap-2 p-2 opacity-20 pointer-events-none book-grid">
            @foreach(range(1, 4) as $col)
            <div class="flex flex-col gap-2">
                @foreach(range(1, 5) as $row)
                    @php $idx = ($col - 1) * 5 + $row; @endphp
                    @if($idx <= 12)
                        <img src="{{ asset('images/hero/book-cover-' . $idx . '.jpg') }}" class="w-full aspect-[3/4] object-cover rounded-sm" alt="">
                    @else
                        <div class="w-full aspect-[3/4] bg-slate-800 rounded-sm"></div>
                    @endif
                @endforeach
            </div>
            @endforeach
        </div>

        <div class="absolute inset-0 panel-fade pointer-events-none"></div>

        <div class="relative z-10 mt-auto p-12">
            <p class="font-serif text-3xl italic text-white/90 leading-snug mb-4">
                "There is no friend as loyal as a book."
            </p>
            <p class="text-xs font-medium tracking-widest uppercase text-slate-500">— Ernest Hemingway</p>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex flex-1 items-center justify-center px-6 py-12 overflow-y-auto">
        <div class="w-full max-w-sm py-3">

            {{-- Brand --}}
            <div class="mb-12">
                <p class="font-brand text-white text-2xl uppercase font-black">Read<span class="text-purple-500">bond</span></p>
                <p class="text-xs text-slate-500 font-light mt-1">Join now with fill this form to sign up. <a href="{{ url('/') }}" class="hover:text-white underline">Or back to Home</a></p>
            </div>

            <p class="text-xl font-semibold text-white tracking-tight mb-7">Create Account</p>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-sm">
                    <ul class="flex flex-col gap-1">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-400">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                @csrf

                {{-- Name + Username --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="name" class="block text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            placeholder="Your name"
                            class="input-field w-full px-4 py-3 bg-[#111118] border border-white/[.08] rounded-sm text-white text-sm">
                        @error('name')
                            <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required
                            placeholder="@handle"
                            class="input-field w-full px-4 py-3 bg-[#111118] border border-white/[.08] rounded-sm text-white text-sm">
                        @error('username')
                            <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="you@example.com"
                        class="input-field w-full px-4 py-3 bg-[#111118] border border-white/[.08] rounded-sm text-white text-sm">
                    @error('email')
                        <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password + Confirm --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-3 bg-[#111118] border border-white/[.08] rounded-sm text-white text-sm">
                        @error('password')
                            <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[11px] font-semibold uppercase tracking-widest text-slate-500 mb-2">Confirm</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-3 bg-[#111118] border border-white/[.08] rounded-sm text-white text-sm">
                    </div>
                </div>

                {{-- Terms --}}
                <div class="flex items-start gap-2.5">
                    <input type="checkbox" id="agree_terms" name="agree_terms" required
                        class="w-4 h-4 mt-0.5 flex-shrink-0 accent-purple-500 cursor-pointer rounded-sm">
                    <label for="agree_terms" class="text-xs text-slate-500 leading-relaxed cursor-pointer">
                        I agree to the <a href="#" class="text-purple-500 hover:underline">Terms of Service</a> and <a href="#" class="text-purple-500 hover:underline">Privacy Policy</a>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold uppercase tracking-wider rounded-sm transition hover:-translate-y-px mt-1">
                    Create Account
                </button>
            </form>

            {{-- Divider --}}
            <div class="divider flex items-center gap-3 my-6">
                <span class="text-[11px] text-slate-600 whitespace-nowrap">Already have an account?</span>
            </div>

            {{-- Login link --}}
            <a href="{{ route('login') }}"
                class="block w-full text-center py-3 border border-purple-500/30 hover:border-purple-500 hover:bg-purple-500/10 text-purple-500 text-xs font-semibold uppercase tracking-wider rounded-sm transition">
                Sign In
            </a>

        </div>
    </div>

</div>

@endsection