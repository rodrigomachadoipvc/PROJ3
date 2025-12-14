@props([
    'user',
    'size' => 'md', // sm | md | lg
])

@php
    $sizes = [
        'sm' => 'h-8 w-8 text-sm',
        'md' => 'h-12 w-12 text-base',
        'lg' => 'h-16 w-16 text-xl',
    ];

    $classes = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatar_url_public)
    <img
        src="{{ $user->avatar_url_public }}"
        alt="{{ $user->nome }}"
        class="{{ $classes }} rounded-full object-cover shadow-sm"
    >
@else
    <div
        class="{{ $classes }} rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm"
        aria-label="Avatar {{ $user->initials }}"
    >
        {{ $user->initials }}
    </div>
@endif
