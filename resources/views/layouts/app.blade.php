<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" style="background-color: var(--c-bg-0); color-scheme: dark;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Communico') }}</title>

        @php
            $logoPath = public_path('images/logo_communico.svg');
            $icoPath  = public_path('favicon.ico');

            $vLogo = file_exists($logoPath) ? filemtime($logoPath) : null;
            $vIco  = file_exists($icoPath)  ? filemtime($icoPath)  : null;

            $logoHref = asset('images/logo_communico.svg') . ($vLogo ? '?v=' . $vLogo : '');
            $icoHref  = asset('favicon.ico') . ($vIco ? '?v=' . $vIco : '');

            $isChat = request()->routeIs('chat.index');
        @endphp

        {{-- Favicon / Logo no separador --}}
        <link rel="icon" type="image/svg+xml" href="{{ $logoHref }}">
        <link rel="alternate icon" href="{{ $icoHref }}">
        <link rel="shortcut icon" href="{{ $icoHref }}">
        <link rel="apple-touch-icon" href="{{ $logoHref }}">
        <meta name="theme-color" content="#0B1220">

        {{-- FIX: força o browser a renderizar controlos nativos em modo escuro --}}
        <meta name="color-scheme" content="dark">
        <style>
            :root { color-scheme: dark; }

            /* Alpine: evita flash antes do x-show */
            [x-cloak] { display: none !important; }

            /* Select nativo em dark (principalmente Windows/Chrome) */
            select.comm-select { color-scheme: dark; }

            /* Dropdown (options) — evita ficar branco */
            select.comm-select option {
                background-color: var(--c-bg-0);
                color: rgba(255,255,255,.92);
            }
        </style>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body
        class="h-full font-sans antialiased text-white {{ $isChat ? 'overflow-hidden' : '' }}"
        style="background-color: var(--c-bg-0);"
    >
        {{-- Wrapper global --}}
        <div
            class="{{ $isChat ? 'h-full' : 'min-h-screen' }} comm-bg relative flex flex-col w-full overflow-hidden"
            style="background-color: var(--c-bg-0);"
        >
            {{-- FIX: garante que a navbar fica SEMPRE acima do overlay do chat --}}
            <div class="relative z-50">
                @include('layouts.navigation')
            </div>

            {{-- Header (quando usado em páginas normais) --}}
            @isset($header)
                <header class="pt-8 pb-4">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="comm-card rounded-2xl px-6 py-4">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            {{-- IMPORTANTÍSSIMO: no chat, o main tem de ser um “viewport” sem scroll global --}}
            <main class="flex-1 {{ $isChat ? 'min-h-0 overflow-hidden' : '' }}">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
