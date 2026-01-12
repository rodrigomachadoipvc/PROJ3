@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-semibold text-white bg-white/5 transition focus:outline-none'
    : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-semibold text-white/70 hover:text-white hover:bg-white/5 hover:border-white/20 transition focus:outline-none';

$style = ($active ?? false)
    ? 'border-color: rgba(45,212,191,.70);'
    : '';
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $style]) }}>
    {{ $slot }}
</a>
