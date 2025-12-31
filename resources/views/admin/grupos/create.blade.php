<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Criar novo grupo de chat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">

                    {{-- Título --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Detalhes do grupo
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Define as informações base do novo grupo de chat.
                        </p>
                    </div>

                    <!-- Formulário de Criação -->
                    <form method="POST" action="{{ route('admin.grupos.store') }}" class="space-y-6">
                        @csrf

                        <!-- Nome do Grupo -->
                        <div>
                            <x-input-label
                                for="nome"
                                :value="__('Nome do grupo')"
                            />

                            <x-text-input
                                id="nome"
                                type="text"
                                name="nome"
                                :value="old('nome')"
                                required
                                autofocus
                                class="mt-1 block w-full
                                       rounded-lg
                                       bg-gray-50 border border-gray-300
                                       focus:bg-white
                                       focus:border-brand-secondary
                                       focus:ring-brand-secondary"
                                placeholder="Ex.: Projeto Alpha, Equipa Comercial"
                            />

                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        <!-- Descrição -->
                        <div>
                            <x-input-label
                                for="descricao"
                                :value="__('Descrição (opcional)')"
                            />

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="4"
                                class="mt-1 block w-full
                                       rounded-lg
                                       bg-gray-50 border border-gray-300
                                       focus:bg-white
                                       focus:border-brand-secondary
                                       focus:ring-brand-secondary
                                       shadow-sm"
                                placeholder="Breve descrição do objetivo do grupo"
                            >{{ old('descricao') }}</textarea>

                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <!-- Ações -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <a
                                href="{{ route('admin.grupos.index') }}"
                                class="text-sm text-gray-600
                                       hover:text-brand-secondary
                                       focus:outline-none focus:ring-2
                                       focus:ring-offset-2
                                       focus:ring-brand-secondary
                                       rounded-md"
                            >
                                Cancelar
                            </a>

                            <x-primary-button class="px-6 py-3">
                                Guardar grupo
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
