<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Grupo de Chat') }}: {{ $grupo->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-xl font-bold mb-6">{{ __('Detalhes do Grupo') }}</h3>

                    <!-- Formulário de Edição -->
                    <form method="POST" action="{{ route('admin.grupos.update', $grupo->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nome do Grupo -->
                        <div class="mb-4">
                            <x-input-label for="nome" :value="__('Nome do Grupo')" />
                            <x-text-input id="nome" class="block mt-1 w-full" type="text" name="nome" :value="old('nome', $grupo->nome)" required autofocus />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <!-- Descrição -->
                        <div class="mb-6">
                            <x-input-label for="descricao" :value="__('Descrição (Opcional)')" />
                            <textarea id="descricao" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" name="descricao">{{ old('descricao', $grupo->descricao) }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.grupos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancelar') }}</a>
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