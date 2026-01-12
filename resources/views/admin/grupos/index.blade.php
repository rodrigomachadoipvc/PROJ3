<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Gestão de grupos de chat
        </h2>
    </x-slot>

    @php
        $allUsers = \App\Models\User::orderBy('nome')->get();
    @endphp

    <div
        x-data="{
            /* Estados gerais */
            showDeleteModal: false,
            deleteUrl: '',

            /* Gestão de membros */
            showMembrosModal: false,
            grupoId: null,
            grupoNome: '',
            membros: [],

            /* Remover membro */
            showMemberDeleteModal: false,
            memberName: '',

            /* Carregar membros via fetch */
            carregarMembros() {
                if (!this.grupoId) return;

                fetch(`/admin/grupos/${this.grupoId}/membros-json`)
                    .then(res => res.json())
                    .then(data => this.membros = data)
                    .catch(() => this.membros = []);
            }
        }"
    >

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="comm-card rounded-2xl overflow-hidden">
                    <div class="p-6 sm:p-8 text-white/90">

                        {{-- Flash messages --}}
                        @if (session('success'))
                            <div class="mb-6 rounded-xl border px-4 py-3 text-sm"
                                 style="border-color: rgba(34,197,94,.25); background: rgba(34,197,94,.10); color: rgba(220,252,231,.95);">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-6 rounded-xl border px-4 py-3 text-sm"
                                 style="border-color: rgba(248,113,113,.25); background: rgba(248,113,113,.10); color: rgba(254,202,202,.95);">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Header --}}
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
                            <div>
                                <div class="text-xs tracking-wide text-white/55 uppercase">
                                    Administração
                                </div>
                                <h3 class="mt-1 text-2xl font-semibold text-white">
                                    Grupos criados
                                </h3>
                                <p class="mt-1 text-sm text-white/65">
                                    Total: <span class="font-semibold text-white/85">{{ $grupos->total() }}</span>
                                </p>
                            </div>

                            <a
                                href="{{ route('admin.grupos.create') }}"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                       text-white border transition hover:brightness-110"
                                style="
                                    border-color: rgba(255,255,255,.12);
                                    background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));
                                "
                            >
                                Criar novo grupo
                            </a>
                        </div>

                        {{-- Tabela --}}
                        <div class="overflow-x-auto rounded-2xl border" style="border-color: rgba(255,255,255,.10);">
                            <table class="min-w-full">
                                <thead style="background: rgba(255,255,255,.04);">
                                    <tr class="border-b" style="border-color: rgba(255,255,255,.10);">
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">Criador</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">Membros</th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-white/60">Ações</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y" style="divide-color: rgba(255,255,255,.08);">
                                    @forelse ($grupos as $grupo)
                                        <tr
                                            class="transition"
                                            style="background: rgba(255,255,255,.02);"
                                            onmouseover="this.style.background='rgba(255,255,255,.05)'"
                                            onmouseout="this.style.background='rgba(255,255,255,.02)'"
                                        >
                                            <td class="px-6 py-4 text-sm font-semibold text-white/85">
                                                {{ $grupo->id }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/85">
                                                <div class="font-semibold">{{ $grupo->nome }}</div>
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/65 max-w-sm truncate">
                                                {{ $grupo->descricao ?? '—' }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/70">
                                                {{ $grupo->criador->nome ?? '—' }}
                                            </td>

                                            <td class="px-6 py-4 text-sm">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                                      style="border-color: rgba(96,165,250,.25); background: rgba(96,165,250,.10); color: rgba(191,219,254,.95);">
                                                    {{ $grupo->membros->count() }} membros
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                                <div class="inline-flex items-center gap-4">

                                                    {{-- Gerir Membros --}}
                                                    <button
                                                        @click="
                                                            membros = [];
                                                            grupoId = {{ $grupo->id }};
                                                            grupoNome = '{{ $grupo->nome }}';

                                                            fetch(`/admin/grupos/${grupoId}/membros-json`)
                                                                .then(res => res.json())
                                                                .then(data => {
                                                                    membros = data;
                                                                    showMembrosModal = true;
                                                                });
                                                        "
                                                        class="font-semibold hover:underline"
                                                        style="color: rgba(45,212,191,.95);"
                                                    >
                                                        Gerir membros
                                                    </button>

                                                    {{-- Editar --}}
                                                    <a
                                                        href="{{ route('admin.grupos.edit', $grupo) }}"
                                                        class="font-semibold hover:underline"
                                                        style="color: rgba(96,165,250,.95);"
                                                    >
                                                        Editar
                                                    </a>

                                                    {{-- Eliminar --}}
                                                    <button
                                                        @click="
                                                            showDeleteModal = true;
                                                            deleteUrl = '{{ route('admin.grupos.destroy', $grupo) }}';
                                                        "
                                                        class="font-semibold hover:underline"
                                                        style="color: rgba(248,113,113,.95);"
                                                    >
                                                        Eliminar
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-sm text-white/60">
                                                Nenhum grupo encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 text-white/80">
                            {{ $grupos->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Modal eliminar grupo --}}
        <div x-show="showDeleteModal" style="display:none;">
            <x-confirm-modal
                name="group-delete"
                title="Eliminar grupo"
                content="Tem a certeza que deseja eliminar este grupo?"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>

        {{-- Modal remover membro --}}
        <div x-show="showMemberDeleteModal" style="display:none;">
            <x-confirm-modal
                name="member-delete"
                title="Remover membro"
                x-bind:content="'Tem a certeza que deseja remover ' + memberName + ' deste grupo?'"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>

        {{-- Modal de membros --}}
        @include('admin.grupos.membros_modal')

    </div>
</x-app-layout>
