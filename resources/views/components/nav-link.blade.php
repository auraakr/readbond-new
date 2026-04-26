@props(['active'])

@php
$classes = ($active ?? false)
            ? 'px-1 pt-1 text-md uppercase font-bold transition duration-150'
            : 'text-slate-400 hover:text-slate-200 px-1 pt-1 text-md uppercase font-medium transition duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>