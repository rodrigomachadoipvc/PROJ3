<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar utilizador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Título --}}
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900">
                            Detalhes do novo utilizador
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Preenche os dados base, define o tipo de utilizador e o estado inicial da conta.
                        </p>
                    </div>

                    <!-- Formulário -->
                    <form method="POST" action="{{ route('admin.utilizadores.store') }}">
                        @csrf

                        <!-- Nome -->
                        <div class="mb-4">
                            <x-input-label for="nome" :value="__('Nome completo')" />
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

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Endereço de email')" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                class="block mt-1 w-full
                                       border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label
                                for="password"
                                :value="__('Password')"
                            />
                            <p class="text-xs text-gray-500 mb-1">
                                Mínimo 8 caracteres, incluindo uma letra maiúscula, um número e um símbolo.
                            </p>

                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="block mt-1 w-full
                                       border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmar Password -->
                        <div class="mb-6">
                            <x-input-label for="password_confirmation" :value="__('Confirmar password')" />
                            <x-text-input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                class="block mt-1 w-full
                                       border-gray-300
                                       focus:border-indigo-500
                                       focus:ring-indigo-500"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Tipo de utilizador -->
                        <div class="mb-6">
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
                                        {{ old('tipo_utilizador_id') == $tipo->id ? 'selected' : '' }}
                                    >
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('tipo_utilizador_id')" class="mt-2" />
                        </div>

                        <!-- Estado -->
                        <div class="mb-6">
                            <x-input-label
                                for="status"
                                :value="__('Estado inicial da conta')"
                            />

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
                                <option value="ativo" {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>
                                    Ativo
                                </option>
                                <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>
                                    Inativo
                                </option>
                            </select>

                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center justify-end mt-6">
                            <a
                                href="{{ route('admin.utilizadores.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar utilizador
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
