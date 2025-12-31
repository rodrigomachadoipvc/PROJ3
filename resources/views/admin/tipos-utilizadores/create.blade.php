<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar tipo de utilizador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Título --}}
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900">
                            Detalhes do novo tipo
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Define o nome, descrição e permissões associadas a este tipo de utilizador.
                        </p>
                    </div>

                    <!-- Formulário -->
                    <form method="POST" action="{{ route('admin.tipos-utilizadores.store') }}">
                        @csrf

                        <!-- Nome -->
                        <div class="mb-4">
                            <x-input-label
                                for="nome"
                                :value="__('Nome do tipo')"
                            />

                            <x-text-input
                                id="nome"
                                type="text"
                                name="nome"
                                :value="old('nome')"
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
                                :value="__('Descrição')"
                            />

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="3"
                                class="block mt-1 w-full
                                       border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500
                                       rounded-md shadow-sm"
                            >{{ old('descricao') }}</textarea>

                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <!-- Permissões -->
                        <div class="mb-6">
                            <x-input-label
                                :value="__('Permissões associadas')"
                                class="mb-2 font-semibold text-base"
                            />

                            <p class="mb-3 text-sm text-gray-600">
                                Seleciona as permissões que este tipo de utilizador deverá possuir.
                            </p>

                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200
                                        h-96 overflow-y-auto shadow-inner">

                                @error('permissoes')
                                    <p class="text-sm text-red-600 mb-3">
                                        Seleciona pelo menos uma permissão válida.
                                    </p>
                                @enderror

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    @forelse ($permissoes as $permissao)
                                        <label
                                            for="permissao_{{ $permissao->id }}"
                                            class="flex items-center space-x-2
                                                   cursor-pointer select-none
                                                   text-sm font-medium text-gray-700
                                                   hover:text-indigo-600 transition"
                                            title="{{ $permissao->descricao }}"
                                        >
                                            <input
                                                id="permissao_{{ $permissao->id }}"
                                                name="permissoes[]"
                                                type="checkbox"
                                                value="{{ $permissao->id }}"
                                                class="w-4 h-4 text-indigo-600
                                                       border-gray-300 rounded
                                                       focus:ring-indigo-500"
                                                {{ in_array($permissao->id, old('permissoes', [])) ? 'checked' : '' }}
                                            >
                                            <span>{{ $permissao->nome }}</span>
                                        </label>
                                    @empty
                                        <p class="col-span-4 text-center text-sm text-gray-500">
                                            Não existem permissões definidas no sistema.
                                            É necessário executar o seeder de permissões.
                                        </p>
                                    @endforelse
                                </div>
                            </div>

                            <p class="mt-2 text-xs text-gray-500">
                                Passa o rato sobre o nome da permissão para ver a descrição detalhada.
                            </p>
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center justify-end mt-6">
                            <a
                                href="{{ route('admin.tipos-utilizadores.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar tipo
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
