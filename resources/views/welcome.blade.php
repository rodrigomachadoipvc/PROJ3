<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Communico - Chat Interno</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            /* Base (fundo) */
            --c-bg-0: #070B14;
            --c-bg-1: #0B1220;

            /* Acentos (aproximação às cores do logo: azul + verde/água) */
            --c-a1: #60A5FA;  /* azul */
            --c-a2: #2DD4BF;  /* verde/água */

            --c-t1: rgba(255,255,255,.92);
            --c-t2: rgba(255,255,255,.72);
            --c-stroke: rgba(255,255,255,.12);
        }

        .comm-bg{
            background:
                radial-gradient(900px 500px at 20% 10%, rgba(96,165,250,.18), transparent 55%),
                radial-gradient(900px 500px at 80% 20%, rgba(45,212,191,.14), transparent 55%),
                linear-gradient(180deg, var(--c-bg-0), var(--c-bg-1));
        }

        .comm-card{
            background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.04));
            border: 1px solid var(--c-stroke);
            box-shadow: 0 24px 60px rgba(0,0,0,.35);
        }

        .comm-link{ color: rgba(255,255,255,.80); }
        .comm-link:hover{ color: rgba(255,255,255,.95); }
    </style>
</head>

<body class="antialiased">
<div class="min-h-screen comm-bg relative overflow-hidden flex flex-col">

    {{-- Top-right auth links --}}
    @if (Route::has('login'))
        <div class="fixed top-0 right-0 p-6 text-right z-20">
            @auth
                <a href="{{ url('/dashboard') }}"
                   class="comm-link font-semibold focus:outline focus:outline-2 focus:rounded-sm"
                   style="outline-color: rgba(96,165,250,.55);">
                    Painel
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="comm-link font-semibold focus:outline focus:outline-2 focus:rounded-sm"
                   style="outline-color: rgba(96,165,250,.55);">
                    Entrar
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="ml-10 comm-link font-semibold focus:outline focus:outline-2 focus:rounded-sm"
                       style="outline-color: rgba(45,212,191,.55);">
                        Criar conta
                    </a>
                @endif
            @endauth
        </div>
    @endif

    {{-- Conteúdo central --}}
    <main class="flex-1 flex items-center">
        <div class="max-w-6xl mx-auto px-6 w-full">
            <div class="flex flex-col items-center text-center">

                {{-- Logo (public/images/logo_communico.svg) --}}
                <div class="mb-8">
                        <img
                        src="{{ asset('images/logo_communico.svg') }}"
                        alt="Communico"
                        class="h-10 w-auto"
                    />
                    
                </div>

                <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight" style="color: var(--c-t1);">
                    Communico
                </h1>

                <p class="mt-4 max-w-3xl text-base sm:text-lg leading-relaxed" style="color: var(--c-t2);">
                    Comunicação interna simples, rápida e organizada. Conversas diretas e em grupo,
                    com gestão de utilizadores e permissões num só lugar.
                </p>

                {{-- Highlights (centrados + caixas centradas na página) --}}
                <div class="mt-10 w-full grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="comm-card rounded-2xl p-7 text-center">
                        <h3 class="text-lg font-semibold" style="color: var(--c-t1);">
                            Organização por grupos
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed" style="color: var(--c-t2);">
                            Conversas por equipas e projetos, com contexto e histórico.
                        </p>
                    </div>

                    <div class="comm-card rounded-2xl p-7 text-center">
                        <h3 class="text-lg font-semibold" style="color: var(--c-t1);">
                            Administração prática
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed" style="color: var(--c-t2);">
                            Gestão de utilizadores, permissões e grupos, sem complicações.
                        </p>
                    </div>

                    <div class="comm-card rounded-2xl p-7 text-center">
                        <h3 class="text-lg font-semibold" style="color: var(--c-t1);">
                            Interface limpa
                        </h3>
                        <p class="mt-2 text-sm leading-relaxed" style="color: var(--c-t2);">
                            Visual moderno e focado no trabalho — rápido de usar, sem ruído.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- Footer no fundo --}}
    <footer class="mt-auto">
        <div class="max-w-6xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4"
             style="color: rgba(255,255,255,.55);">
            <div class="text-sm">
                © {{ date('Y') }} Communico
            </div>
            <div class="text-sm">
                Laravel • Livewire • Reverb
            </div>
        </div>
    </footer>

</div>
</body>
</html>
