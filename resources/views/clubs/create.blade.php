@extends('layouts.main')

@section('content')
<div class="bg-slate-900 min-h-screen pt-24 pb-16 px-6 lg:px-16">
    <div class="max-w-2xl mx-auto">
        
        <div class="mb-8">
            <p class="text-slate-500 text-xs uppercase tracking-widest font-medium mb-1">Readbond Space</p>
            <h1 class="text-3xl font-black text-white tracking-tight">Establish a Book Club</h1>
        </div>

        <form action="{{ route('clubs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                <div class="col-span-1">
                    <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Club Cover</label>
                    <div class="aspect-[3/4] bg-slate-800 border-2 border-dashed border-slate-700 rounded-sm flex flex-col items-center justify-center p-4 text-center relative group hover:border-purple-500 transition">
                        <svg class="w-8 h-8 text-slate-500 group-hover:text-purple-400 mb-2 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs text-slate-400 group-hover:text-purple-300 transition block">Pilih File Cover</span>
                        <input type="file" name="cover_image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    </div>
                </div>

                <div class="col-span-2 space-y-5">
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Club Name</label>
                        <input type="text" name="name" required placeholder="Contoh: edge-of-your-seat suspense"
                               class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-2 focus:ring-purple-500 outline-none placeholder-slate-600 transition">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Short Description</label>
                        <textarea name="description" rows="3" required placeholder="What is this club about?"
                                  class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-2 focus:ring-purple-500 outline-none placeholder-slate-600 transition"></textarea>
                    </div>
                </div>
            </div>

            <hr class="border-slate-800 my-4">

            <div class="space-y-5">
                <div>
                    <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Club Rules</label>
                    <textarea name="rules" rows="3" placeholder="Please be courteous to other book club members. No spam..."
                              class="w-full bg-slate-800 border border-slate-700 text-white text-sm rounded-sm py-2.5 px-4 focus:ring-2 focus:ring-purple-500 outline-none placeholder-slate-600 transition"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Category / Tag</label>
                        <select name="category" required class="w-full bg-slate-800 border border-slate-700 text-slate-300 text-sm rounded-sm py-2.5 px-4 focus:ring-2 focus:ring-purple-500 outline-none transition cursor-pointer">
                            <option value="Just for fun">Just for fun</option>
                            <option value="Fan Club">Fan Club</option>
                            <option value="Academic">Academic</option>
                            <option value="Romance">Romance</option>
                            <option value="Sci-Fi">Sci-Fi</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 text-xs font-semibold uppercase tracking-wider mb-2">Club Visibility</label>
                        <select name="visibility" required class="w-full bg-slate-800 border border-slate-700 text-slate-300 text-sm rounded-sm py-2.5 px-4 focus:ring-2 focus:ring-purple-500 outline-none transition cursor-pointer">
                            <option value="public">Public (Everyone can join)</option>
                            <option value="private">Private (Invite only)</option>
                        </select>
                    </div>
                </div>

                <div class="bg-slate-850 border border-slate-800/80 p-4 rounded-sm space-y-3">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Non-Moderator Permissions</h3>
                    
                    <label class="flex items-center gap-3 cursor-pointer group text-slate-300 text-sm">
                        <input type="checkbox" name="allow_member_add_book" value="1" checked
                               class="rounded border-slate-700 bg-slate-800 text-purple-600 focus:ring-purple-500 focus:ring-offset-slate-900 w-4 h-4">
                        <span>Non-moderators can add club's book</span>
                    </label>

                    <label class="flex items-center gap-3 cursor-pointer group text-slate-300 text-sm">
                        <input type="checkbox" name="allow_member_add_discussion" value="1"
                               class="rounded border-slate-700 bg-slate-800 text-purple-600 focus:ring-purple-500 focus:ring-offset-slate-900 w-4 h-4">
                        <span>Non-moderators can add club's topic discussion</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white font-bold text-sm px-6 py-2.5 rounded-sm shadow-md shadow-purple-950/50 transition duration-200">
                    + Save Book Club
                </button>
            </div>
        </form>

    </div>
</div>
@endsection