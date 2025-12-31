<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Respostas automáticas
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        {{-- Mensagens de feedback --}}
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Cabeçalho --}}
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900">
                                Respostas automáticas ({{ $autoReplies->count() }})
                            </h3>

                            <a
                                href="{{ route('admin.auto-replies.create') }}"
                                class="bg-indigo-600 hover:bg-indigo-700
                                       text-white font-bold py-2 px-4
                                       rounded transition"
                            >
                                Nova resposta
                            </a>
                        </div>

                        {{-- Tabela --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Disparador
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">
                                            Resposta automática
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">
                                            Prioridade
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-600 uppercase">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-right"></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($autoReplies as $reply)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $reply->trigger }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-700 truncate max-w-md">
                                                {{ $reply->response }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-center text-gray-900">
                                                {{ $reply->priority }}
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 inline-flex text-xs font-semibold rounded-full
                                                    {{ $reply->active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-200 text-gray-700' }}">
                                                    {{ $reply->active ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <a
                                                    href="{{ route('admin.auto-replies.edit', $reply) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                                >
                                                    Editar
                                                </a>

                                                <button
                                                    @click="
                                                        showDeleteModal = true;
                                                        deleteUrl = '{{ route('admin.auto-replies.destroy', $reply) }}'
                                                    "
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Eliminar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Não existem respostas automáticas definidas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de confirmação --}}
        <div
            x-show="showDeleteModal"
            x-on:keydown.escape.window="showDeleteModal = false"
            style="display:none"
        >
            <x-confirm-modal
                name="auto-reply-delete"
                title="Eliminar resposta automática"
                content="Tem a certeza de que pretende eliminar esta resposta automática?"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>
    </div>
</x-app-layout>
