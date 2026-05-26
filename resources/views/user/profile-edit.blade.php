@extends('layouts.profile')

@section('title', 'Edit Profile — ' . $user->name)

@section('content')
<div class="min-h-screen bg-[#0a0a0f] text-white font-sans">
    <div class="max-w-3xl mx-auto px-6 py-10">
        
        {{-- Judul Halaman --}}
        <div class="mb-8 pb-3 border-b border-slate-800">
            <h2 class="text-xl font-bold tracking-tight text-white">Profile Settings</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola informasi publik akun ReadBond kamu.</p>
        </div>

        {{-- Flash Session Message --}}
        @if(session('status') === 'profile-updated')
            <div class="mb-6 p-3 bg-emerald-950/40 border border-emerald-900/60 text-emerald-400 text-xs rounded-sm flex items-center gap-2">
                <span>✨</span> Profil kamu berhasil diperbarui!
            </div>
        @endif

        {{-- Form Utama --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('patch')

            {{-- Row 1: Upload Avatar --}}
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-sm p-5 flex flex-col sm:flex-row items-center gap-6">
                <div class="relative shrink-0">
                    <img id="avatar-preview" 
                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=8b5cf6&color=fff&size=96' }}"
                        class="w-20 h-20 rounded-full object-cover ring-2 ring-purple-500/30 bg-slate-800" 
                        alt="Avatar preview">
                </div>
                <div class="flex-1 text-center sm:text-left space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Profile Picture</label>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden">
                    <button type="button" onclick="document.getElementById('avatar-input').click()" 
                            class="px-3 py-1.5 text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-sm border border-slate-700 transition cursor-pointer">
                        Choose New Image
                    </button>
                    <p class="text-[10px] text-slate-500">Mendukung file JPG, PNG. Maksimal 2MB.</p>
                    @error('avatar')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Row 2: Form Fields --}}
            <div class="bg-slate-900/40 border border-slate-800/80 rounded-sm p-6 space-y-5">
                
                {{-- Input Name --}}
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Display Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full bg-slate-950 text-slate-200 text-sm border border-slate-800 rounded-sm px-3 py-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                    @error('name')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Input Username --}}
                <div>
                    <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Username</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3 text-slate-600 text-sm select-none">@</span>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                               class="w-full bg-slate-950 text-slate-200 text-sm border border-slate-800 rounded-sm pl-7 pr-3 py-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                    </div>
                    @error('username')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('profile', $user->username) }}" class="text-slate-400 hover:text-white text-xs font-medium transition px-1">
                    Cancel & Back
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-5 py-2.5 rounded-sm transition shadow-md shadow-purple-950/40 cursor-pointer">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fitur Instant Preview Image saat file avatar dipilih
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatar-preview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

</script>
@endsection