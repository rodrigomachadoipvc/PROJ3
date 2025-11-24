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

            <div class="text-sm text-gray-500">
                {{ Auth::user()->nome }} ({{ Auth::user()->tipoUtilizador->nome ?? 'N/A' }})
            </div>
        </div>
    </header>


    <!-- Corpo -->
    <div class="flex flex-grow overflow-hidden">

        <!-- Sidebar -->
        <aside class="w-1/4 bg-gray-100 border-r border-gray-200 overflow-y-auto p-4">

            <!-- DM Section -->
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Conversas (DM)</h3>

            @forelse ($conversas->where('tipo','dm') as $conversa)
                @php
                    $outro = $conversa->participantes->where('id','!=',Auth::id())->first();
                @endphp

                <div wire:click="selecionarConversa({{ $conversa->id }})"
                    class="block p-3 rounded-lg cursor-pointer shadow-sm mb-2 border
                        {{ $conversaAtiva && $conversaAtiva->id === $conversa->id ? 
                            'bg-indigo-100 border-indigo-300' : 'bg-white hover:bg-gray-50 border-gray-200' }}">

                    <p class="font-semibold text-sm text-gray-800">
                        {{ $outro?->nome ?? 'Utilizador' }}
                    </p>

                    @if ($conversa->ultimaMensagem)
                        <p class="text-xs text-gray-600 truncate mt-1">
                            <strong>{{ $conversa->ultimaMensagem->emissor_id == Auth::id() ? 'Você' : $conversa->ultimaMensagem->emissor?->nome }}:</strong>
                            {{ $conversa->ultimaMensagem->conteudo ?? 'Anexo' }}
                        </p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">Sem conversas DM.</p>
            @endforelse


            <!-- Group Section -->
            <h3 class="text-lg mt-6 font-semibold mb-2 text-gray-700">Grupos</h3>

            @forelse ($conversas->where('tipo','grupo') as $conversa)
                <div wire:click="selecionarConversa({{ $conversa->id }})"
                    class="block p-3 rounded-lg cursor-pointer shadow-sm mb-2 border
                        {{ $conversaAtiva && $conversaAtiva->id === $conversa->id ? 
                            'bg-indigo-100 border-indigo-300' : 'bg-white hover:bg-gray-50 border-gray-200' }}">

                    <p class="font-semibold text-sm text-gray-800">
                        {{ $conversa->nome ?? $conversa->grupo?->nome ?? 'Grupo' }}
                    </p>

                    @if ($conversa->ultimaMensagem)
                        <p class="text-xs text-gray-600 truncate mt-1">
                            <strong>{{ $conversa->ultimaMensagem->emissor_id == Auth::id() ? 'Você' : $conversa->ultimaMensagem->emissor?->nome }}:</strong>
                            {{ $conversa->ultimaMensagem->conteudo ?? 'Anexo' }}
                        </p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">Sem grupos.</p>
            @endforelse


            <!-- Iniciar DM -->
            <div class="mt-6 pt-4 border-t border-gray-300">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Iniciar Conversa (DM)</h3>

                @forelse ($outrosUtilizadores as $outro)
                    <div wire:click="iniciarConversaDM({{ $outro->id }})"
                        class="block p-3 rounded-lg cursor-pointer bg-white hover:bg-gray-50 shadow-sm border border-gray-200 mb-1">
                        <strong>{{ $outro->nome }}</strong>
                        <br>
                        <span class="text-xs text-gray-500">{{ $outro->tipoUtilizador->nome ?? 'N/A' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Sem utilizadores.</p>
                @endforelse
            </div>

        </aside>


        <!-- Área das mensagens -->
        <main class="w-3/4 flex flex-col">

            <div class="flex-grow bg-white p-6 overflow-y-auto space-y-4">

                @if ($conversaAtiva)
                    @forelse ($mensagens as $mensagem)

                        <div class="flex {{ $mensagem->emissor_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="{{ $mensagem->emissor_id == Auth::id() ? 
                                'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} 
                                rounded-lg p-3 max-w-lg shadow">

                                <p class="text-sm">{{ $mensagem->conteudo }}</p>

                                <span class="text-xs block mt-1 text-right
                                    {{ $mensagem->emissor_id == Auth::id() ? 'text-blue-100' : 'text-gray-600' }}">
                                    {{ $mensagem->emissor?->nome }} - {{ $mensagem->created_at->format('H:i') }}
                                </span>

                            </div>
                        </div>

                    @empty
                        <p class="text-center text-gray-500 italic">Sem mensagens.</p>
                    @endforelse

                @else
                    <p class="text-center text-gray-500 italic">Selecione uma conversa.</p>
                @endif

            </div>


            <!-- Caixa de envio -->
            <div class="bg-gray-100 p-4 border-t border-gray-200">

                @if ($conversaAtiva)

                    <form wire:submit="enviarMensagem" class="flex items-center space-x-3">
                        <input type="text" 
                               wire:model.defer="novaMensagem"
                               x-on:limpar-input-chat.window="$el.value=''"
                               class="flex-grow border-gray-300 rounded-md text-sm"
                               placeholder="Escreva a sua mensagem…">

                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Enviar
                        </button>
                    </form>

                @else
                    <p class="text-center text-gray-500 italic">Selecione uma conversa para enviar mensagens.</p>
                @endif

            </div>

        </main>

    </div>

</div>
