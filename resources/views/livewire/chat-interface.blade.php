<div class="h-screen flex flex-col">

    <!-- Cabeçalho -->
    <header class="bg-white shadow sticky top-0 z-10">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if ($conversaAtiva)
                    @if ($conversaAtiva->tipo === 'dm')
                        {{ $conversaAtiva->participantes->where('id', '!=', Auth::id())->first()?->nome ?? 'Conversa DM' }}
                    @else
                        {{ $conversaAtiva->nome ?? $conversaAtiva->grupo?->nome ?? 'Grupo' }}
                    @endif
                @else
                    Communico Chat
                @endif
            </h2>
        </div>
    </header>

    <!-- Corpo -->
    <div class="flex flex-grow overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-1/4 bg-gray-100 border-r border-gray-200 overflow-y-auto p-4">

            {{-- Conversas DM --}}
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Conversas (DM)</h3>

            @php
                $dmConversas = $conversas->where('tipo', 'dm');
                $grupoConversas = $conversas->where('tipo', 'grupo');
            @endphp

            @forelse ($dmConversas as $conversa)
                @php
                    $outro = $conversa->participantes->where('id', '!=', Auth::id())->first();
                    $ultima = $conversa->ultimaMensagem ?? null;
                @endphp

                <div wire:click="selecionarConversa({{ $conversa->id }})"
                     class="block p-3 rounded-lg cursor-pointer shadow-sm mb-2 border transition
                        {{ $conversaAtiva && $conversaAtiva->id === $conversa->id
                            ? 'bg-indigo-100 border-indigo-300'
                            : 'bg-white hover:bg-gray-50 border-gray-200' }}">

                    <p class="font-semibold text-sm text-gray-800 truncate">
                        {{ $outro?->nome ?? 'Utilizador' }}
                    </p>

                    @if ($ultima)
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
                <p class="text-sm text-gray-500">Sem conversas DM.</p>
            @endforelse

            {{-- Grupos --}}
            <h3 class="text-lg mt-6 font-semibold mb-2 text-gray-700">Grupos</h3>

            @forelse ($grupoConversas as $conversa)
                @php
                    $ultima = $conversa->ultimaMensagem ?? null;
                    $nomeGrupo = $conversa->nome ?? $conversa->grupo?->nome ?? 'Grupo';
                @endphp

                <div wire:click="selecionarConversa({{ $conversa->id }})"
                     class="block p-3 rounded-lg cursor-pointer shadow-sm mb-2 border transition
                        {{ $conversaAtiva && $conversaAtiva->id === $conversa->id
                            ? 'bg-indigo-100 border-indigo-300'
                            : 'bg-white hover:bg-gray-50 border-gray-200' }}">

                    <p class="font-semibold text-sm text-gray-800 truncate">
                        {{ $nomeGrupo }}
                    </p>

                    @if ($ultima)
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
                <p class="text-sm text-gray-500">Sem grupos.</p>
            @endforelse

            {{-- Iniciar Conversa --}}
            <div class="mt-6 pt-4 border-t border-gray-300">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Iniciar Conversa (DM)</h3>

                @forelse ($outrosUtilizadores as $outro)
                    <div wire:click="iniciarConversaDM({{ $outro->id }})"
                         class="block p-3 rounded-lg cursor-pointer bg-white hover:bg-gray-50 shadow-sm border border-gray-200 mb-1 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ $outro->nome }}</p>
                            <span class="text-xs text-gray-500">
                                {{ $outro->tipoUtilizador->nome ?? 'N/A' }}
                            </span>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sem outros utilizadores.</p>
                @endforelse
            </div>
        </aside>

        <!-- Área das Mensagens -->
        <main class="w-3/4 flex flex-col">
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

                        @if ($isNewGroup)

                            {{-- Mensagem própria --}}
                            @if ($isOwn)
                                <div class="flex items-start justify-end mt-4 space-x-3">
                                    <div class="max-w-lg">
                                        <div class="bg-blue-100 text-gray-900 rounded-2xl px-4 py-2 shadow-sm">
                                            @if (!empty($mensagem->conteudo))
                                                <p class="text-sm break-words">
                                                    {{ $mensagem->conteudo }}
                                                </p>
                                            @endif
                                            {{-- Anexos --}}
                                            @if ($mensagem->anexos && $mensagem->anexos->count())
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($mensagem->anexos as $anexo)

                                                        @php
                                                            $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                            $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                        @endphp

                                                        @if ($isImage)
                                                            {{-- Preview de imagem --}}
                                                            <a href="{{ route('anexos.ver', $anexo) }}"
                                                            target="_blank"
                                                            class="inline-block">
                                                                <img src="{{ route('anexos.ver', $anexo) }}"
                                                                    alt="{{ $anexo->nome_original }}"
                                                                    class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                            </a>

                                                        @else
                                                            {{-- Documento / ficheiro --}}
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

                            {{-- Mensagem de outro utilizador --}}
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
                                            {{-- Anexos --}}
                                            @if ($mensagem->anexos && $mensagem->anexos->count())
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($mensagem->anexos as $anexo)

                                                        @php
                                                            $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                            $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                        @endphp

                                                        @if ($isImage)
                                                            {{-- Preview de imagem --}}
                                                            <a href="{{ route('anexos.ver', $anexo) }}"
                                                            target="_blank"
                                                            class="inline-block">
                                                                <img src="{{ route('anexos.ver', $anexo) }}"
                                                                    alt="{{ $anexo->nome_original }}"
                                                                    class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                            </a>

                                                        @else
                                                            {{-- Documento / ficheiro --}}
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
                            {{-- Continuação do grupo de mensagens --}}
                            @if ($isOwn)
                                <div class="flex justify-end mt-1">
                                    <div class="max-w-lg mr-10">
                                        <div class="bg-blue-100 text-gray-900 rounded-2xl px-4 py-2 shadow-sm">
                                            @if (!empty($mensagem->conteudo))
                                                <p class="text-sm break-words">
                                                    {{ $mensagem->conteudo }}
                                                </p>
                                            @endif
                                            {{-- Anexos --}}
                                            @if ($mensagem->anexos && $mensagem->anexos->count())
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($mensagem->anexos as $anexo)

                                                        @php
                                                            $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                            $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                        @endphp

                                                        @if ($isImage)
                                                            {{-- Preview de imagem --}}
                                                            <a href="{{ route('anexos.ver', $anexo) }}"
                                                            target="_blank"
                                                            class="inline-block">
                                                                <img src="{{ route('anexos.ver', $anexo) }}"
                                                                    alt="{{ $anexo->nome_original }}"
                                                                    class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                            </a>

                                                        @else
                                                            {{-- Documento / ficheiro --}}
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
                                            {{-- Anexos --}}
                                            @if ($mensagem->anexos && $mensagem->anexos->count())
                                                <div class="mt-2 space-y-2">
                                                    @foreach ($mensagem->anexos as $anexo)

                                                        @php
                                                            $isImage = str_starts_with($anexo->mime_type, 'image/');
                                                            $sizeMb = number_format($anexo->tamanho_bytes / 1024 / 1024, 2);
                                                        @endphp

                                                        @if ($isImage)
                                                            {{-- Preview de imagem --}}
                                                            <a href="{{ route('anexos.ver', $anexo) }}"
                                                            target="_blank"
                                                            class="inline-block">
                                                                <img src="{{ route('anexos.ver', $anexo) }}"
                                                                    alt="{{ $anexo->nome_original }}"
                                                                    class="max-w-xs rounded-lg border border-gray-200 shadow-sm hover:opacity-90">
                                                            </a>

                                                        @else
                                                            {{-- Documento / ficheiro --}}
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
                        <!-- Botão de anexos -->
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
        </main>
    </div>
