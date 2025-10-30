<x-app-layout>
    {{-- Remover o cabeçalho padrão para ter ecrã completo --}}
    {{-- <x-slot name="header"> ... </x-slot> --}}

    <div class="h-screen flex flex-col"> {{-- Garante que ocupa a altura toda --}}
        
        <!-- Cabeçalho Simples do Chat (Opcional) -->
        <header class="bg-white shadow sticky top-0 z-10">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Communico Chat') }}
                </h2>
                {{-- Aqui pode ir info do utilizador ou botões --}}
                <div class="text-sm text-gray-500">
                    {{ Auth::user()->nome }} ({{ Auth::user()->tipoUtilizador->nome ?? 'N/A' }})
                </div>
            </div>
        </header>

        <!-- Corpo Principal do Chat (Layout Dividido) -->
        <div class="flex flex-grow overflow-hidden"> {{-- Flex-grow para ocupar espaço restante --}}
            
            <!-- Barra Lateral Esquerda: Lista de Conversas -->
            <aside class="w-1/4 bg-gray-100 border-r border-gray-200 overflow-y-auto p-4">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Conversas</h3>
                
                <div class="space-y-2">
                    
                    @forelse ($conversas as $conversa)
                        <div class="p-3 rounded-lg cursor-pointer transition duration-150 ease-in-out {{-- Lógica para conversa ativa --}} bg-white hover:bg-gray-50 shadow-sm border border-gray-200"
                             wire:click="selecionarConversa({{ $conversa->id }})"> {{-- Ação para Livewire (futuro) --}}
                            
                            <p class="font-semibold text-sm text-gray-800">
                                {{-- Lógica para nome da conversa: Se for DM, mostra o nome do outro participante --}}
                                @if ($conversa->tipo === 'dm' && $conversa->participantes->count() > 1)
                                    {{ $conversa->participantes->where('id', '!=', Auth::id())->first()?->nome ?? 'Conversa DM' }}
                                @else
                                    {{ $conversa->nome ?? $conversa->grupo?->nome ?? 'Conversa #' . $conversa->id }}
                                @endif
                            </p>
                            
                            {{-- Última mensagem (se existir) --}}
                            @if ($conversa->ultimaMensagem)
                                <p class="text-xs text-gray-600 truncate mt-1">
                                    <strong>{{ $conversa->ultimaMensagem->emissor_id == Auth::id() ? 'Você' : $conversa->ultimaMensagem->emissor?->nome }}:</strong> 
                                    {{ $conversa->ultimaMensagem->conteudo ?? 'Anexo' }}
                                </p>
                                <span class="text-xs text-gray-400 float-right">{{ $conversa->ultimaMensagem->created_at->diffForHumans() }}</span>
                            @else
                                <p class="text-xs text-gray-400 italic mt-1">{{ __('Nenhuma mensagem ainda.') }}</p>
                            @endif
                            <div class="clear-both"></div> {{-- Clear float --}}
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center">{{ __('Não participa em nenhuma conversa ainda.') }}</p>
                    @endforelse
                    
                </div>

                <div class="mt-6 pt-4 border-t border-gray-300">
                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Iniciar Conversa (DM)</h3>
                    <div class="space-y-2">
                        @forelse ($outrosUtilizadores as $outroUtilizador)
                            <div class="p-3 rounded-lg cursor-pointer transition duration-150 ease-in-out bg-white hover:bg-gray-50 shadow-sm border border-gray-200 flex items-center justify-between"
                                 <a href="{{ route('chat.iniciar-dm', $outroUtilizador->id) }}" 
                               class="block p-3 rounded-lg cursor-pointer transition duration-150 ease-in-out bg-white hover:bg-gray-50 shadow-sm border border-gray-200 flex items-center justify-between">
                                
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">{{ $outroUtilizador->nome }}</p>
                                    <span class="text-xs text-gray-500">{{ $outroUtilizador->tipoUtilizador->nome ?? 'N/A' }}</span>
                                </div>
                                
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            </a>
                                
                                {{-- Ícone de Mensagem (opcional) --}}
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
                
                <!-- Área de Mensagens (Ocupa espaço vertical) -->
                <div class="flex-grow bg-white p-6 overflow-y-auto space-y-4">
                    {{-- Placeholder: Mensagens --}}
                    <div class="flex justify-start">
                        <div class="bg-gray-200 rounded-lg p-3 max-w-lg">
                            <p class="text-sm">Olá equipa, bom dia!</p>
                            <span class="text-xs text-gray-500 block mt-1 text-right">Admin - 09:00</span>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <div class="bg-blue-500 text-white rounded-lg p-3 max-w-lg">
                            <p class="text-sm">Bom dia! Tudo pronto para a reunião.</p>
                             <span class="text-xs text-blue-100 block mt-1 text-right">Você - 09:01</span>
                        </div>
                    </div>
                     <div class="flex justify-start">
                        <div class="bg-gray-200 rounded-lg p-3 max-w-lg">
                            <p class="text-sm">Excelente!</p>
                            <span class="text-xs text-gray-500 block mt-1 text-right">Admin - 09:02</span>
                        </div>
                    </div>
                    {{-- Fim do Placeholder --}}
                </div>

                <!-- Caixa de Envio de Mensagem (Fixa no fundo) -->
                <div class="bg-gray-100 p-4 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                         {{-- Botão de Anexo (Placeholder) --}}
                        <button class="text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        </button>
                        {{-- Input de Texto --}}
                        <input type="text" placeholder="Escreva a sua mensagem..." class="flex-grow border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                        {{-- Botão de Envio --}}
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 text-sm">
                            Enviar
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>