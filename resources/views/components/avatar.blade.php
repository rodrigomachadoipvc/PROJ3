@props([
    'user',
    'size' => 'md', // sm | md | lg
])

@php
    $sizes = [
        'sm' => ['w' => '2rem',  'h' => '2rem',  'text' => 'text-sm'],
        'md' => ['w' => '3rem',  'h' => '3rem',  'text' => 'text-base'],
        'lg' => ['w' => '4rem',  'h' => '4rem',  'text' => 'text-xl'],
    ];

    $sizeConfig = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatar_url_public)
    <img
        src="{{ $user->avatar_url_public }}"
        alt="{{ $user->nome }}"
        class="rounded-full object-cover shadow-sm"
        style="
            width: {{ $sizeConfig['w'] }};
            height: {{ $sizeConfig['h'] }};
        "
    >
@else
    <div
        class="rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold shadow-sm {{ $sizeConfig['text'] }}"
        style="
            width: {{ $sizeConfig['w'] }};
            height: {{ $sizeConfig['h'] }};
        "
        aria-label="Avatar {{ $user->initials }}"
    >
        {{ $user->initials }}
    </div>
@endif
