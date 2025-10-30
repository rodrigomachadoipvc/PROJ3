<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Tipo de Utilizador') }}: {{ $tipo->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-xl font-bold mb-6">{{ __('Detalhes do Tipo') }}</h3>

                    <form method="POST" action="{{ route('admin.tipos-utilizadores.update', $tipo) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="nome" :value="__('Nome do Tipo (Ex: Colaborador, Gestor)')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $tipo->nome)" required autofocus />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="descricao" :value="__('Descrição')" />
                            <textarea id="descricao" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="descricao">{{ old('descricao', $tipo->descricao) }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>
                        
                        <div class="mb-6">
                            <x-input-label :value="__('Permissões Associadas')" class="mb-3 font-semibold text-base" />
                            
                            <div class="bg-gray-50 p-4 rounded-md border border-gray-200 h-96 overflow-y-scroll shadow-inner">
                                @error('permissoes')
                                    <p class="text-sm text-red-600 mb-2">{{ __('Selecione pelo menos uma permissão válida.') }}</p>
                                @enderror
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                    
                                    @forelse ($permissoes as $permissao)
                                        <div class="flex items-center">
                                            <input id="permissao_{{ $permissao->id }}" 
                                                name="permissoes[]" 
                                                type="checkbox" 
                                                value="{{ $permissao->id }}"
                                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                {{ in_array($permissao->id, old('permissoes', $permissoesAtuais)) ? 'checked' : '' }}>
                                            
                                            <label for="permissao_{{ $permissao->id }}" 
                                                class="ml-2 text-sm font-medium text-gray-700 select-none cursor-pointer hover:text-indigo-600 transition"
                                                title="{{ $permissao->descricao }}">
                                                {{ $permissao->nome }}
                                            </label>
                                        </div>
                                    @empty
                                        <p class="col-span-4 text-center text-sm text-gray-500">{{ __('Não existem permissões definidas.') }}</p>
                                    @endforelse
                                    
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ __('Passe o rato sobre o nome da permissão para ver a descrição detalhada.') }}</p>
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.tipos-utilizadores.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
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