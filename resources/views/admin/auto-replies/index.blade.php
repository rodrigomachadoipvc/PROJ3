<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Respostas Automáticas') }}
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        {{-- Flash messages --}}
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold">
                                {{ __('Respostas Automáticas') }} ({{ $autoReplies->count() }})
                            </h3>

                            <a href="{{ route('admin.auto-replies.create') }}"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                                {{ __('Nova Resposta') }}
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trigger</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Resposta</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prioridade</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ativa</th>
                                        <th class="px-6 py-3 text-right"></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($autoReplies as $reply)
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $reply->trigger }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-md">
                                                {{ $reply->response }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-center">
                                                {{ $reply->priority }}
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 inline-flex text-xs font-semibold rounded-full
                                                    {{ $reply->active ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                                                    {{ $reply->active ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium">
                                                <a href="{{ route('admin.auto-replies.edit', $reply) }}"
                                                   class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    {{ __('Editar') }}
                                                </a>

                                                <button
                                                    @click="showDeleteModal = true; deleteUrl = '{{ route('admin.auto-replies.destroy', $reply) }}'"
                                                    class="text-red-600 hover:text-red-900">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                {{ __('Nenhuma resposta automática definida.') }}
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

        {{-- Confirm modal --}}
        <div x-show="showDeleteModal" x-on:keydown.escape.window="showDeleteModal = false" style="display:none">
            <x-confirm-modal
                name="auto-reply-delete"
                title="Eliminar Resposta Automática"
                content="Tem a certeza que deseja eliminar esta resposta automática?"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>
    </div>
</x-app-layout>
