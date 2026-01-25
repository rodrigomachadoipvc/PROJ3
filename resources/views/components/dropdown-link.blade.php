{{-- resources/views/components/dropdown-link.blade.php --}}
@props(['active' => false])

@php
    $classes = 'block w-full px-4 py-2 text-left text-sm font-semibold leading-5
                text-white/85 hover:text-white hover:bg-white/10
                focus:outline-none focus:bg-white/10
                transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
