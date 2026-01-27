<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        @endphp

        {{-- Favicon / Logo no separador --}}
        <link rel="icon" type="image/svg+xml" href="{{ $logoHref }}">
        <link rel="alternate icon" href="{{ $icoHref }}">
        <link rel="shortcut icon" href="{{ $icoHref }}">
        <link rel="apple-touch-icon" href="{{ $logoHref }}">
        <meta name="theme-color" content="#0B1220">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="font-sans antialiased text-white">
        {{-- Fundo global (já tens comm-bg/comm-card definidos) --}}
        <div class="min-h-screen comm-bg relative overflow-hidden flex flex-col">
            @include('layouts.navigation')

            {{-- Header (deixa de ser “modo claro”) --}}
            @isset($header)
                <header class="pt-8 pb-4">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="comm-card rounded-2xl px-6 py-4">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
