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

        x-on:confirm-close.window="
            if ($event.detail && $event.detail.name === 'group-delete') {
                showDeleteModal = false;
            }
            if ($event.detail && $event.detail.name === 'member-delete') {
                showMemberDeleteModal = false;
            }
        "
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

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end"
                                 x-data="{
                                    q: @js((string) request('q', '')),
                                    timer: null,
                                    submitLive() {
                                        clearTimeout(this.timer);
                                        this.timer = setTimeout(() => {
                                            const v = (this.q || '').trim();
                                            if (v.length === 0) {
                                                window.location.href = @js(route('admin.grupos.index'));
                                                return;
                                            }
                                            this.$refs.searchForm.submit();
                                        }, 250);
                                    }
                                 }"
                            >
                                {{-- Pesquisa LIVE --}}
                                <form x-ref="searchForm" method="GET" action="{{ route('admin.grupos.index') }}" class="flex items-center gap-2">
                                    <div class="relative">
                                        <span
                                            class="pointer-events-none absolute"
                                            style="left: 12px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,.55);"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:block;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.3 6.3a7.5 7.5 0 0 0 10.35 10.35Z" />
                                            </svg>
                                        </span>

                                        <input
                                            type="text"
                                            name="q"
                                            x-model="q"
                                            @input="submitLive()"
                                            placeholder="Pesquisar por nome, descrição, ID ou criador…"
                                            class="w-full sm:w-80 rounded-xl border px-10 py-2 text-sm text-white placeholder:text-white/45 focus:outline-none focus:ring-2 focus:ring-sky-400/35"
                                            style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.04);"
                                            autocomplete="off"
                                        />
                                    </div>

                                    @if (request('q'))
                                        <a
                                            href="{{ route('admin.grupos.index') }}"
                                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-sm font-semibold text-white/80 border transition hover:text-white hover:brightness-110"
                                            style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                                        >
                                            Limpar
                                        </a>
                                    @endif
                                </form>

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
                        </div>

                        {{-- Tabela (scroll interno) --}}
                        <div
                            class="relative rounded-2xl border"
                            style="
                                border-color: rgba(255,255,255,.10);
                                height: 62vh;
                                overflow-y: scroll;
                                overflow-x: auto;
                            "
                        >
                            <table class="min-w-full">
                                <thead style="background: rgba(255,255,255,.04);">
                                    <tr class="border-b" style="border-color: rgba(255,255,255,.10);">
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10"
                                            style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10"
                                            style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Nome
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10"
                                            style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Descrição
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10"
                                            style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Criador
                                        </th>

                                        {{-- Header centrado --}}
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10"
                                            style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Membros
                                        </th>

                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10"
                                            style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y" style="divide-color: rgba(255,255,255,.08);">
                                    @forelse ($grupos as $grupo)
                                        @php
                                            $countMembros = $grupo->membros->count();
                                        @endphp

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

                                            {{-- Célula centrada --}}
                                            <td class="px-6 py-4 text-sm text-center">
                                                <span
                                                    class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold border whitespace-nowrap min-w-[2.5rem]"
                                                    style="border-color: rgba(96,165,250,.25); background: rgba(96,165,250,.10); color: rgba(191,219,254,.95);"
                                                    title="Número de membros"
                                                >
                                                    {{ $countMembros }}
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
        <template x-teleport="body">
            <div
                x-show="showDeleteModal"
                x-cloak
                class="fixed inset-0"
                style="z-index: 2147483646;"
            >
                <x-confirm-modal
                    name="group-delete"
                    title="Eliminar grupo"
                    content="Tem a certeza que deseja eliminar este grupo?"
                    method="DELETE"
                    confirmText="Confirmar"
                    cancelText="Cancelar"
                />
            </div>
        </template>

        {{-- Modal remover membro --}}
        <template x-teleport="body">
            <div
                x-show="showMemberDeleteModal"
                x-cloak
                class="fixed inset-0"
                style="z-index: 2147483647;"
            >
                <x-confirm-modal
                    name="member-delete"
                    title="Remover membro"
                    x-bind:content="'Tem a certeza que deseja remover ' + memberName + ' deste grupo?'"
                    method="DELETE"
                    confirmText="Confirmar"
                    cancelText="Cancelar"
                />
            </div>
        </template>

        {{-- Modal de membros --}}
        @include('admin.grupos.membros_modal')

    </div>
</x-app-layout>
