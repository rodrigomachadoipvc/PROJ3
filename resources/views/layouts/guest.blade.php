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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased text-white comm-bg">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">

                {{-- Logo --}}
                <div class="mb-6 flex justify-center">
                    <a href="/" class="inline-flex items-center">
                        <img
                            src="{{ asset('images/logo_communico.svg') }}"
                            alt="Communico"
                            class="h-12 w-auto"
                        />
                    </a>
                </div>

                {{-- Card --}}
                <div
                    class="comm-card rounded-2xl overflow-hidden"
                    style="
                        backdrop-filter: blur(10px);
                        -webkit-backdrop-filter: blur(10px);
                        box-shadow: 0 30px 80px rgba(0,0,0,.45);
                    "
                >
                    <div class="px-6 py-6 sm:px-7 sm:py-7 text-white/90">
                        {{ $slot }}
                    </div>
                </div>

                {{-- Footer pequeno opcional --}}
                <div class="mt-6 text-center text-xs text-white/50">
                    © {{ date('Y') }} Communico
                </div>

            </div>
        </div>
    </body>
</html>
