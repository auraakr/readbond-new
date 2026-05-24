@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16 text-slate-300">
    <div class="max-w-5xl mx-auto">

        {{-- ─── BREADCRUMB / NAVIGASI BAR ─── --}}
        <div class="flex justify-between items-center border-b border-slate-800 pb-4 mb-8">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider">
                <a href="{{ route('clubs.show', $club->slug) }}" class="text-slate-500 hover:text-slate-300 transition">
                    {{ $club->name }}
                </a>
                <span class="text-slate-700">/</span>
                <span class="text-purple-400">Settings</span>
            </div>
            
            {{-- Dropdown Switcher Menu Sesuai Mockup --}}
            <div class="relative">
                <select onchange="location = this.value;" class="bg-slate-800 text-slate-300 border border-slate-700 rounded-sm py-1.5 pl-3 pr-8 text-xs font-semibold outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer appearance-none">
                    <option value="{{ route('clubs.show', $club->slug) }}">Club's Home</option>
                    <option value="#" selected>Club's Settings (Admin)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Flash Message Success --}}
        @if(session('success'))
            <div class="bg-purple-950/40 border border-purple-500/30 text-purple-300 text-xs rounded-sm p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif

        {{-- ─── FORM UTAMA: UPDATE SETTINGS ─── --}}
        <form action="{{ route('clubs.update', $club->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                {{-- Sisi Kiri: Preview & Ganti Cover --}}
                <div class="w-full lg:w-1/3 shrink-0">
                    <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Club Cover</p>
                    <div class="aspect-[3/4] bg-slate-800 border border-slate-700 rounded-sm overflow-hidden relative flex flex-col items-center justify-center group hover:border-purple-500 transition-all duration-300">
                        
                        {{-- Wireframe Cross Lines Background --}}
                        <svg class="absolute inset-0 w-full h-full text-slate-700/20 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" stroke="currentColor">
                            <line x1="0" y1="0" x2="100" y2="100" stroke-width="0.5"/>
                            <line x1="100" y1="0" x2="0" y2="100" stroke-width="0.5"/>
                        </svg>

                        @if($club->cover_image)
                            <img src="{{ asset('storage/' . $club->cover_image) }}" class="absolute inset-0 w-full h-full object-cover z-10 opacity-70 group-hover:opacity-40 transition">
                        @endif

                        <div class="z-20 flex flex-col items-center p-4 text-center">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-purple-400 mb-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                            <span class="text-[11px] font-medium text-slate-400 group-hover:text-purple-300 transition">Ganti Cover Baru</span>
                        </div>
                        
                        <input type="file" name="cover_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-30">
                    </div>
                </div>

                {{-- Sisi Kanan: Nama & Deskripsi --}}
                <div class="flex-1 w-full space-y-6">
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Club Name</label>
                        <input type="text" name="name" value="{{ old('name', $club->name) }}" required
                               class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-1 focus:ring-purple-500 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Description</label>
                        <textarea name="description" rows="4" required
                                  class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-1 focus:ring-purple-500 outline-none transition">{{ old('description', $club->description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Rules</label>
                        <textarea name="rules" rows="3" placeholder="No spam, please be courteous..."
                                class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-1 focus:ring-purple-500 outline-none transition">{{ old('rules', $club->rules) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800/60 my-2"></div>

            {{-- Aturan Internal & Kategori --}}
            <div class="space-y-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Category</label>
                        <select name="category" required 
                                class="w-full bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2.5 text-sm outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer transition">
                            <option value="Just for fun" {{ $club->category == 'Just for fun' ? 'selected' : '' }}>Just for fun</option>
                            <option value="Fan Club" {{ $club->category == 'Fan Club' ? 'selected' : '' }}>Fan Club</option>
                            <option value="Romance" {{ $club->category == 'Romance' ? 'selected' : '' }}>Romance</option>
                            <option value="Thriller" {{ $club->category == 'Thriller' ? 'selected' : '' }}>Thriller</option>
                            <option value="Fantasy" {{ $club->category == 'Fantasy' ? 'selected' : '' }}>Fantasy</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Change book club visibility</label>
                        <select name="visibility" required 
                                class="w-full bg-slate-800 text-slate-300 border border-slate-700 rounded-sm px-4 py-2.5 text-sm outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer transition">
                            <option value="public" {{ $club->visibility == 'public' ? 'selected' : '' }}>Public (This book club is currently public)</option>
                            <option value="private" {{ $club->visibility == 'private' ? 'selected' : '' }}>Private (Invite only / Hidden space)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ─── NON-MODERATOR PERMISSIONS (Sesuai Kotak Pilihan di Mockup) ─── --}}
            <div class="pt-2">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-4">Non moderator settings</p>
                <div class="space-y-3.5">
                    <label class="flex items-center gap-3 cursor-pointer group text-slate-300 text-xs select-none">
                        <input type="checkbox" name="allow_member_add_book" value="1" {{ $club->allow_member_add_book ? 'checked' : '' }}
                               class="rounded-sm border-slate-700 bg-slate-800 text-purple-600 focus:ring-purple-500 focus:ring-offset-slate-900 w-4 h-4 transition">
                        <span class="group-hover:text-slate-200 transition">Non-moderators can add club's book</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group text-slate-300 text-xs select-none">
                        <input type="checkbox" name="allow_member_add_discussion" value="1" {{ $club->allow_member_add_discussion ? 'checked' : '' }}
                               class="rounded-sm border-slate-700 bg-slate-800 text-purple-600 focus:ring-purple-500 focus:ring-offset-slate-900 w-4 h-4 transition">
                        <span class="group-hover:text-slate-200 transition">Non-moderators can add club's topic discussion</span>
                    </label>
                </div>
            </div>

            {{-- Tombol Submit Form Pengaturan Utama --}}
            <div class="flex justify-end gap-4 border-t border-slate-800/60 pt-4">
                <a href="{{ route('clubs.show', $club->slug) }}" class="text-slate-400 hover:text-white text-xs font-medium self-center transition">Cancel</a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs px-5 py-2.5 rounded-sm transition">
                    Save Changes
                </button>
            </div>
        </form>

        {{-- ─── SECTION TAMBAHAN: MANAGEMENT MODERATOR (Aksi Multi-Moderator) ─── --}}
        <div class="mt-12 border-t border-slate-800 pt-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Club Governance</h3>
            <p class="text-slate-500 text-xs mb-6">Sebagai pemilik atau rekan moderator, Anda bisa menambahkan member aktif sebagai bagian dari tim moderator.</p>

            {{-- Form Tambah Moderator --}}
            <form action="{{ route('clubs.add-moderator', $club->id) }}" method="POST" class="flex gap-3 max-w-md mb-6">
                @csrf
                <select name="user_id" required class="flex-1 bg-slate-800 text-slate-300 text-xs border border-slate-700 rounded-sm px-3 py-2 outline-none focus:ring-1 focus:ring-purple-500 cursor-pointer">
                    <option value="" disabled selected>Pilih member untuk diangkat...</option>
                    {{-- Loop data member yang bertindak sebagai member biasa --}}
                    @foreach($club->members as $member)
                        @if($member->pivot->role !== 'moderator')
                            <option value="{{ $member->id }}">{{ $member->username }} ({{ $member->email }})</option>
                        @endif
                    @endforeach
                </select>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-purple-400 border border-purple-500/30 text-xs font-bold px-4 py-2 rounded-sm transition">
                    + Add Moderator
                </button>
            </form>

            {{-- List Moderator Saat Ini --}}
            <div class="bg-slate-850 border border-slate-800 rounded-sm p-4">
                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Current Moderators</p>
                <div class="divide-y divide-slate-800/60">
                    @foreach($club->members as $member)
                        @if($member->pivot->role === 'moderator')
                            <div class="py-2.5 flex justify-between items-center text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-purple-900/50 flex items-center justify-center text-[10px] text-purple-300 font-bold uppercase">
                                        {{ substr($member->username, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white">{{ $member->username }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $member->email }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold bg-purple-950 text-purple-400 border border-purple-900/40 px-2 py-0.5 rounded-sm uppercase tracking-wide">
                                    Moderator
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection