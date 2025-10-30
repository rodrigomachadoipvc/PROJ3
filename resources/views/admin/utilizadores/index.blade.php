<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Utilizadores') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
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
                            <h3 class="text-xl font-bold">{{ __('Utilizadores Registados') }} ({{ $utilizadores->total() ?? 0 }})</h3>
                            <a href="{{ route('admin.utilizadores.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150">
                                {{ __('Adicionar Novo Utilizador') }}
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo (Role)</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($utilizadores as $utilizador)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $utilizador->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $utilizador->nome }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $utilizador->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $utilizador->tipoUtilizador->nome ?? __('Sem Tipo') }}
                                                @if ($utilizador->tipoUtilizador?->nome === 'Administrador')
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Admin</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $utilizador->status === 'ativo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($utilizador->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.utilizadores.edit', $utilizador) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Editar') }}</a>

                                                <button 
                                                    @click="showDeleteModal = true; deleteUrl = '{{ route('admin.utilizadores.destroy', $utilizador) }}'" 
                                                    type="button" 
                                                    class="text-red-600 hover:text-red-900"
                                                    x-bind:disabled="{{ $utilizador->id === auth()->id() ? 'true' : 'false' }}">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                {{ __('Nenhum utilizador encontrado.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $utilizadores->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Confirmação (AGORA DENTRO DO ESCOPO x-data) -->
         <div x-show="showDeleteModal" x-on:keydown.escape.window="showDeleteModal = false" style="display: none;">
             <x-confirm-modal 
                name="user-delete" 
                title="Atenção: Eliminar Utilizador" 
                content="Tem certeza que deseja eliminar este utilizador? Esta ação não pode ser revertida." 
                method="DELETE"
                x-bind:action-route="deleteUrl"> <!-- Liga à variável deleteUrl do Alpine -->
            </x-confirm-modal>
        </div>

    </div> 
</x-app-layout>