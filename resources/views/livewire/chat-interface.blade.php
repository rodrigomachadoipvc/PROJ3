<div class="h-screen flex flex-col">
    <!-- Cabeçalho -->
    <header class="bg-white shadow sticky top-0 z-10">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                @if ($conversaAtiva)
                    @php
                        $avatar = null;
                        $label = null;
                        if ($conversaAtiva->tipo === 'dm') {
                            $outro = $conversaAtiva->participantes->where('id', '!=', Auth::id())->first();
                            $avatar = $outro?->avatar_url_public;
                            $label = $outro?->nome ?? 'Conversa DM';
                        } else {
                            $label = $conversaAtiva->grupo?->nome ?? 'Grupo';
                        }
                    @endphp
                    @if ($avatar)
                        <img src="{{ $avatar }}" class="w-9 h-9 rounded-full object-cover">
                    @else
                        <div class="w-9 h-9 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(mb_substr($label, 0, 2)) }}
                        </div>
                    @endif
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $label }}
                    </h2>
                @else
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Communico Chat
                    </h2>
                @endif
            </div>
            @if ($conversaAtiva && $conversaAtiva->tipo === 'grupo')
                <button
                    wire:click="toggleMembrosGrupo"
                    class="text-sm text-gray-600 hover:text-indigo-600 flex items-center space-x-1"
                >
                    <span>👥</span>
                    <span>Membros</span>
                </button>
            @endif
        </div>
    </header>
    <!-- Corpo -->
    <div class="flex flex-grow overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-1/4 bg-gray-100 border-r border-gray-200 overflow-y-auto p-4">
            <!-- Botão Nova conversa -->
            <div class="flex items-center gap-2 mb-3">
                <button type="button"
                        wire:click="abrirNovaConversa"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 rounded-md">
                    + Nova conversa
                </button>
            </div>

            <!-- Painel de seleção de utilizador (picker) -->
            @if ($mostrarNovaConversa)
                <div class="mb-3 bg-white border border-gray-200 rounded-md p-2">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700">Nova conversa</p>
                        <button type="button"
                                wire:click="fecharNovaConversa"
                                class="text-gray-400 hover:text-gray-700">
                            ✕
                        </button>
                    </div>

                    <input type="text"
                           wire:model.live.debounce.200ms="searchUser"
                           placeholder="Procurar utilizador..."
                           class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm mb-2">

                    <div class="max-h-72 overflow-y-auto">
                        @forelse ($utilizadoresFiltrados as $u)
                            <button type="button"
                                    wire:click="iniciarConversaDM({{ $u->id }})"
                                    class="w-full text-left p-2 rounded-md hover:bg-gray-50 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $u->nome }}</p>
                                    <p class="text-xs text-gray-500">{{ $u->tipoUtilizador->nome ?? '—' }}</p>
                                </div>
                                <span class="text-gray-400">›</span>
                            </button>
                        @empty
                            <p class="text-xs text-gray-500 p-2">Sem resultados.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Pesquisa de conversas -->
            <div class="mb-3">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchConversa"
                    placeholder="Pesquisar (pessoa, grupo ou mensagem)..."
                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                >
            </div>

            <!-- Filtros tipo WhatsApp: Todos / Grupos / Favoritos -->
            <div class="flex gap-2 mb-4 overflow-x-auto pb-1">
                <button type="button"
                    wire:click="setFiltroConversas('todas')"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border whitespace-nowrap
                    {{ $filtroConversas === 'todas' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    Todos
                </button>

                <button type="button"
                    wire:click="setFiltroConversas('grupos')"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border whitespace-nowrap
                    {{ $filtroConversas === 'grupos' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    Grupos
                </button>

                <button type="button"
                    wire:click="setFiltroConversas('favoritos')"
                    class="px-4 py-1.5 rounded-full text-sm font-medium border whitespace-nowrap
                    {{ $filtroConversas === 'favoritos' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                    Favoritos
                </button>
            </div>

            <!-- Lista única de conversas (DM + Grupos misturados) -->
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Conversas</h3>

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

                <div wire:click="selecionarConversa({{ $conversa->id }}, {{ $conversa->matched_message_id ?? 'null' }})"
                     class="block p-3 rounded-lg cursor-pointer shadow-sm mb-2 border transition
                     {{ $conversaAtiva && $conversaAtiva->id === $conversa->id
                        ? 'bg-indigo-100 border-indigo-300'
                        : 'bg-white hover:bg-gray-50 border-gray-200' }}">

                    <div class="flex items-start justify-between gap-2">
                        <p class="font-semibold text-sm text-gray-800 truncate">
                            @if ($termo !== '' && stripos($titulo, $termo) !== false)
                                {!! $this->highlightTermUnderline($titulo, $termo) !!}
                            @else
                                {{ $titulo }}
                            @endif
                        </p>

                        <button type="button"
                                wire:click.stop="togglePin({{ $conversa->id }})"
                                class="text-gray-400 hover:text-indigo-600 shrink-0"
                                title="{{ (($conversa->is_pinned ?? 0) > 0) ? 'Remover pin' : 'Fixar conversa' }}">
                            @if (($conversa->is_pinned ?? 0) > 0) ⭐ @else ☆ @endif
                        </button>
                    </div>

                    @if ($termo !== '' && $matchBody)
                        <p class="text-xs text-gray-600 truncate mt-1">
                            <span class="text-indigo-600 font-semibold">Encontrado:</span>
                            {!! $this->highlightTerm($matchBody, $termo) !!}
                        </p>
                        @if ($ultima)
                            <span class="text-[11px] text-gray-400 float-right">
                                {{ $ultima->created_at->diffForHumans() }}
                            </span>
                        @endif
                    @elseif ($termo !== '' && $conversa->matched_group_name)
                        <p class="text-xs text-gray-600 truncate mt-1">
                            <span class="text-indigo-600 font-semibold">Encontrado:</span>
                            {!! $this->highlightTermUnderline($conversa->matched_group_name, $termo) !!}
                        </p>
                        @if ($ultima)
                            <span class="text-[11px] text-gray-400 float-right">
                                {{ $ultima->created_at->diffForHumans() }}
                            </span>
                        @endif
                    @elseif ($termo !== '' && $conversa->matched_participant_name)
                        <p class="text-xs text-gray-600 truncate mt-1">
                            <span class="text-indigo-600 font-semibold">Encontrado:</span>
                            {!! $this->highlightTermUnderline($conversa->matched_participant_name, $termo) !!}
                        </p>
                        @if ($ultima)
                            <span class="text-[11px] text-gray-400 float-right">
                                {{ $ultima->created_at->diffForHumans() }}
                            </span>
                        @endif
                    @elseif ($ultima)
                        <p class="text-xs text-gray-600 truncate mt-1">
                            <strong>{{ $ultima->emissor_id == Auth::id() ? 'Você' : $ultima->emissor?->nome }}:</strong>
                            {{ $ultima->conteudo ?? 'Anexo' }}
                        </p>
                        <span class="text-[11px] text-gray-400 float-right">
                            {{ $ultima->created_at->diffForHumans() }}
                        </span>
                    @else
                        <p class="text-xs text-gray-400 italic mt-1">Nenhuma mensagem ainda.</p>
                    @endif

                    <div class="clear-both"></div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Sem conversas.</p>
            @endforelse
        </aside>

        <!-- Área das Mensagens -->
        <section class="flex flex-col flex-1">
            <div class="flex-grow bg-white p-6 overflow-y-auto">
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
                             class="{{ $highlightMessageId === $mensagem->id ? 'ring-2 ring-yellow-300 rounded-2xl' : '' }}">
                            @if ($isNewGroup)
                                @if ($isOwn)
                                    <div class="flex items-start justify-end mt-4 space-x-3">
                                        <div class="max-w-lg">
                                            <div class="bg-blue-100 text-gray-900 rounded-2xl px-4 py-2 shadow-sm">
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">
                                                        {{ $mensagem->conteudo }}
                                                    </p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}"
                                                                target="_blank"
                                                                class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between
                                                                            bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-gray-700 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-gray-400">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}"
                                                                    target="_blank"
                                                                    class="text-indigo-600 hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-gray-600 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            @if ($senderAvatar)
                                                <img src="{{ $senderAvatar }}" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-semibold">
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
                                                <div class="w-8 h-8 rounded-full bg-gray-400 text-white flex items-center justify-center text-xs font-semibold">
                                                    {{ $senderInitials }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="max-w-lg">
                                            <div class="bg-white border border-gray-200 text-gray-900 rounded-2xl px-4 py-2 shadow-sm">
                                                <p class="text-xs font-semibold text-gray-700 mb-1">{{ $sender->nome }}</p>
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">
                                                        {{ $mensagem->conteudo }}
                                                    </p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}"
                                                                target="_blank"
                                                                class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between
                                                                            bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-gray-700 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-gray-400">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}"
                                                                    target="_blank"
                                                                    class="text-indigo-600 hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-gray-500 flex justify-end">
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
                                            <div class="bg-blue-100 text-gray-900 rounded-2xl px-4 py-2 shadow-sm">
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">
                                                        {{ $mensagem->conteudo }}
                                                    </p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}"
                                                                target="_blank"
                                                                class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between
                                                                            bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-gray-700 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-gray-400">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}"
                                                                    target="_blank"
                                                                    class="text-indigo-600 hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-gray-600 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex justify-start mt-1">
                                        <div class="max-w-lg ml-10">
                                            <div class="bg-white border border-gray-200 text-gray-900 rounded-2xl px-4 py-2 shadow-sm">
                                                @if (!empty($mensagem->conteudo))
                                                    <p class="text-sm break-words">
                                                        {{ $mensagem->conteudo }}
                                                    </p>
                                                @endif
                                                @if ($mensagem->anexos && $mensagem->anexos->count())
                                                    <div class="mt-2 space-y-2">
                                                        @foreach ($mensagem->anexos as $anexo)
                                                            @php
                                                                $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                                $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                            @endphp
                                                            @if ($isImage)
                                                                <a href="{{ route('anexos.ver', $anexo) }}"
                                                                target="_blank"
                                                                class="inline-block">
                                                                    <img src="{{ route('anexos.ver', $anexo) }}"
                                                                        alt="{{ $anexo->nome_original }}"
                                                                        class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                                </a>
                                                            @else
                                                                <div class="flex items-center justify-between
                                                                            bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
                                                                    <div class="flex items-center space-x-2 text-xs text-gray-700 truncate">
                                                                        <span>📎</span>
                                                                        <span class="truncate">{{ $anexo->nome_original }}</span>
                                                                        <span class="text-gray-400">({{ $sizeMb }} MB)</span>
                                                                    </div>
                                                                    <a href="{{ route('anexos.ver', $anexo) }}"
                                                                    target="_blank"
                                                                    class="text-indigo-600 hover:underline text-xs">
                                                                        Download
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div class="mt-1 text-[11px] text-gray-500 flex justify-end">
                                                    <span>{{ $mensagem->created_at->format('H:i') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-gray-500 text-sm italic">Nenhuma mensagem ainda.</p>
                    @endforelse
                @else
                    <p class="text-center text-gray-500 text-sm italic">Selecione uma conversa para ver as mensagens.</p>
                    <p class="text-center text-gray-500 text-sm italic">Caso tenha alguma dúvida mande, em qualquer dm ou grupo, @sistema e mostrará todas as ajudas! </p>
                @endif
            </div>
           
            <!-- Caixa de envio -->
            <div class="bg-gray-100 p-4 border-t border-gray-200">
                @if ($conversaAtiva)
                    <form wire:submit="enviarMensagem" class="flex items-center space-x-3">
                        @csrf
                        <label class="cursor-pointer flex items-center justify-center
                                    w-9 h-9 rounded-md bg-white border border-gray-300
                                    hover:bg-gray-50">
                            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828L18 9.828a4 4 0 10-5.656-5.656L6.343 10.172a6 6 0 108.485 8.485L20 13"/>
                            </svg>
                            <input type="file"
                                class="hidden"
                                multiple
                                wire:model="uploads">
                        </label>
                        <input type="text"
                               wire:model.defer="novaMensagem"
                               placeholder="Escreva a sua mensagem..."
                               class="flex-grow border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                               autocomplete="off">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="uploads,enviarMensagem"
                                class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50
                                    text-white font-bold py-2 px-4 rounded text-sm">
                            <span wire:loading.remove wire:target="enviarMensagem">
                                Enviar
                            </span>
                            <span wire:loading wire:target="enviarMensagem">
                                A enviar…
                            </span>
                        </button>
                    </form>
                    @if (!empty($uploads))
                        <div class="mt-2 bg-white border border-gray-200 rounded-md p-2">
                            <p class="text-xs text-gray-600 font-semibold mb-1">
                                Ficheiros selecionados ({{ count($uploads) }}):
                            </p>
                            <ul class="space-y-1">
                                @foreach ($uploads as $i => $file)
                                    <li class="flex items-center justify-between text-xs text-gray-700">
                                        <span class="truncate">
                                            📎 {{ $file->getClientOriginalName() }}
                                            <span class="text-gray-400">
                                                ({{ number_format($file->getSize() / 1024 / 1024, 2) }} MB)
                                            </span>
                                        </span>
                                        <button type="button"
                                                wire:click="removerUpload({{ $i }})"
                                                class="text-red-500 hover:text-red-700 text-xs ml-2">
                                            Remover
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @error('uploads')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    @error('uploads.*')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                @else
                    <p class="text-center text-gray-500 text-sm italic">Selecione uma conversa.</p>
                @endif
            </div>
        </section>
        @if ($mostrarMembrosGrupo && $membrosGrupo)
            <aside class="w-64 bg-gray-50 border-l border-gray-200 p-4 overflow-y-auto">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    Membros do Grupo ({{ $membrosGrupo->count() }})
                </h3>
                <ul class="space-y-2">
                    @foreach ($membrosGrupo as $membro)
                        <li class="flex items-center space-x-2">
                            @if ($membro->avatar_url_public)
                                <img src="{{ $membro->avatar_url_public }}"
                                    class="w-7 h-7 rounded-full object-cover">
                            @else
                                <div class="w-7 h-7 rounded-full bg-gray-400 text-white flex items-center justify-center text-xs">
                                    {{ strtoupper(mb_substr($membro->nome, 0, 2)) }}
                                </div>
                            @endif
                            <span class="text-sm text-gray-800 flex-1">
                                {{ $membro->nome }}
                            </span>
                            @if ($membro->pivot->is_admin ?? false)
                                <span class="text-xs bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded">
                                    Admin
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </aside>
        @endif
    </div>
</div>
<script>
    (function () {
        let currentSubscription = null;
        function subscribeToConversa(conversaId) {
            if (!conversaId || !window.Echo) return;
            if (currentSubscription) {
                try { window.Echo.leave(currentSubscription); } catch (e) {}
                currentSubscription = null;
            }
            currentSubscription = `conversa.${conversaId}`;
            window.Echo.private(`conversa.${conversaId}`)
                .listen('.mensagem.nova', (e) => {
                    if (window.Livewire) {
                        window.Livewire.dispatch('mensagemRecebidaRealtime', e);
                    }
                });
        }

        document.addEventListener('livewire:init', () => {
            Livewire.on('conversa-selecionada', (data) => {
                subscribeToConversa(data.conversaId);
            });

            Livewire.on('scroll-chat-para-fundo', () => {
                const container = document.querySelector('.overflow-y-auto');
                if (container) container.scrollTop = container.scrollHeight;
            });

            Livewire.on('scroll-to-message', ({ messageId }) => {
                const el = document.getElementById('msg-' + messageId);
                if (!el) return;

                el.scrollIntoView({ block: 'center', behavior: 'smooth' });

                el.classList.add('bg-yellow-50');
                setTimeout(() => el.classList.remove('bg-yellow-50'), 2000);

                // Limpa o ring amarelo após 3 segundos
                setTimeout(() => {
                    if (window.Livewire) {
                        window.Livewire.dispatch('clearHighlight');
                    }
                }, 3000);
            });
        });
    })();
</script>