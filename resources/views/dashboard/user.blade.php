<x-app-layout>
    @php
        $user = $user ?? Auth::user();

        $nome = $user->nome ?? $user->name ?? 'Utilizador';

        $totalConversas = method_exists($user, 'conversasAtivas')
            ? $user->conversasAtivas()->count()
            : (method_exists($user, 'conversas') ? $user->conversas()->count() : 0);

        $totalGrupos = method_exists($user, 'gruposAtivos')
            ? $user->gruposAtivos()->count()
            : (method_exists($user, 'grupos') ? $user->grupos()->count() : 0);

        $mensagensEnviadas = \App\Models\Mensagem::where('emissor_id', $user->id)->count();

        $anexosEnviados = \App\Models\Anexo::whereHas('mensagem', function ($q) use ($user) {
            $q->where('emissor_id', $user->id);
        })->count();

        $chatUrl = rtrim(config('app.url') ?: url('/'), '/') . '/chat';
    @endphp

    <x-slot name="header">
        <div class="flex items-start justify-between gap-6">
            <div class="min-w-0">
                <div class="text-xs tracking-wide text-white/55 uppercase">
                    Painel
                </div>

                <h2 class="mt-1 font-semibold text-xl text-white leading-tight">
                    Olá, {{ $nome }}
                </h2>

                <p class="mt-1 text-sm text-white/65">
                    Bem-vindo ao teu painel. Aqui tens atalhos e atividade recente.
                </p>
            </div>

            {{-- ✅ ÚNICA ALTERAÇÃO: self-center -> self-start mt-5 (alinha com o "Olá") --}}
            <div class="hidden sm:flex items-center gap-3 shrink-0 self-start mt-5">
                <x-avatar :user="$user" size="md" />
                <div class="leading-tight">
                    <div class="text-sm font-semibold text-white/85">
                        {{ $nome }}
                    </div>
                    <div class="text-xs text-white/55">
                        {{ $user->email }}
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="comm-card rounded-2xl overflow-hidden relative">
                    <a href="{{ $chatUrl }}" class="absolute inset-0 z-0" aria-label="Abrir chat"></a>

                    <div class="p-6 sm:p-7 text-white/90 relative z-10">
                        <div class="flex items-end justify-between mb-5">
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    Conversas recentes
                                </h3>
                                <p class="mt-1 text-sm text-white/60">
                                    Acede rapidamente às últimas conversas.
                                </p>
                            </div>

                            <a
                                href="{{ route('chat.index') }}"
                                class="text-sm font-semibold hover:underline"
                                style="color: var(--c-a1);"
                            >
                                Ver chat →
                            </a>
                        </div>

                        <div class="space-y-3 pr-1"
                             style="max-height: 270px; overflow-y: auto;">
                            @forelse ($recentConversas as $conversa)
                                @php
                                    $outro = $conversa->participantes->where('id', '!=', $user->id)->first();
                                    $ultima = $conversa->ultimaMensagem;

                                    $nomeOutro = $outro->nome ?? $outro->name ?? 'Conversa';
                                    $isMine = $ultima && ($ultima->emissor_id == $user->id);
                                    $emissorNome = $isMine ? 'Tu' : ($ultima->emissor?->nome ?? $ultima->emissor?->name ?? '—');
                                    $textoUltima = $ultima ? ($ultima->conteudo ?? 'Anexo') : null;
                                @endphp

                                <a
                                    href="{{ route('chat.index', ['c' => $conversa->id]) }}"
                                    class="block rounded-xl border px-4 py-3 transition"
                                    style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);"
                                    onmouseover="this.style.background='rgba(255,255,255,.05)'"
                                    onmouseout="this.style.background='rgba(255,255,255,.02)'"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-white/90 truncate">
                                                {{ $nomeOutro }}
                                            </div>

                                            @if ($ultima)
                                                <div class="mt-1 text-xs text-white/65 truncate">
                                                    <span class="font-semibold text-white/75">{{ $emissorNome }}:</span>
                                                    {{ $textoUltima }}
                                                </div>
                                            @else
                                                <div class="mt-1 text-xs text-white/55">
                                                    Sem mensagens ainda.
                                                </div>
                                            @endif
                                        </div>

                                        @if ($ultima)
                                            <div class="text-[11px] text-white/45 whitespace-nowrap">
                                                {{ $ultima->created_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-xl border px-4 py-4 text-sm text-white/60"
                                     style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);">
                                    Sem conversas recentes.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="comm-card rounded-2xl overflow-hidden">
                    <div class="p-6 sm:p-7 text-white/90">
                        <div class="flex items-end justify-between mb-5">
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    Os teus grupos
                                </h3>
                                <p class="mt-1 text-sm text-white/60">
                                    Conversas em equipa e projetos.
                                </p>
                            </div>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                  style="border-color: rgba(45,212,191,.25); background: rgba(45,212,191,.10); color: rgba(167,243,208,.95);">
                                {{ $totalGrupos }} total
                            </span>
                        </div>

                        <div class="space-y-3 pr-1"
                             style="max-height: 270px; overflow-y: auto;">
                            @forelse ($recentGrupos as $grupo)
                                <a
                                    href="{{ route('chat.index', ['g' => $grupo->id]) }}"
                                    class="block rounded-xl border px-4 py-3 transition"
                                    style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);"
                                    onmouseover="this.style.background='rgba(255,255,255,.05)'"
                                    onmouseout="this.style.background='rgba(255,255,255,.02)'"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-white/90 truncate">
                                                {{ $grupo->nome }}
                                            </div>

                                            @if(isset($grupo->pivot?->created_at))
                                                <div class="mt-1 text-xs text-white/55">
                                                    Entraste: {{ $grupo->pivot->created_at->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>

                                        <span class="text-[11px] text-white/45 whitespace-nowrap">
                                            Abrir →
                                        </span>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-xl border px-4 py-4 text-sm text-white/60"
                                     style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);">
                                    Ainda não pertences a grupos.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="comm-card rounded-2xl overflow-hidden">
                    <div class="p-6 sm:p-7 text-white/90">
                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-white">
                                Estatísticas
                            </h3>
                            <p class="mt-1 text-sm text-white/60">
                                Visão rápida da tua atividade no Communico.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="rounded-2xl border p-4"
                                 style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.03);">
                                <div class="text-xs text-white/55">Conversas</div>
                                <div class="mt-1 text-2xl font-semibold" style="color: var(--c-a1);">
                                    {{ $totalConversas }}
                                </div>
                            </div>

                            <div class="rounded-2xl border p-4"
                                 style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.03);">
                                <div class="text-xs text-white/55">Grupos</div>
                                <div class="mt-1 text-2xl font-semibold" style="color: var(--c-a2);">
                                    {{ $totalGrupos }}
                                </div>
                            </div>

                            <div class="rounded-2xl border p-4"
                                 style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.03);">
                                <div class="text-xs text-white/55">Mensagens enviadas</div>
                                <div class="mt-1 text-2xl font-semibold text-white">
                                    {{ $mensagensEnviadas }}
                                </div>
                            </div>

                            <div class="rounded-2xl border p-4"
                                 style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.03);">
                                <div class="text-xs text-white/55">Anexos enviados</div>
                                <div class="mt-1 text-2xl font-semibold text-white">
                                    {{ $anexosEnviados }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

                <a
                    href="{{ route('chat.index') }}"
                    class="comm-card rounded-2xl p-5 text-center border transition hover:-translate-y-0.5"
                    style="
                        border-color: rgba(255,255,255,.12);
                        background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));
                    "
                >
                    <div class="text-sm font-semibold text-white">
                        Abrir chat
                    </div>
                    <div class="mt-1 text-xs text-white/65">
                        Ir para conversas e grupos
                    </div>
                </a>

                <a
                    href="{{ route('chat.index') }}"
                    class="comm-card rounded-2xl p-5 text-center border transition hover:-translate-y-0.5"
                    style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                >
                    <div class="text-sm font-semibold text-white/90">
                        Iniciar nova conversa
                    </div>
                    <div class="mt-1 text-xs text-white/65">
                        Seleciona um utilizador no chat
                    </div>
                </a>

                <a
                    href="{{ route('profile.edit') }}"
                    class="comm-card rounded-2xl p-5 text-center border transition hover:-translate-y-0.5"
                    style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                >
                    <div class="text-sm font-semibold text-white/90">
                        Gerir conta
                    </div>
                    <div class="mt-1 text-xs text-white/65">
                        Dados e palavra-passe
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