</div>
<script>
    (function () {
        let currentSubscription = null;

        function subscribeToConversa(conversaId) {
            if (!conversaId || !window.Echo) return;

            // Sair do canal anterior
            if (currentSubscription) {
                try { window.Echo.leave(currentSubscription); } catch (e) {}
                currentSubscription = null;
            }

            const channelName = `private-conversa.${conversaId}`;
            currentSubscription = `conversa.${conversaId}`;

            window.Echo.private(`conversa.${conversaId}`)
                .listen('.mensagem.nova', (e) => {
                    // Envia o payload para Livewire atualizar sem refresh
                    if (window.Livewire) {
                        window.Livewire.dispatch('mensagemRecebidaRealtime', e);
                    }
                });
        }

        // Quando o Livewire muda a conversa ativa
        document.addEventListener('livewire:init', () => {
            Livewire.on('conversa-selecionada', (data) => {
                subscribeToConversa(data.conversaId);
            });

            Livewire.on('scroll-chat-para-fundo', () => {
                const container = document.querySelector('main .overflow-y-auto');
                if (container) container.scrollTop = container.scrollHeight;
            });

            Livewire.on('limpar-input-chat', () => {
                // opcional: o wire:model já limpa, mas mantém UX suave
            });
        });
    })();
</script>
