<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Adicionar Novo Utilizador') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-xl font-bold mb-6">{{ __('Detalhes do Novo Utilizador') }}</h3>

                    <!-- Formulário de Criação -->
                    <form method="POST" action="{{ route('admin.utilizadores.store') }}">
                        @csrf

                        <!-- Nome Completo -->
                        <div class="mb-4">
                            <x-input-label for="nome" :value="__('Nome Completo')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome')" required autofocus />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Endereço de Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Password (mínimo 8 caracteres)')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmação de Password -->
                        <div class="mb-6">
                            <x-input-label for="password_confirmation" :value="__('Confirmar Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Tipo de Utilizador (Dropdown/Select) -->
                        <div class="mb-6">
                            <x-input-label for="tipo_utilizador_id" :value="__('Tipo de Utilizador (Role)')" />
                            <select id="tipo_utilizador_id" name="tipo_utilizador_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Selecione um Tipo --</option>
                                @foreach ($tiposUtilizador as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_utilizador_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('tipo_utilizador_id')" class="mt-2" />
                        </div>

                        <!-- Status (Opcional - Pode ser 'ativo' por padrão) -->
                         <div class="mb-6">
                            <x-input-label for="status" :value="__('Status Inicial')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.utilizadores.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
                            <x-primary-button>
                                {{ __('Guardar Utilizador') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>