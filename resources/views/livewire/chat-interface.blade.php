<div
    x-data="{
        showMemberDeleteModal: false,
        memberIdToRemove: null,
        memberNameToRemove: ''
    }"
    class="fixed top-16 left-0 right-0 bottom-0 z-10 flex flex-col overflow-hidden comm-bg text-[var(--c-t1)]"
    style="background-color: var(--c-bg-0);"
>
    <!-- Cabeçalho -->
    <header class="sticky top-0 z-20 comm-card rounded-none border-x-0 border-t-0 backdrop-blur shrink-0">
        <div class="w-full py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center min-w-0">
            <div class="flex items-center space-x-3 min-w-0">
                @if ($conversaAtiva)
                    @php
                        $avatar = null;
                        $label = null;
                        if ($conversaAtiva->tipo === 'dm') {
                            $outro = $conversaAtiva->participantes->where('id', '!=', Auth::id())->first();
                            $avatar = $outro?->avatar_url_public;
                            $label = $outro?->nome ?? 'Conversa DM';
                        } else {
                            $grupo = $conversaAtiva->grupo;
                            $avatar = $grupo?->foto_path ? asset('storage/' . $grupo->foto_path) : null;
                            $label = $grupo?->nome ?? 'Grupo';
                        }
                    @endphp
                    @if ($avatar)
                        <img src="{{ $avatar }}" class="w-9 h-9 rounded-full object-cover">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[var(--c-a1)] to-[var(--c-a2)] text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(mb_substr($label, 0, 2)) }}
                        </div>
                    @endif

                    <h2 class="font-semibold text-xl text-white leading-tight truncate">
                        {{ $label }}
                    </h2>

                    @if ($conversaAtiva->tipo === 'grupo' && $this->userPodeGerirGrupo())
                        <button type="button" wire:click.stop.prevent="openEditarGrupo" class="text-sm text-white/70 hover:text-white shrink-0">
                            Editar
                        </button>
                    @endif
                @else
                    <h2 class="font-semibold text-xl text-white leading-tight">
                        Communico Chat
                    </h2>
                @endif
            </div>

            @if ($conversaAtiva && $conversaAtiva->tipo === 'grupo')
                <button
                    wire:click="toggleMembrosGrupo"
                    class="text-sm text-white/70 hover:text-white flex items-center space-x-1 shrink-0"
                >
                    <span>👥</span>
                    <span>Membros</span>
                </button>
            @endif
        </div>
    </header>

    <!-- Corpo -->
    <div class="flex flex-1 min-h-0 overflow-hidden w-full min-w-0">
        <!-- Sidebar -->
        <aside class="w-1/4 comm-card border-r border-white/10 p-4 flex flex-col min-h-0 overflow-hidden shrink-0 min-w-[280px]">
            <!-- Botão Nova conversa -->
            <div class="flex items-center gap-2 mb-3 shrink-0">
                <button type="button"
                        wire:click="abrirNovaConversa"
                        class="w-full bg-gradient-to-r from-[var(--c-a1)] to-[var(--c-a2)] text-white text-sm font-semibold py-2 rounded-md hover:brightness-110">
                    + Nova conversa
                </button>
            </div>

            <!-- Painel de seleção de utilizador (picker) -->
            @if ($mostrarNovaConversa)
                <div class="mb-3 bg-white/5 border border-white/10 rounded-md p-2 shrink-0">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-white/90">Nova conversa</p>
                        <button type="button"
                                wire:click="fecharNovaConversa"
                                class="text-white/40 hover:text-white/70">
                            ✕
                        </button>
                    </div>

                    <input type="text"
                           wire:model.live.debounce.200ms="searchUser"
                           placeholder="Procurar utilizador..."
                           class="w-full rounded-xl bg-white/5 border border-white/10 text-white/90 placeholder-white/40 focus:border-[var(--c-a2)] focus:ring-[var(--c-a2)] text-sm mb-2">

                    <div class="max-h-72 overflow-y-auto">
                        @forelse ($utilizadoresFiltrados as $u)
                            <button type="button"
                                    wire:click="iniciarConversaDM({{ $u->id }})"
                                    class="w-full text-left p-2 rounded-md hover:bg-white/10 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $u->nome }}</p>
                                    <p class="text-xs text-white/70">{{ $u->tipoUtilizador->nome ?? '—' }}</p>
                                </div>
                                <span class="text-white/40">›</span>
                            </button>
                        @empty
                            <p class="text-xs text-white/70 p-2">Sem resultados.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Pesquisa de conversas -->
            <div class="mb-3 shrink-0">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchConversa"
                    placeholder="Pesquisar (pessoa, grupo ou mensagem)..."
                    class="w-full rounded-xl bg-white/5 border border-white/10 text-white/90 placeholder-white/40 focus:border-[var(--c-a2)] focus:ring-[var(--c-a2)] text-sm"
                >
            </div>

            <!-- Sistema (fixo, não afetado por filtros/pesquisa) -->
            @if ($conversaSistema)
                @php
                    $ultimaSistema = $conversaSistema->ultimaMensagem ?? null;
                @endphp

                <div wire:key="conversa-sistema"
                     wire:click="selecionarConversa({{ $conversaSistema->id }})"
                     class="block p-3 rounded-lg cursor-pointer shadow-sm mb-4 border transition shrink-0
                     {{ $conversaAtiva && $conversaAtiva->id === $conversaSistema->id
                        ? 'bg-white/10 border-white/20'
                        : 'bg-white/5 hover:bg-white/10 border-white/10' }}">

                    <div class="flex items-start justify-between gap-2">
                        <p class="font-semibold text-sm text-white truncate">Sistema</p>
                    </div>

                    @if ($ultimaSistema)
                        <p class="text-xs text-white/70 truncate mt-1">
                            <strong>{{ $ultimaSistema->emissor_id == Auth::id() ? 'Tu' : ($ultimaSistema->emissor?->nome ?? 'Sistema') }}:</strong>
                            {{ $ultimaSistema->conteudo ?? 'Anexo' }}
                        </p>
                        <span class="text-[11px] text-white/50 float-right">
                            {{ $ultimaSistema->created_at->diffForHumans() }}
                        </span>
                        <div class="clear-both"></div>
                    @else
                        <p class="text-xs text-white/50 italic mt-1">Sem mensagens ainda.</p>
                    @endif
                </div>
            @endif

            <h3 class="text-lg font-semibold mb-2 text-white/90 shrink-0">Conversas</h3>

            <!-- Filtros -->
            <div class="flex gap-2 mb-4 overflow-x-auto pb-1 shrink-0">
                <button type="button"
                    wire:click="setFiltroConversas('todas')"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border whitespace-nowrap
                    {{ $filtroConversas === 'todas' ? 'bg-white/10 text-white border-white/10' : 'bg-white/5 text-white/70 border-white/10 hover:bg-white/10' }}">
                    Todos
                </button>

                <button type="button"
                    wire:click="setFiltroConversas('grupos')"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border whitespace-nowrap
                    {{ $filtroConversas === 'grupos' ? 'bg-white/10 text-white border-white/10' : 'bg-white/5 text-white/70 border-white/10 hover:bg-white/10' }}">
                    Grupos
                </button>

                <button type="button"
                    wire:click="setFiltroConversas('favoritos')"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border whitespace-nowrap
                    {{ $filtroConversas === 'favoritos' ? 'bg-white/10 text-white border-white/10' : 'bg-white/5 text-white/70 border-white/10 hover:bg-white/10' }}">
                    Favoritos
                </button>
            </div>

            <!-- Lista de conversas -->
            <div class="flex-1 min-h-0 overflow-y-auto pr-1">
                @forelse ($conversas as $conversa)
                    @php
                        $ultima = $conversa->ultimaMensagem ?? null;

                        if ($conversa->tipo === 'dm') {
                            $outro = $conversa->participantes->where('id', '!=', Auth::id())->first();
                            $titulo = $outro?->nome ?? 'Conversa';
                        } else {
                            $titulo = $conversa->grupo?->nome ?? 'Grupo';
                        }

                        $termo = trim($searchConversa);
                        $matchBody = $conversa->matched_message_body ?? null;
                    @endphp

                    <div wire:key="conversa-{{ $conversa->id }}" wire:click="selecionarConversa({{ $conversa->id }}, {{ $conversa->matched_message_id ?? 'null' }})"
                         class="block p-3 rounded-lg cursor-pointer shadow-sm mb-2 border transition
                         {{ $conversaAtiva && $conversaAtiva->id === $conversa->id
                            ? 'bg-white/10 border-white/20 ring-1 ring-[var(--c-a2)]'
                            : 'bg-white/5 hover:bg-white/10 border-white/10' }}">

                        <div class="flex items-start justify-between gap-2">
                            <p class="font-semibold text-sm text-white truncate">
                                @if ($termo !== '' && stripos($titulo, $termo) !== false)
                                    {!! $this->highlightTermUnderline($titulo, $termo) !!}
                                @else
                                    {{ $titulo }}
                                @endif
                            </p>

                            <button type="button"
                                    wire:click.stop="togglePin({{ $conversa->id }})"
                                    class="text-white/50 hover:text-[var(--c-a2)] shrink-0"
                                    title="{{ (($conversa->is_pinned ?? 0) > 0) ? 'Remover pin' : 'Fixar conversa' }}">
                                @if (($conversa->is_pinned ?? 0) > 0) ⭐ @else ☆ @endif
                            </button>
                        </div>

                        @if ($termo !== '' && $matchBody)
                            <p class="text-xs text-white/70 truncate mt-1">
                                <span class="text-[var(--c-a2)] font-semibold">Encontrado:</span>
                                {!! $this->highlightTerm($matchBody, $termo) !!}
                            </p>
                            @if ($ultima)
                                <span class="text-[11px] text-white/45 float-right">
                                    {{ $ultima->created_at->diffForHumans() }}
                                </span>
                            @endif
                        @elseif ($termo !== '' && $conversa->matched_group_name)
                            <p class="text-xs text-white/70 truncate mt-1">
                                <span class="text-[var(--c-a2)] font-semibold">Encontrado:</span>
                                {!! $this->highlightTermUnderline($conversa->matched_group_name, $termo) !!}
                            </p>
                            @if ($ultima)
                                <span class="text-[11px] text-white/45 float-right">
                                    {{ $ultima->created_at->diffForHumans() }}
                                </span>
                            @endif
                        @elseif ($termo !== '' && $conversa->matched_participant_name)
                            <p class="text-xs text-white/70 truncate mt-1">
                                <span class="text-[var(--c-a2)] font-semibold">Encontrado:</span>
                                {!! $this->highlightTermUnderline($conversa->matched_participant_name, $termo) !!}
                            </p>
                            @if ($ultima)
                                <span class="text-[11px] text-white/45 float-right">
                                    {{ $ultima->created_at->diffForHumans() }}
                                </span>
                            @endif
                        @elseif ($ultima)
                            <p class="text-xs text-white/70 truncate mt-1">
                                <strong>{{ $ultima->emissor_id == Auth::id() ? 'Tu' : $ultima->emissor?->nome }}:</strong>
                                {{ $ultima->conteudo ?? 'Anexo' }}
                            </p>
                            <span class="text-[11px] text-white/45 float-right">
                                {{ $ultima->created_at->diffForHumans() }}
                            </span>
                        @else
                            <p class="text-xs text-white/45 italic mt-1">Nenhuma mensagem ainda.</p>
                        @endif

                        <div class="clear-both"></div>
                    </div>
                @empty
                    <p class="text-sm text-white/70">Sem conversas.</p>
                @endforelse
            </div>
        </aside>

        <!-- Área das Mensagens -->
        <section class="flex flex-col flex-1 min-h-0 overflow-hidden min-w-0">
            <div
                id="chat-scroll"
                data-conversa-id="{{ $conversaAtiva?->id ?? '' }}"
                data-last-msg-id="{{ $mensagens->last()?->id ?? '' }}"
                data-highlight-id="{{ $highlightMessageId ?? '' }}"
                class="flex-1 min-h-0 p-6 overflow-y-auto overscroll-contain min-w-0"
            >
                @php
                    $initials = function (?string $nome): string {
                        $nome = trim($nome ?? '');
                        if ($nome === '') return '?';
                        $partes = preg_split('/\s+/', $nome);
                        return strtoupper(
                            substr($partes[0], 0, 1) . (count($partes) > 1 ? substr(end($partes), 0, 1) : '')
                        );
                    };
                    $previousEmissorId = null;
                @endphp

                @if ($conversaAtiva)
                    @forelse ($mensagens as $mensagem)
                        @php
                            $sender = $mensagem->emissor;
                            $isOwn = $mensagem->emissor_id == Auth::id();
                            $isNewGroup = $previousEmissorId !== $mensagem->emissor_id;
                            $previousEmissorId = $mensagem->emissor_id;
                            $senderAvatar = $sender->avatar_url_public;
                            $senderInitials = $initials($sender->nome);
                        @endphp

                        <div id="msg-{{ $mensagem->id }}"
                             class="{{ $highlightMessageId === $mensagem->id ? 'ring-2 ring-yellow-400/60 rounded-2xl' : '' }}">
                            {{-- MANTÉM O TEU BLOCO INTEIRO DE MENSAGENS COMO ESTÁ --}}
                            @if ($isNewGroup)
                                @if ($isOwn)
                                    <div class="flex items-start justify-end mt-4 space-x-3">
                                        <div class="max-w-lg">
                                            <div class="bg-[rgba(96,165,250,.18)] border border-white/10 text-white rounded-2xl px-4 py-2 shadow-sm">
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">{{ $mensagem->conteudo }}</p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-white/10 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-white/80 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-white/45">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="text-[var(--c-a2)] hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-white/45 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if ($senderAvatar)
                                                <img src="{{ $senderAvatar }}" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[var(--c-a1)] to-[var(--c-a2)] text-white flex items-center justify-center text-xs font-semibold">
                                                    {{ $senderInitials }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start justify-start mt-4 space-x-3">
                                        <div class="flex-shrink-0">
                                            @if ($senderAvatar)
                                                <img src="{{ $senderAvatar }}" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center text-xs font-semibold">
                                                    {{ $senderInitials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="max-w-lg">
                                            <div class="bg-white/5 border border-white/10 text-white rounded-2xl px-4 py-2 shadow-sm">
                                                <p class="text-xs font-semibold text-white/80 mb-1">{{ $sender->nome }}</p>
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">{{ $mensagem->conteudo }}</p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-white/10 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-white/80 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-white/45">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="text-[var(--c-a2)] hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-white/45 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                @if ($isOwn)
                                    <div class="flex justify-end mt-1">
                                        <div class="max-w-lg mr-10">
                                            <div class="bg-[rgba(96,165,250,.18)] border border-white/10 text-white rounded-2xl px-4 py-2 shadow-sm">
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">{{ $mensagem->conteudo }}</p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-white/10 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-white/80 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-white/45">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="text-[var(--c-a2)] hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-white/45 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex justify-start mt-1">
                                        <div class="max-w-lg ml-10">
                                            <div class="bg-white/5 border border-white/10 text-white rounded-2xl px-4 py-2 shadow-sm">
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">{{ $mensagem->conteudo }}</p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-white/10 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-white/80 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-white/45">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}" target="_blank" class="text-[var(--c-a2)] hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-white/45 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-white/70 text-sm italic">Nenhuma mensagem ainda.</p>
                    @endforelse
                @else
                    <p class="text-center text-white/70 text-sm italic">Selecione uma conversa para ver as mensagens.</p>
                    <p class="text-center text-white/70 text-sm italic">Caso tenha alguma dúvida mande, em qualquer dm ou grupo, @sistema e mostrará todas as ajudas!</p>
                @endif

                <div id="chat-bottom-anchor" class="h-0"></div>
            </div>

            <!-- Caixa de envio -->
            <div class="comm-card rounded-none border-x-0 border-b-0 border-t border-white/10 backdrop-blur p-4 shrink-0">
                @if ($conversaAtiva)
                    <form wire:submit.prevent="enviarMensagem" class="flex items-center space-x-3">
                        @csrf
                        <label class="cursor-pointer flex items-center justify-center w-9 h-9 rounded-md bg-white/5 border border-white/10 hover:bg-white/10">
                            <svg class="w-5 h-5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828a4 4 0 10-5.656-5.656L6.343 10.172a6 6 0 108.485 8.485L20 13"/>
                            </svg>
                            <input type="file" class="hidden" multiple wire:model="uploads" id="chat-upload-input">
                        </label>

                        <input type="text"
                               wire:model.defer="novaMensagem"
                               placeholder="Escreva a sua mensagem..."
                               class="flex-grow rounded-xl bg-white/5 border border-white/10 text-white/90 placeholder-white/40 focus:border-[var(--c-a2)] focus:ring-[var(--c-a2)] shadow-sm text-sm"
                               autocomplete="off" id="chat-message-input" wire:key="chat-message-input-{{ $conversaAtiva?->id ?? 'none' }}">

                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="uploads,enviarMensagem"
                                class="bg-gradient-to-r from-[var(--c-a1)] to-[var(--c-a2)] hover:brightness-110 disabled:opacity-50 text-white font-bold py-2 px-4 rounded text-sm">
                            <span wire:loading.remove wire:target="enviarMensagem">Enviar</span>
                            <span wire:loading wire:target="enviarMensagem">A enviar…</span>
                        </button>
                    </form>

                    @if (!empty($uploads))
                        <div class="mt-2 bg-white/5 border border-white/10 rounded-md p-2">
                            <p class="text-xs text-white/70 font-semibold mb-1">
                                Ficheiros selecionados ({{ count($uploads) }}):
                            </p>
                            <ul class="space-y-1">
                                @foreach ($uploads as $i => $file)
                                    <li class="flex items-center justify-between text-xs text-white/80">
                                        <span class="truncate">
                                            📎 {{ $file->getClientOriginalName() }}
                                            <span class="text-white/45">
                                                ({{ number_format($file->getSize() / 1024 / 1024, 2) }} MB)
                                            </span>
                                        </span>
                                        <button type="button"
                                                wire:click="removerUpload({{ $i }})"
                                                class="text-red-500 hover:text-red-400 text-xs ml-2">
                                            Remover
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @error('uploads') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @error('uploads.*') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @else
                    <p class="text-center text-white/70 text-sm italic">Selecione uma conversa.</p>
                @endif
            </div>
        </section>

        @if ($mostrarMembrosGrupo && $membrosGrupo)
            <aside class="w-64 comm-card border-l border-white/10 p-4 overflow-y-auto shrink-0">
                {{-- igual ao teu --}}
                <h3 class="text-sm font-semibold text-white/90 mb-3">
                    Membros do Grupo ({{ $membrosGrupo->count() }})
                </h3>
                @if ($this->userPodeGerirMembros())
                    <button wire:click.stop.prevent="abrirAdicionarMembro" class="text-sm text-[var(--c-a2)] hover:underline mb-3">
                        Adicionar membro
                    </button>
                @endif
                <ul class="space-y-2">
                    @foreach ($membrosGrupo as $membro)
                        <li wire:key="membro-{{ $membro->id }}" class="flex items-center space-x-2">
                            @if ($membro->avatar_url_public)
                                <img src="{{ $membro->avatar_url_public }}" class="w-7 h-7 rounded-full object-cover">
                            @else
                                <div class="w-7 h-7 rounded-full bg-white/10 text-white flex items-center justify-center text-xs">
                                    {{ strtoupper(mb_substr($membro->nome, 0, 2)) }}
                                </div>
                            @endif
                            <span class="text-sm text-white flex-1">{{ $membro->nome }}</span>
                            @php
                                $role = $membro->pivot->role ?? 'member';
                                $badge = $role === 'moderator' ? 'Moderador' : 'Membro';
                                $grupo = $conversaAtiva->grupo;
                                $isCriador = (int)$grupo->criador_id === (int)$membro->id;
                                $isSystemAdmin = optional($membro->tipoUtilizador)->nome === 'Administrador';
                                $isBloqueado = $isCriador || $isSystemAdmin;
                            @endphp

                            @if (!$this->userPodeGerirMembros())
                                <span class="text-xs bg-white/10 text-[var(--c-a2)] px-2 py-0.5 rounded border border-white/10">
                                    {{ $badge }}
                                </span>
                            @else
                                @if ($isBloqueado)
                                    <span class="text-xs bg-white/10 text-[var(--c-a2)] px-2 py-0.5 rounded border border-white/10">Moderador</span>
                                    <span class="text-xs text-white/50 ml-1">🔒</span>
                                @else
                                    <select
                                        wire:change.stop="definirRoleMembro({{ $membro->id }}, $event.target.value)"
                                        class="comm-select text-xs bg-white/5 border border-white/10 text-white/90 rounded-lg px-2 py-1 focus:ring-[var(--c-a2)] focus:border-[var(--c-a2)]"
                                    >
                                        <option value="member" {{ $role === 'member' ? 'selected' : '' }}>Membro</option>
                                        <option value="moderator" {{ $role === 'moderator' ? 'selected' : '' }}>Moderador</option>
                                    </select>

                                    {{-- CONFIRMAÇÃO (modal) --}}
                                    <button
                                        type="button"
                                        class="text-red-500 hover:text-red-400 text-xs"
                                        @click.stop.prevent="
                                            memberIdToRemove = {{ $membro->id }};
                                            memberNameToRemove = @js($membro->nome);
                                            showMemberDeleteModal = true;
                                        "
                                    >
                                        Remover
                                    </button>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>
            </aside>
        @endif
    </div>

    {{-- MODAL: confirmação de remover membro --}}
    <div
        x-show="showMemberDeleteModal"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-[999] flex items-center justify-center p-4"
        aria-modal="true"
        role="dialog"
        @keydown.escape.window="showMemberDeleteModal = false"
    >
        <div
            class="absolute inset-0"
            style="background: rgba(0,0,0,.65); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
            @click="showMemberDeleteModal = false"
        ></div>

        <div
            class="relative w-full max-w-lg rounded-2xl border overflow-hidden"
            style="
                border-color: rgba(255,255,255,.12);
                background: radial-gradient(900px 520px at 70% 10%, rgba(248,113,113,.10), transparent 60%),
                            radial-gradient(900px 520px at 25% 40%, rgba(96,165,250,.08), transparent 55%),
                            rgba(10,15,24,.92);
                box-shadow: 0 30px 80px rgba(0,0,0,.55);
            "
        >
            <div class="px-6 py-5 border-b" style="border-color: rgba(255,255,255,.10);">
                <h3 class="text-xl font-semibold text-white leading-tight">
                    Remover membro
                </h3>
                <p class="mt-1 text-sm text-white/65 leading-snug">
                    Tem a certeza que deseja remover <span class="font-semibold text-white/85" x-text="memberNameToRemove"></span> do grupo?
                    Esta ação não pode ser revertida.
                </p>
            </div>

            <div class="px-6 py-4 border-t flex justify-end gap-3" style="border-color: rgba(255,255,255,.10);">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white/85 border transition hover:text-white hover:brightness-110"
                    style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                    @click="showMemberDeleteModal = false"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white border transition hover:brightness-110"
                    style="
                        border-color: rgba(255,255,255,.12);
                        background: linear-gradient(180deg, rgba(248,113,113,.22), rgba(248,113,113,.10));
                    "
                    @click="
                        if (memberIdToRemove) { $wire.removerMembroDoGrupo(memberIdToRemove); }
                        showMemberDeleteModal = false;
                        memberIdToRemove = null;
                        memberNameToRemove = '';
                    "
                >
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    {{-- MODAIS --}}
    @if ($mostrarEditarGrupo)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[999] p-4">
            <div class="comm-card w-full max-w-lg rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
                <!-- Header estilo "Eliminar" -->
                <div class="px-6 py-5 flex items-start gap-4 bg-white/[0.03] border-b border-white/10">
                    <div class="w-11 h-11 rounded-full bg-white/5 border border-white/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 4h2m-1 0v16m8-8H4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-white">Editar Grupo</h2>
                        <p class="text-sm text-white/60 mt-1">
                            Atualize o nome e a imagem do grupo.
                        </p>
                    </div>
                </div>

                <!-- Corpo -->
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-white/80">Nome do Grupo</label>
                        <input type="text" wire:model.defer="novoNomeGrupo"
                               class="mt-2 block w-full rounded-xl bg-white/5 border border-white/10 text-white/90 placeholder-white/40 focus:border-[var(--c-a2)] focus:ring-[var(--c-a2)]">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white/80">Foto do Grupo</label>
                        <input
                            type="file"
                            wire:model="fotoGrupoUpload"
                            class="mt-2 block w-full rounded-xl bg-white/5 border border-white/10 text-sm text-white/80
                                   file:mr-4 file:rounded-lg file:border-0 file:bg-white/10 file:px-4 file:py-2
                                   file:text-sm file:font-semibold file:text-white hover:file:bg-white/15"
                        >
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 flex justify-end gap-3 bg-white/[0.02] border-t border-white/10">
                    <button type="button"
                            wire:click="cancelarEditarGrupo"
                            class="bg-white/5 text-white/80 hover:bg-white/10 px-4 py-2 rounded-md">
                        Cancelar
                    </button>

                    <button type="button"
                            wire:click="guardarGrupo"
                            class="bg-gradient-to-r from-[var(--c-a1)] to-[var(--c-a2)] hover:brightness-110 text-white px-4 py-2 rounded-md">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($mostrarAdicionarMembro)
        @php
            $usersPayload = collect($usersDisponiveisAdicionar ?? [])->map(fn ($u) => [
                'id'    => $u->id,
                'nome'  => $u->nome,
                'email' => $u->email,
            ])->values();
        @endphp

        <div
            x-cloak
            class="fixed inset-0 z-[999] flex items-center justify-center p-4"
            aria-modal="true"
            role="dialog"
        >
            {{-- Overlay --}}
            <div
                class="absolute inset-0"
                style="background: rgba(0,0,0,.65); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
                wire:click.stop.prevent="fecharAdicionarMembro"
            ></div>

            {{-- Modal (estilo admin) --}}
            <div
                class="relative w-full max-w-3xl rounded-2xl border overflow-hidden"
                style="
                    border-color: rgba(255,255,255,.12);
                    background: radial-gradient(1200px 600px at 70% 10%, rgba(45,212,191,.10), transparent 60%),
                                radial-gradient(900px 500px at 25% 40%, rgba(96,165,250,.10), transparent 55%),
                                rgba(10,15,24,.92);
                    box-shadow: 0 30px 80px rgba(0,0,0,.55);
                "
                x-data="{
                    users: @js($usersPayload),
                    query: '',
                    open: false,
                    selectedId: null,
                    selectedLabel: '',
                    maxResults: 50,

                    filtered() {
                        const q = (this.query || '').trim().toLowerCase();
                        if (q.length < 1) return [];
                        return this.users
                            .filter(u => {
                                const nome = (u.nome || '').toLowerCase();
                                const email = (u.email || '').toLowerCase();
                                const id = String(u.id || '');
                                return nome.includes(q) || email.includes(q) || id.includes(q);
                            })
                            .slice(0, this.maxResults);
                    },

                    selectUser(u) {
                        this.selectedId = u.id;
                        this.selectedLabel = `${u.nome} (${u.email})`;
                        this.query = this.selectedLabel;
                        this.open = false;
                        if (this.$wire) this.$wire.set('selectedUserToAdd', u.id);
                    },

                    clearSelection() {
                        this.selectedId = null;
                        this.selectedLabel = '';
                        this.query = '';
                        this.open = false;
                        if (this.$wire) this.$wire.set('selectedUserToAdd', '');
                    }
                }"
                @keydown.escape.window="open = false"
            >
                {{-- Header --}}
                <div class="px-6 py-5 border-b" style="border-color: rgba(255,255,255,.10);">
                    <h3 class="text-xl font-semibold text-white leading-tight">
                        Adicionar membro
                    </h3>
                    <p class="mt-1 text-sm text-white/65 leading-snug">
                        Pesquise por nome, email ou ID e selecione o utilizador.
                    </p>
                </div>

                {{-- Body --}}
                <div class="px-6 py-6 space-y-6">
                    <div class="rounded-2xl border p-5" style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);">
                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-start">
                            {{-- Pesquisa + limpar + resultados --}}
                            <div class="relative" @click.outside="open = false">
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3" style="color: rgba(255,255,255,.55);">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.3 6.3a7.5 7.5 0 0 0 10.35 10.35Z" />
                                            </svg>
                                        </span>

                                        <input
                                            type="text"
                                            class="w-full rounded-xl border px-10 py-2.5 text-sm text-white placeholder:text-white/45 focus:outline-none focus:ring-2 focus:ring-sky-400/35"
                                            style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.04);"
                                            placeholder="Procurar utilizador por nome, email ou ID…"
                                            x-model="query"
                                            @focus="open = true"
                                            @input="open = true"
                                            @keydown.escape.prevent="open = false"
                                            @keydown.enter.prevent="
                                                const r = filtered();
                                                if (r.length) selectUser(r[0]);
                                            "
                                            autocomplete="off"
                                        />
                                    </div>

                                    <button
                                        type="button"
                                        x-show="(query || '').trim().length"
                                        x-cloak
                                        class="h-[42px] inline-flex items-center justify-center rounded-xl px-3 text-xs font-semibold border text-white/80 hover:text-white hover:brightness-110"
                                        style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                                        @click="clearSelection()"
                                    >
                                        Limpar
                                    </button>
                                </div>

                                <div x-show="selectedId" x-cloak class="mt-2 text-xs text-white/70">
                                    Selecionado: <span class="font-semibold text-white/85" x-text="selectedLabel"></span>
                                </div>

                                <div
                                    x-show="open && (query || '').trim().length >= 1"
                                    x-cloak
                                    class="mt-3 rounded-2xl border overflow-hidden"
                                    style="border-color: rgba(255,255,255,.12); background: rgba(10,15,24,.92);"
                                >
                                    <div
                                        class="divide-y"
                                        style="divide-color: rgba(255,255,255,.08); max-height: 240px; overflow-y: auto;"
                                    >
                                        <div
                                            x-show="filtered().length === 0"
                                            class="px-4 py-3 text-sm text-white/60"
                                        >
                                            Sem resultados para a pesquisa atual.
                                        </div>

                                        <template x-for="u in filtered()" :key="u.id">
                                            <button
                                                type="button"
                                                class="w-full text-left px-4 py-3 transition"
                                                style="background: rgba(255,255,255,.02);"
                                                @click="selectUser(u)"
                                                @mouseover="($el.style.background='rgba(255,255,255,.06)')"
                                                @mouseout="($el.style.background='rgba(255,255,255,.02)')"
                                            >
                                                <div class="flex items-center justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <div class="text-sm font-semibold text-white/90 truncate" x-text="u.nome"></div>
                                                        <div class="text-xs text-white/60 truncate" x-text="u.email"></div>
                                                    </div>

                                                    <span class="shrink-0 inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-semibold border"
                                                          style="border-color: rgba(45,212,191,.25); background: rgba(45,212,191,.10); color: rgba(153,246,228,.95);">
                                                        Selecionar
                                                    </span>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click.stop.prevent="adicionarMembroSelecionado"
                                class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white border transition hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed"
                                style="
                                    border-color: rgba(255,255,255,.12);
                                    background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));
                                "
                                x-bind:disabled="!selectedId"
                                title="Seleciona um utilizador para adicionar"
                            >
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t flex justify-end gap-3" style="border-color: rgba(255,255,255,.10);">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white/85 border transition hover:text-white hover:brightness-110"
                        style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                        wire:click.stop.prevent="fecharAdicionarMembro"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @endif

    @once
    <script>
    (function () {
        const subscribed = new Set();
        let lastIds = [];
        let activeId = null;

        let lastRenderedConversaId = null;
        let lastRenderedLastMsgId = null;

        function leaveChannel(name) {
            if (!window.Echo) return;
            try { window.Echo.leave(name); } catch (e) {}
            subscribed.delete(name);
        }

        function ensureChannel(conversaId) {
            if (!window.Echo || !conversaId) return;

            const name = `conversa.${conversaId}`;
            if (subscribed.has(name)) return;

            subscribed.add(name);

            window.Echo.private(name)
                .listen('.mensagem.nova', (e) => {
                    if (window.Livewire) {
                        window.Livewire.dispatch('mensagemRecebidaRealtimeSidebar', e);
                    }
                });
        }

        function syncSubscriptions(ids, newActiveId) {
            if (!Array.isArray(ids)) ids = [];
            lastIds = ids;

            if (newActiveId) activeId = newActiveId;

            const wanted = new Set(ids.map(id => `conversa.${id}`));
            if (activeId) wanted.add(`conversa.${activeId}`);

            wanted.forEach((name) => {
                const parts = name.split('.');
                const cid = parseInt(parts[1], 10);
                if (cid) ensureChannel(cid);
            });

            Array.from(subscribed).forEach((name) => {
                if (!wanted.has(name)) leaveChannel(name);
            });
        }

        function scrollChatToBottom(behavior = 'auto') {
            const container = document.getElementById('chat-scroll');
            if (!container) return;

            const anchor = document.getElementById('chat-bottom-anchor');

            const doScroll = () => {
                if (anchor) anchor.scrollIntoView({ block: 'end', behavior });
                else container.scrollTop = container.scrollHeight;
            };

            requestAnimationFrame(doScroll);
            setTimeout(doScroll, 60);
        }

        function bindAddMemberSearch() {
            const input = document.getElementById('add-member-search');
            const list  = document.getElementById('add-member-list');
            if (!input || !list) return;

            if (input.dataset.bound === '1') return;
            input.dataset.bound = '1';

            const applyFilter = () => {
                const q = (input.value || '').trim().toLowerCase();
                const items = list.querySelectorAll('[data-add-member-item="1"]');

                items.forEach((el) => {
                    const hay = (el.dataset.search || '');
                    const show = (q === '') ? true : hay.includes(q);
                    el.style.display = show ? '' : 'none';
                });
            };

            input.addEventListener('input', applyFilter);
            applyFilter();
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('conversas-ids', ({ ids, activeId: aId }) => {
                syncSubscriptions(ids || [], aId || null);
            });

            Livewire.on('conversa-selecionada', (data) => {
                syncSubscriptions(lastIds, data.conversaId);
            });

            if (Livewire.hook) {
                Livewire.hook('message.processed', () => {
                    const container = document.getElementById('chat-scroll');
                    if (container) {
                        const highlightId = (container.dataset.highlightId || '').trim();
                        if (highlightId === '') {
                            const convId = (container.dataset.conversaId || '').trim();
                            const lastMsgId = (container.dataset.lastMsgId || '').trim();

                            if (convId !== '' && convId !== lastRenderedConversaId) {
                                lastRenderedConversaId = convId;
                                lastRenderedLastMsgId = lastMsgId;
                                scrollChatToBottom('auto');
                            } else if (lastMsgId !== '' && lastMsgId !== lastRenderedLastMsgId) {
                                lastRenderedLastMsgId = lastMsgId;
                                scrollChatToBottom('auto');
                            }
                        }
                    }

                    // garante binding da pesquisa do modal "Adicionar Membro"
                    bindAddMemberSearch();
                });
            }

            Livewire.on('scroll-chat-para-fundo', () => scrollChatToBottom('auto'));

            Livewire.on('scroll-to-message', ({ messageId }) => {
                const el = document.getElementById('msg-' + messageId);
                if (!el) return;
                el.scrollIntoView({ block: 'center', behavior: 'smooth' });
                el.classList.add('bg-yellow-500/10');
                setTimeout(() => el.classList.remove('bg-yellow-500/10'), 2000);
                setTimeout(() => {
                    if (window.Livewire) window.Livewire.dispatch('clearHighlight');
                }, 3000);
            });

            Livewire.on('limpar-input-chat', () => {
                const input = document.getElementById('chat-message-input');
                if (input) {
                    input.value = '';
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            Livewire.on('reset-upload-input', () => {
                const upload = document.getElementById('chat-upload-input');
                if (upload) upload.value = '';
            });

            // binding inicial caso o modal já esteja aberto
            bindAddMemberSearch();
        });
    })();
    </script>
    @endonce
</div>
