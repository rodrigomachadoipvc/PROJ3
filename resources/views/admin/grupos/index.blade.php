<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Grupos de Chat') }}
        </h2>
    </x-slot>

    {{-- [NOVO] Carregar todos os utilizadores para o modal de adição --}}
    @php
        // Carrega todos os utilizadores para a lista de "Adicionar Membros" no modal
        $allUsers = \App\Models\User::orderBy('nome')->get();
    @endphp

    <div x-data="{ 
        // Variáveis de Eliminar Grupo E Membro (UNIFICADAS NO deleteUrl)
        showDeleteModal: false, 
        deleteUrl: '', 
        // Variáveis de Gestão de Membros
        showMembrosModal: false, 
        grupoId: null, 
        grupoNome: '', 
        // Variáveis de Eliminar Membro (NOVAS)
        showMemberDeleteModal: false, 
        memberName: '',
    }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <!-- Mensagens Flash -->
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                         @if (session('error'))
                            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                               {{ session('error') }}
                            </div>
                        @endif

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold">{{ __('Grupos Criados') }} ({{ $grupos->total() ?? 0 }})</h3>
                            <a href="{{ route('admin.grupos.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150">
                                {{ __('Criar Novo Grupo') }}
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome do Grupo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criador</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membros</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($grupos as $grupo)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $grupo->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $grupo->nome }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $grupo->descricao ?? '--' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $grupo->criador->nome ?? __('Desconhecido') }}</td>
                                            
                                            {{-- Mostra a contagem real de membros --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $grupo->membros->count() }} {{ __('Membros') }} 
                                            </td>

                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end space-x-3">
                                                
                                                {{-- Botão de Gerir Membros --}}
                                                <button 
                                                    @click="showMembrosModal = true; grupoId = {{ $grupo->id }}; grupoNome = '{{ $grupo->nome }}'" 
                                                    class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                    {{ __('Gerir Membros') }}
                                                </button>

                                                <a href="{{ route('admin.grupos.edit', $grupo) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Editar') }}</a>
                                                
                                                <!-- Botão Eliminar Grupo (Chama o Alpine.js) -->
                                                <button 
                                                    {{-- O deleteUrl é usado por ambos os modais de eliminação (Grupo e Membro) --}}
                                                    @click="showDeleteModal = true; deleteUrl = '{{ route('admin.grupos.destroy', $grupo) }}'" 
                                                    type="button" 
                                                    class="text-red-600 hover:text-red-900">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                {{ __('Nenhum grupo encontrado.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $grupos->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Confirmação (Eliminar Grupo) -->
        <div x-show="showDeleteModal" x-on:keydown.escape.window="showDeleteModal = false" style="display: none;">
             <x-confirm-modal 
                name="group-delete" 
                title="Atenção: Eliminar Grupo" 
                content="Tem certeza que deseja eliminar este grupo? Todas as mensagens associadas poderão ser perdidas." 
                method="DELETE"
                x-bind:action-route="deleteUrl"> <!-- Liga à variável deleteUrl do Alpine -->
            </x-confirm-modal>
        </div>

        {{-- [NOVO] Modal de Confirmação (Remover Membro do Grupo) --}}
        {{-- CORREÇÃO: Usa showMemberDeleteModal, a variável correta. Note que este modal também usa deleteUrl para o formulário --}}
        <div x-show="showMemberDeleteModal" x-on:keydown.escape.window="showMemberDeleteModal = false" style="display: none;">
             <x-confirm-modal 
                name="member-delete" 
                title="Remover Membro" 
                content="" 
                x-bind:content="'Tem a certeza que deseja remover o membro ' + memberName + ' do grupo?'"
                method="DELETE"
                x-bind:action-route="deleteUrl"> <!-- Usa deleteUrl para o formulário, que será definido pelo botão do modal de membros -->
            </x-confirm-modal>
        </div>

        {{-- Incluir o Modal de Gestão de Membros --}}
        @include('admin.grupos.membros_modal', ['grupo' => $grupo ?? null, 'users' => $allUsers])

    </div> <!-- Fim do escopo x-data -->

</x-app-layout>