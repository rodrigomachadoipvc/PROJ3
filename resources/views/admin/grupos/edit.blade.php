<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar grupo de chat: {{ $grupo->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Título --}}
                    <div class="mb-6">
                        <h3 class="text-xl font-bold">
                            Detalhes do grupo
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Atualiza as informações do grupo de chat.
                        </p>
                    </div>

                    <!-- Formulário de Edição -->
                    <form method="POST" action="{{ route('admin.grupos.update', $grupo->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nome do Grupo -->
                        <div class="mb-4">
                            <x-input-label
                                for="nome"
                                :value="__('Nome do grupo')"
                            />

                            <x-text-input
                                id="nome"
                                type="text"
                                name="nome"
                                :value="old('nome', $grupo->nome)"
                                required
                                autofocus
                                class="block mt-1 w-full
                                       border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            />

                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <!-- Descrição -->
                        <div class="mb-6">
                            <x-input-label
                                for="descricao"
                                :value="__('Descrição (opcional)')"
                            />

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="4"
                                class="block mt-1 w-full
                                       border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500
                                       rounded-md shadow-sm"
                            >{{ old('descricao', $grupo->descricao) }}</textarea>

                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center justify-end mt-4">
                            <a
                                href="{{ route('admin.grupos.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar alterações
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
