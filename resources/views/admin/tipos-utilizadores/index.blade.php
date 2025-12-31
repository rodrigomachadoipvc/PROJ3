<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de tipos de utilizador e permissões
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        {{-- Cabeçalho --}}
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Tipos de utilizador
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Total: {{ $tipos->total() ?? 0 }}
                                </p>
                            </div>

                            <a
                                href="{{ route('admin.tipos-utilizadores.create') }}"
                                class="bg-indigo-600 hover:bg-indigo-700
                                       text-white font-semibold
                                       py-2 px-4 rounded transition"
                            >
                                Criar novo tipo
                            </a>
                        </div>

                        {{-- Tabela --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Nome
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Descrição
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Permissões
                                        </th>
                                        <th class="px-6 py-3 text-right"></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($tipos as $tipo)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $tipo->id }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium">
                                                        {{ $tipo->nome }}
                                                    </span>

                                                    @if ($tipo->nome === 'Administrador')
                                                        <span
                                                            class="inline-flex items-center
                                                                   px-2 py-0.5 rounded-full
                                                                   text-xs font-semibold
                                                                   bg-red-100 text-red-800"
                                                        >
                                                            Base
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-600 max-w-sm truncate">
                                                {{ $tipo->descricao ?: '—' }}
                                            </td>

                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center
                                                           px-2.5 py-0.5 rounded-full
                                                           text-xs font-semibold
                                                           bg-green-100 text-green-800"
                                                >
                                                    {{ $tipo->permissoes->count() }} permissões
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                                <a
                                                    href="{{ route('admin.tipos-utilizadores.edit', $tipo) }}"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Editar
                                                </a>

                                                <button
                                                    type="button"
                                                    @click="
                                                        showDeleteModal = true;
                                                        deleteUrl = '{{ route('admin.tipos-utilizadores.destroy', $tipo->id) }}';
                                                    "
                                                    class="text-red-600 hover:text-red-900"
                                                    x-bind:disabled="{{ $tipo->nome === 'Administrador' ? 'true' : 'false' }}"
                                                >
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                                Nenhum tipo de utilizador encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginação --}}
                        <div class="mt-4">
                            {{ $tipos->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de confirmação --}}
        <div
            x-show="showDeleteModal"
            x-on:keydown.escape.window="showDeleteModal = false"
            style="display: none;"
        >
            <x-confirm-modal
                name="tipo-delete"
                title="Eliminar tipo de utilizador"
                content="Tem a certeza que deseja eliminar este tipo de utilizador? Esta ação é irreversível e irá desassociar todos os utilizadores."
                method="DELETE"
            />
        </div>
    </div>
</x-app-layout>