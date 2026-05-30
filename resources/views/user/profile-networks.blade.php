@extends('layouts.profile')

@section('title', $user->username . "'s Network")

@section('content')
<div class="min-h-screen bg-[#0a0a0f] text-white" style="font-family:'DM Sans',sans-serif;">
    <div class="max-w-4xl mx-auto px-6 py-8">
        
        {{-- ── TAB NAVIGATION ── --}}
        <div class="flex border-b border-slate-800 mb-8" x-data="{ currentTab: 'followers' }">
            <button onclick="switchTab('followers')" id="tab-followers"
                    class="tab-btn px-6 py-3 text-sm font-semibold border-b-2 border-purple-500 text-purple-400 transition-all duration-200">
                Followers <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-md bg-slate-800 text-slate-400 group-hover:text-white">{{ $user->followers_count }}</span>
            </button>
            <button onclick="switchTab('following')" id="tab-following"
                    class="tab-btn px-6 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-300 transition-all duration-200">
                Following <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-md bg-slate-800 text-slate-400">{{ $user->following_count }}</span>
            </button>
        </div>

        {{-- ── FOLLOWERS LIST PANEL ── --}}
        <div id="panel-followers" class="tab-panel block">
            @if($followers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($followers as $follower)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-900/50 border border-slate-800 hover:border-slate-700 transition group">
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Avatar --}}
                                <a href="{{ route('profile.networks', $follower->username) }}" class="shrink-0">
                                    @if($follower->avatar)
                                        <img src="{{ asset('storage/' . $follower->avatar) }}" 
                                             class="w-12 h-12 rounded-full object-cover border border-slate-700 group-hover:border-purple-500 transition">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold text-lg border border-slate-700 group-hover:border-purple-500 transition">
                                            {{ strtoupper(substr($follower->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </a>
                                
                                {{-- User Info --}}
                                <div class="min-w-0">
                                    <a href="{{ route('profile', $follower->username) }}" class="block font-semibold text-white hover:text-purple-400 transition truncate">
                                        {{ $follower->name }}
                                    </a>
                                    <p class="text-xs text-slate-500 truncate">@<span>{{ $follower->username }}</span></p>
                                </div>
                            </div>

                            {{-- Optional Action Button --}}
                            <a href="{{ route('profile', $follower->username) }}" 
                               class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition shrink-0">
                                View Profile
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 border border-dashed border-slate-800 rounded-2xl">
                    <p class="text-slate-500 font-medium">Belum ada pengikut.</p>
                </div>
            @endif
        </div>

        {{-- ── FOLLOWING LIST PANEL ── --}}
        <div id="panel-following" class="tab-panel hidden">
            @if($following->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($following as $followed)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-slate-900/50 border border-slate-800 hover:border-slate-700 transition group">
                            <div class="flex items-center gap-3 min-w-0">
                                {{-- Avatar --}}
                                <a href="{{ route('profile', $followed->username) }}" class="shrink-0">
                                    @if($followed->avatar)
                                        <img src="{{ asset('storage/' . $followed->avatar) }}" 
                                             class="w-12 h-12 rounded-full object-cover border border-slate-700 group-hover:border-purple-500 transition">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold text-lg border border-slate-700 group-hover:border-purple-500 transition">
                                            {{ strtoupper(substr($followed->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </a>
                                
                                {{-- User Info --}}
                                <div class="min-w-0">
                                    <a href="{{ route('profile', $followed->username) }}" class="block font-semibold text-white hover:text-purple-400 transition truncate">
                                        {{ $followed->name }}
                                    </a>
                                    <p class="text-xs text-slate-500 truncate">@<span>{{ $followed->username }}</span></p>
                                </div>
                            </div>

                            {{-- Optional Action Button --}}
                            <a href="{{ route('profile', $followed->username) }}" 
                               class="px-3 py-1.5 text-xs font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition shrink-0">
                                View Profile
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 border border-dashed border-slate-800 rounded-2xl">
                    <p class="text-slate-500 font-medium">Belum mengikuti siapapun.</p>
                </div>
            @endif
        </div>

    </div>
</div>

{{-- ── TAB CONTROLLER SCRIPT ── --}}
<script>
    function switchTab(tabName) {
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
            panel.classList.remove('block');
        });
        
        // Show selected panel
        document.getElementById('panel-' + tabName).classList.remove('hidden');
        document.getElementById('panel-' + tabName).classList.add('block');
        
        // Reset all tab button styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-purple-500', 'text-purple-400');
            btn.classList.add('border-transparent', 'text-slate-500');
        });
        
        // Active styling for clicked tab
        const activeTab = document.getElementById('tab-' + tabName);
        activeTab.classList.remove('border-transparent', 'text-slate-500');
        activeTab.classList.add('border-purple-500', 'text-purple-400');
    }
</script>
@endsection