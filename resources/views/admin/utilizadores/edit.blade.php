<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar utilizador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Título --}}
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ $utilizador->nome }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Atualiza os dados da conta, tipo de utilizador e estado.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.utilizadores.update', $utilizador->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Dados base --}}
                        <div class="mb-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                Dados do utilizador
                            </h4>

                            <!-- Nome -->
                            <div class="mb-4">
                                <x-input-label for="nome" :value="__('Nome completo')" />
                                <x-text-input
                                    id="nome"
                                    type="text"
                                    name="nome"
                                    :value="old('nome', $utilizador->nome)"
                                    required
                                    autofocus
                                    class="block mt-1 w-full
                                           border-gray-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500"
                                />
                                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <x-input-label for="email" :value="__('Endereço de email')" />
                                <x-text-input
                                    id="email"
                                    type="email"
                                    name="email"
                                    :value="old('email', $utilizador->email)"
                                    required
                                    class="block mt-1 w-full
                                           border-gray-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500"
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Alterar password --}}
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                Alterar password
                            </h4>

                            <p class="text-sm text-gray-600 mb-4">
                                Preenche apenas se quiseres alterar a password atual.
                            </p>

                            <div class="mb-4">
                                <x-input-label for="password" :value="__('Nova password')" />
                                <x-text-input
                                    id="password"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password"
                                    class="block mt-1 w-full
                                           border-gray-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500"
                                />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="password_confirmation" :value="__('Confirmar nova password')" />
                                <x-text-input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="block mt-1 w-full
                                           border-gray-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500"
                                />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Tipo e estado --}}
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                Permissões e estado
                            </h4>

                            <!-- Tipo de utilizador -->
                            <div class="mb-4">
                                <x-input-label
                                    for="tipo_utilizador_id"
                                    :value="__('Tipo de utilizador')"
                                />

                                <select
                                    id="tipo_utilizador_id"
                                    name="tipo_utilizador_id"
                                    required
                                    class="block mt-1 w-full
                                           border-gray-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500
                                           rounded-md shadow-sm"
                                >
                                    <option value="">Selecionar tipo</option>
                                    @foreach ($tiposUtilizador as $tipo)
                                        <option
                                            value="{{ $tipo->id }}"
                                            {{ old('tipo_utilizador_id', $utilizador->tipo_utilizador_id) == $tipo->id ? 'selected' : '' }}
                                        >
                                            {{ $tipo->nome }}
                                        </option>
                                    @endforeach
                                </select>

                                <x-input-error :messages="$errors->get('tipo_utilizador_id')" class="mt-2" />
                            </div>

                            <!-- Estado -->
                            <div class="mb-4">
                                <x-input-label for="status" :value="__('Estado da conta')" />

                                <select
                                    id="status"
                                    name="status"
                                    required
                                    class="block mt-1 w-full
                                           border-gray-300
                                           focus:border-indigo-500
                                           focus:ring-indigo-500
                                           rounded-md shadow-sm"
                                >
                                    <option value="ativo" {{ old('status', $utilizador->status) === 'ativo' ? 'selected' : '' }}>
                                        Ativo
                                    </option>
                                    <option value="inativo" {{ old('status', $utilizador->status) === 'inativo' ? 'selected' : '' }}>
                                        Inativo
                                    </option>
                                </select>

                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center justify-end mt-6">
                            <a
                                href="{{ route('admin.utilizadores.index') }}"
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
