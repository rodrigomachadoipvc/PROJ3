<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white-800 leading-tight">
            Painel de administração
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Container principal (glass) --}}
            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8">

                    {{-- Cabeçalho --}}
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                        <div>
                            <p class="text-sm font-semibold tracking-wide text-white/60">
                                Communico
                            </p>

                            <h3 class="mt-1 text-2xl sm:text-3xl font-semibold text-white">
                                Administração
                            </h3>

                            <p class="mt-2 text-sm text-white/70 max-w-3xl leading-relaxed">
                                A partir deste painel pode gerir utilizadores, permissões, grupos de chat
                                e respostas automáticas do sistema de comunicação interna.
                            </p>
                        </div>

                        <div class="sm:flex items-center">
                            <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs text-white/70">
                                <span class="h-2 w-2 rounded-full" style="background: var(--c-a2)"></span>
                                Sistema ativo
                            </span>
                        </div>
                    </div>

                    {{-- Ações rápidas --}}
                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                        <a
                            href="{{ route('admin.tipos-utilizadores.index') }}"
                            class="comm-card rounded-2xl p-6 transition
                                   hover:-translate-y-0.5 hover:border-[rgba(96,165,250,.35)]
                                   focus:outline focus:outline-2 focus:outline-offset-2"
                            style="outline-color: rgba(96,165,250,.45);"
                        >
                            <h4 class="text-base font-semibold text-white">
                                Tipos de utilizador
                            </h4>

                            <p class="mt-2 text-sm text-white/70 leading-relaxed">
                                Definição de funções e permissões de acesso ao sistema.
                            </p>

                            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold"
                                 style="color: var(--c-a1);">
                                Gerir tipos <span aria-hidden="true">→</span>
                            </div>
                        </a>

                        <a
                            href="{{ route('admin.utilizadores.index') }}"
                            class="comm-card rounded-2xl p-6 transition
                                   hover:-translate-y-0.5 hover:border-[rgba(45,212,191,.30)]
                                   focus:outline focus:outline-2 focus:outline-offset-2"
                            style="outline-color: rgba(45,212,191,.45);"
                        >
                            <h4 class="text-base font-semibold text-white">
                                Utilizadores
                            </h4>

                            <p class="mt-2 text-sm text-white/70 leading-relaxed">
                                Criação, edição e controlo do estado das contas.
                            </p>

                            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold"
                                 style="color: var(--c-a2);">
                                Gerir utilizadores <span aria-hidden="true">→</span>
                            </div>
                        </a>

                        <a
                            href="{{ route('admin.grupos.index') }}"
                            class="comm-card rounded-2xl p-6 transition
                                   hover:-translate-y-0.5 hover:border-[rgba(96,165,250,.35)]
                                   focus:outline focus:outline-2 focus:outline-offset-2"
                            style="outline-color: rgba(96,165,250,.45);"
                        >
                            <h4 class="text-base font-semibold text-white">
                                Grupos de chat
                            </h4>

                            <p class="mt-2 text-sm text-white/70 leading-relaxed">
                                Organização de conversas em grupo e respetivos membros.
                            </p>

                            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold"
                                 style="color: var(--c-a1);">
                                Gerir grupos <span aria-hidden="true">→</span>
                            </div>
                        </a>

                        <a
                            href="{{ route('admin.auto-replies.index') }}"
                            class="comm-card rounded-2xl p-6 transition
                                   hover:-translate-y-0.5 hover:border-[rgba(45,212,191,.30)]
                                   focus:outline focus:outline-2 focus:outline-offset-2"
                            style="outline-color: rgba(45,212,191,.45);"
                        >
                            <h4 class="text-base font-semibold text-white">
                                Respostas automáticas
                            </h4>

                            <p class="mt-2 text-sm text-white/70 leading-relaxed">
                                Configuração de respostas baseadas em regras e palavras-chave.
                            </p>

                            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold"
                                 style="color: var(--c-a2);">
                                Gerir respostas <span aria-hidden="true">→</span>
                            </div>
                        </a>

                        <a
                            href="{{ route('admin.estatisticas.index') }}"
                            class="comm-card rounded-2xl p-6 transition
                                   hover:-translate-y-0.5 hover:border-[rgba(96,165,250,.35)]
                                   focus:outline focus:outline-2 focus:outline-offset-2"
                            style="outline-color: rgba(96,165,250,.45);"
                        >
                            <h4 class="text-base font-semibold text-white">
                                Estatísticas Gerais
                            </h4>

                            <p class="mt-2 text-sm text-white/70 leading-relaxed">
                                Visualização de estatisticas gerais acerca da aplicação.
                            </p>

                            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold"
                                 style="color: var(--c-a1);">
                                Estatisticas <span aria-hidden="true">→</span>
                            </div>
                        </a>

                    </div>

                    {{-- Nota --}}
                    <div class="mt-10 border-t border-white/10 pt-4">
                        <p class="text-sm text-white/55">
                            Este painel destina-se exclusivamente à gestão administrativa do Communico.
                            Todas as alterações têm impacto imediato no sistema.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
