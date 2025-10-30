<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Utilizador') }}: {{ $utilizador->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-xl font-bold mb-6">{{ __('Detalhes do Utilizador') }}</h3>

                    <!-- Formulário de Edição (Usa método PUT) -->
                    <form method="POST" action="{{ route('admin.utilizadores.update', $utilizador->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nome Completo -->
                        <div class="mb-4">
                            <x-input-label for="nome" :value="__('Nome Completo')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $utilizador->nome)" required autofocus />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Endereço de Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $utilizador->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Alterar Password (Opcional) -->
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h4 class="text-lg font-semibold mb-4">{{ __('Alterar Password (Opcional)')}}</h4>
                            <div class="mb-4">
                                <x-input-label for="password" :value="__('Nova Password (mínimo 8 caracteres)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">{{ __('Deixe em branco para não alterar a password.') }}</p>
                            </div>

                            <div class="mb-6">
                                <x-input-label for="password_confirmation" :value="__('Confirmar Nova Password')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Tipo de Utilizador (Dropdown/Select) -->
                        <div class="mb-6 border-t border-gray-200 pt-6 mt-6">
                            <x-input-label for="tipo_utilizador_id" :value="__('Tipo de Utilizador (Role)')" />
                            <select id="tipo_utilizador_id" name="tipo_utilizador_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Selecione um Tipo --</option>
                                @foreach ($tiposUtilizador as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_utilizador_id', $utilizador->tipo_utilizador_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tipo_utilizador_id')" class="mt-2" />
                        </div>

                        <!-- Status -->
                         <div class="mb-6">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="ativo" {{ old('status', $utilizador->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ old('status', $utilizador->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                <!-- Adicionar 'bloqueado' se necessário -->
                                <!-- <option value="bloqueado" {{ old('status', $utilizador->status) == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option> -->
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.utilizadores.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
                            <x-primary-button>
                                {{ __('Guardar Alterações') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>