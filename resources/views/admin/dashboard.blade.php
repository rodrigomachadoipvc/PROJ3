<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Administração do Communico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">{{ __('Bem-vindo, Administrador!') }}</h3>
                    <p class="mb-4">{{ __('A partir daqui pode gerir todos os aspetos do sistema de chat interno.') }}</p>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                            <h4 class="font-bold text-lg mb-2">{{ __('Tipos de Utilizador & Permissões') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Gerir as funções (Administrador, Gestor, Colaborador) e as suas permissões de acesso.') }}</p>
                            <a href="{{ route('admin.tipos-utilizadores.index') }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-800 font-semibold text-sm">{{ __('Gerir Tipos') }} &rarr;</a>
                        </div>

                        <div class="bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                            <h4 class="font-bold text-lg mb-2">{{ __('Gestão de Utilizadores') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Adicionar, editar e desativar contas de utilizadores do sistema.') }}</p>
                            <a href="{{ route('admin.utilizadores.index') }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-800 font-semibold text-sm">{{ __('Gerir Utilizadores') }} &rarr;</a>
                        </div>

                        <div class="bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                            <h4 class="font-bold text-lg mb-2">{{ __('Gestão de Grupos de Chat') }}</h4>
                            <p class="text-sm text-gray-600">{{ __('Configurar e gerir grupos de chat internos.') }}</p>
                            <a href="{{ route('admin.grupos.index') }}" class="mt-3 inline-block text-indigo-600 hover:text-indigo-800 font-semibold text-sm">{{ __('Gerir Grupos') }} &rarr;</a>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                            <h4 class="font-bold text-lg mb-2">{{ __('Respostas Automáticas') }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ __('Gerir respostas automáticas baseadas em regras para o chat interno.') }}
                            </p>
                            <a href="{{ route('admin.auto-replies.index') }}"
                            class="mt-3 inline-block text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                                {{ __('Gerir Respostas') }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>