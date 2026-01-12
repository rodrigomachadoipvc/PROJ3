@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-semibold text-white transition focus:outline-none'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-semibold text-white/70 hover:text-white hover:border-white/20 transition focus:outline-none';

$style = ($active ?? false)
    ? 'border-color: rgba(45,212,191,.70);'
    : '';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $style]) }}>
    {{ $slot }}
</a>
