<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de utilizadores
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        {{-- Mensagens --}}
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Cabeçalho --}}
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    Utilizadores registados
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Total: {{ $utilizadores->total() ?? 0 }}
                                </p>
                            </div>

                            <a
                                href="{{ route('admin.utilizadores.create') }}"
                                class="bg-indigo-600 hover:bg-indigo-700
                                       text-white font-semibold
                                       py-2 px-4 rounded transition"
                            >
                                Criar utilizador
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
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-right"></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($utilizadores as $utilizador)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $utilizador->id }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-800">
                                                {{ $utilizador->nome }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $utilizador->email }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                <div class="flex items-center gap-2">
                                                    <span>
                                                        {{ $utilizador->tipoUtilizador->nome ?? 'Sem tipo' }}
                                                    </span>

                                                    @if ($utilizador->tipoUtilizador?->nome === 'Administrador')
                                                        <span
                                                            class="inline-flex items-center
                                                                   px-2 py-0.5 rounded-full
                                                                   text-xs font-semibold
                                                                   bg-red-100 text-red-800"
                                                        >
                                                            Admin
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 text-sm">
                                                <span
                                                    class="inline-flex items-center
                                                           px-2.5 py-0.5 rounded-full
                                                           text-xs font-semibold
                                                           {{ $utilizador->status === 'ativo'
                                                                ? 'bg-green-100 text-green-800'
                                                                : 'bg-yellow-100 text-yellow-800' }}"
                                                >
                                                    {{ ucfirst($utilizador->status) }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                                                <a
                                                    href="{{ route('admin.utilizadores.edit', $utilizador) }}"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Editar
                                                </a>

                                                <button
                                                    type="button"
                                                    @click="
                                                        showDeleteModal = true;
                                                        deleteUrl = '{{ route('admin.utilizadores.destroy', $utilizador) }}';
                                                    "
                                                    class="text-red-600 hover:text-red-900"
                                                    x-bind:disabled="{{ $utilizador->id === auth()->id() ? 'true' : 'false' }}"
                                                >
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                                Nenhum utilizador encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginação --}}
                        <div class="mt-4">
                            {{ $utilizadores->links() }}
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
                name="user-delete"
                title="Eliminar utilizador"
                content="Tem a certeza que deseja eliminar este utilizador? Esta ação não pode ser revertida."
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>
    </div>
</x-app-layout>
