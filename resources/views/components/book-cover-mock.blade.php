@props(['color' => 'bg-slate-700', 'src' => null])

<div class="{{ $color }} aspect-[3/4] rounded-lg shadow-2xl border border-white/10 overflow-hidden transform hover:scale-105 transition duration-300">
    @if($src)
        <img src="{{ $src }}" class="w-full h-full object-cover">
    @else
        <div class="w-full h-full flex items-end p-4 bg-gradient-to-t from-black/60 to-transparent">
            <div class="h-2 w-12 bg-white/30 rounded"></div>
        </div>
    @endif
</div>