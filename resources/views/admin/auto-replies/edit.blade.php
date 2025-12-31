<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar resposta automática
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Título --}}
                    <div class="mb-6">
                        <h3 class="text-xl font-bold">
                            Configuração da resposta automática
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Atualiza as regras e o conteúdo da resposta automática.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.auto-replies.update', $autoReply) }}">
                        @csrf
                        @method('PUT')

                        <!-- Disparador -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Disparador
                            </label>

                            <input
                                type="text"
                                name="trigger"
                                value="{{ old('trigger', $autoReply->trigger) }}"
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Ex.: horário, ajuda, contacto"
                            >
                        </div>

                        <!-- Resposta -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Resposta automática
                            </label>

                            <textarea
                                name="response"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300
                                       shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Texto que será enviado automaticamente quando o disparador for detetado"
                            >{{ old('response', $autoReply->response) }}</textarea>
                        </div>

                        <!-- Prioridade e estado -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Prioridade
                                </label>

                                <input
                                    type="number"
                                    name="priority"
                                    value="{{ old('priority', $autoReply->priority) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                            </div>

                            <div class="flex items-center mt-6">
                                <input
                                    type="checkbox"
                                    name="active"
                                    value="1"
                                    {{ $autoReply->active ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span class="ml-2 text-sm text-gray-700">
                                    Ativa
                                </span>
                            </div>
                        </div>

                        <!-- Ações -->
                        <div class="flex justify-end space-x-3">
                            <a
                                href="{{ route('admin.auto-replies.index') }}"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800
                                       px-4 py-2 rounded"
                            >
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700
                                       text-white px-4 py-2 rounded"
                            >
                                Atualizar
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
