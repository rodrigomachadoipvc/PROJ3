<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Tipos de Utilizador e Permissões') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold">{{ __('Tipos de Utilizador') }} ({{ $tipos->total() ?? 0 }})</h3>
                            <a href="{{ route('admin.tipos-utilizadores.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150">
                                {{ __('Adicionar Novo Tipo') }}
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('ID') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Nome') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Descrição') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Permissões Associadas') }}
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">{{ __('Ações') }}</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($tipos as $tipo)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $tipo->id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $tipo->nome }}
                                                @if ($tipo->nome === 'Administrador')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ __('Base') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                                {{ $tipo->descricao }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $tipo->permissoes->count() }} {{ __('Permissões') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.tipos-utilizadores.edit', $tipo) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Editar') }}</a>
                                                
                                                <button 
                                                    @click="showDeleteModal = true; deleteUrl = '{{ route('admin.tipos-utilizadores.destroy', $tipo->id) }}'"
                                                    type="button"
                                                    class="text-red-600 hover:text-red-900"
                                                    x-bind:disabled="{{ $tipo->nome === 'Administrador' ? 'true' : 'false' }}">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                {{ __('Nenhum Tipo de Utilizador encontrado.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $tipos->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div x-show="showDeleteModal" x-on:keydown.escape.window="showDeleteModal = false" style="display: none;">
             <x-confirm-modal 
                name="tipo-delete" 
                title="Atenção: Eliminar Tipo de Utilizador" 
                content="Tem certeza que deseja eliminar este Tipo de Utilizador? Esta ação é irreversível e irá desassociar todos os utilizadores desta função." 
                method="DELETE"> 
                {{-- Removemos a passagem de :action-route --}}
            </x-confirm-modal>
        </div>

    </div>
</x-app-layout>