@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'block w-full pl-3 pr-4 py-2 border-l-4 border-purple-500 text-left text-base font-semibold text-purple-400 bg-purple-900/20 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800 hover:border-slate-700 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>