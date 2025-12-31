@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-secondary text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-secondary'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-600 hover:text-gray-800 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-secondary';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
