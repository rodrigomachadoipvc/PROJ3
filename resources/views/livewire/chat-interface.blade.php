<div class="h-screen flex flex-col">
        
    <!-- Cabeçalho Dinâmico do Chat -->
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

    <!-- Corpo Principal do Chat (Layout Dividido) -->
    <div class="flex flex-grow overflow-hidden">
        
        <!-- Barra Lateral Esquerda: Lista de Conversas -->
        <aside class="w-1/4 bg-gray-100 border-r border-gray-200 overflow-y-auto p-4">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Conversas</h3>
            
            <div class="space-y-2">
                @forelse ($conversas as $conversa)
                    @php
                        $nomeConversa = 'Grupo ' . $conversa->id;

                        if ($conversa->tipo === 'dm' && $conversa->participantes->count() > 1) {
                            $outroUtilizador = $conversa->participantes->where('id', '!=', Auth::id())->first();
                            if ($outroUtilizador) {
                                $nomeConversa = $outroUtilizador->nome;
                            }
                        } else {
                            $nomeConversa = $conversa->nome ?? $conversa->grupo?->nome ?? 'Grupo';
                        }
                    @endphp

                    {{-- MUDANÇA: Substituído <a> por <div wire:click> --}}
                    <div wire:click="selecionarConversa({{ $conversa->id }})"
                         class="block p-3 rounded-lg cursor-pointer transition duration-150 ease-in-out shadow-sm border
                                {{ $conversaAtiva && $conversaAtiva->id === $conversa->id ? 'bg-indigo-100 border-indigo-300' : 'bg-white hover:bg-gray-50 border-gray-200' }}">
                        
                        <p class="font-semibold text-sm text-gray-800">{{ $nomeConversa }}</p>
                        
                        @if ($conversa->ultimaMensagem)
                            <p class="text-xs text-gray-600 truncate mt-1">
                                <strong>{{ $conversa->ultimaMensagem->emissor_id == Auth::id() ? 'Você' : $conversa->ultimaMensagem->emissor?->nome }}:</strong> 
                                {{ $conversa->ultimaMensagem->conteudo ?? 'Anexo' }}
                            </p>
                            <span class="text-xs text-gray-400 float-right">{{ $conversa->ultimaMensagem->created_at->diffForHumans() }}</span>
                        @else
                            <p class="text-xs text-gray-400 italic mt-1">{{ __('Nenhuma mensagem ainda.') }}</p>
                        @endif
                        <div class="clear-both"></div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center">{{ __('Não participa em nenhuma conversa ainda.') }}</p>
                @endforelse
            </div>

            {{-- Utilizadores para DM --}}
            <div class="mt-6 pt-4 border-t border-gray-300">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Iniciar Conversa (DM)</h3>
                <div class="space-y-2">
                    @forelse ($outrosUtilizadores as $outroUtilizador)
                        {{-- MUDANÇA: Substituído <a> por <div wire:click> --}}
                        <div wire:click="iniciarConversaDM({{ $outroUtilizador->id }})" 
                             class="block p-3 rounded-lg cursor-pointer transition duration-150 ease-in-out bg-white hover:bg-gray-50 shadow-sm border border-gray-200 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-sm text-gray-800">{{ $outroUtilizador->nome }}</p>
                                <span class="text-xs text-gray-500">{{ $outroUtilizador->tipoUtilizador->nome ?? 'N/A' }}</span>
                            </div>
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center">{{ __('Não existem outros utilizadores no sistema.') }}</p>
                    @endforelse
                </div>
            </div>
        </aside>

        <!-- Área Principal Direita: Mensagens e Caixa de Envio -->
        <main class="w-3/4 flex flex-col">
            
            <!-- Área de Mensagens -->
            <div class="flex-grow bg-white p-6 overflow-y-auto space-y-4">
                
                @if ($conversaAtiva)
                    @forelse ($mensagens as $mensagem)
                        <div class="flex {{ $mensagem->emissor_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="{{ $mensagem->emissor_id == Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} rounded-lg p-3 max-w-lg shadow">
                                <p class="text-sm">{{ $mensagem->conteudo ?? 'Anexo(s)' }}</p>
                                <span class="text-xs {{ $mensagem->emissor_id == Auth::id() ? 'text-blue-100' : 'text-gray-500' }} block mt-1 text-right">
                                    {{ $mensagem->emissor->nome }} - {{ $mensagem->created_at->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 text-sm italic">{{ __('Nenhuma mensagem nesta conversa ainda. Envie a primeira!') }}</p>
                    @endforelse
                @else
                    <p class="text-center text-gray-500 text-sm italic">{{ __('Selecione uma conversa ou inicie uma nova para ver as mensagens.') }}</p>
                @endif
                
            </div>

            <!-- Caixa de Envio de Mensagem (LIGADA AO LIVEWIRE) -->
            <div class="bg-gray-100 p-4 border-t border-gray-200">
                @if ($conversaAtiva)
                    
                    <form wire:submit="enviarMensagem" class="flex items-center space-x-3">
                        @csrf
                        <button type="button" class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </button>
                        
                        <input type="text" 
                               wire:model.defer="novaMensagem"
                               x-on:limpar-input-chat.window="$el.value = ''"
                               placeholder="Escreva a sua mensagem..." 
                               class="flex-grow border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                               autocomplete="off">
                        
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 text-sm">
                            Enviar
                        </button>
                    </form>
                    <x-input-error :messages="$errors->get('novaMensagem')" class="mt-2" />

                @else
                     <p class="text-center text-gray-500 text-sm italic">{{ __('Selecione uma conversa para enviar mensagens.') }}</p>
                @endif
            </div>
        </main>
    </div>
</div>