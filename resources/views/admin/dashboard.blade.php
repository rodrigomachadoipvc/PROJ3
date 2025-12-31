<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Painel de administração
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Introdução --}}
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900">
                            Communico · Administração
                        </h3>
                        <p class="mt-2 text-sm text-gray-600 max-w-3xl">
                            A partir deste painel pode gerir utilizadores, permissões, grupos de chat
                            e respostas automáticas do sistema de comunicação interna.
                        </p>
                    </div>

                    {{-- Secção principal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                        {{-- Tipos de utilizador --}}
                        <div
                            class="bg-gray-50 border border-gray-200 rounded-xl p-5
                                   hover:shadow-md hover:border-indigo-300 transition"
                        >
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">
                                Tipos de utilizador
                            </h4>

                            <p class="text-sm text-gray-600 mb-4">
                                Definição de funções e permissões de acesso ao sistema.
                            </p>

                            <a
                                href="{{ route('admin.tipos-utilizadores.index') }}"
                                class="inline-flex items-center text-sm font-semibold
                                       text-indigo-600 hover:text-indigo-800"
                            >
                                Gerir tipos
                                <span class="ml-1">→</span>
                            </a>
                        </div>

                        {{-- Utilizadores --}}
                        <div
                            class="bg-gray-50 border border-gray-200 rounded-xl p-5
                                   hover:shadow-md hover:border-indigo-300 transition"
                        >
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">
                                Utilizadores
                            </h4>

                            <p class="text-sm text-gray-600 mb-4">
                                Criação, edição e controlo do estado das contas.
                            </p>

                            <a
                                href="{{ route('admin.utilizadores.index') }}"
                                class="inline-flex items-center text-sm font-semibold
                                       text-indigo-600 hover:text-indigo-800"
                            >
                                Gerir utilizadores
                                <span class="ml-1">→</span>
                            </a>
                        </div>

                        {{-- Grupos --}}
                        <div
                            class="bg-gray-50 border border-gray-200 rounded-xl p-5
                                   hover:shadow-md hover:border-indigo-300 transition"
                        >
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">
                                Grupos de chat
                            </h4>

                            <p class="text-sm text-gray-600 mb-4">
                                Organização de conversas em grupo e respetivos membros.
                            </p>

                            <a
                                href="{{ route('admin.grupos.index') }}"
                                class="inline-flex items-center text-sm font-semibold
                                       text-indigo-600 hover:text-indigo-800"
                            >
                                Gerir grupos
                                <span class="ml-1">→</span>
                            </a>
                        </div>

                        {{-- Respostas automáticas --}}
                        <div
                            class="bg-gray-50 border border-gray-200 rounded-xl p-5
                                   hover:shadow-md hover:border-indigo-300 transition"
                        >
                            <h4 class="font-semibold text-lg text-gray-800 mb-2">
                                Respostas automáticas
                            </h4>

                            <p class="text-sm text-gray-600 mb-4">
                                Configuração de respostas baseadas em regras e palavras-chave.
                            </p>

                            <a
                                href="{{ route('admin.auto-replies.index') }}"
                                class="inline-flex items-center text-sm font-semibold
                                       text-indigo-600 hover:text-indigo-800"
                            >
                                Gerir respostas
                                <span class="ml-1">→</span>
                            </a>
                        </div>

                    </div>

                    {{-- Nota inferior --}}
                    <div class="mt-10 text-sm text-gray-500 border-t pt-4">
                        <p>
                            Este painel destina-se exclusivamente à gestão administrativa do Communico.
                            Todas as alterações têm impacto imediato no sistema.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
