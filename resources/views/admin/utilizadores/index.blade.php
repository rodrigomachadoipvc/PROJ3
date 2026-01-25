<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Gestão de utilizadores
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="comm-card rounded-2xl overflow-hidden">
                    <div class="p-6 sm:p-8 text-white/90">

                        {{-- FIX (PROD): alinhamento do ícone + padding consistente --}}
                        <style>
                            .comm-search {
                                position: relative;
                                flex: 0 0 auto;
                                width: min(20rem, 100%);
                            }
                            .comm-search-icon {
                                position: absolute;
                                left: 12px;
                                top: 50%;
                                transform: translateY(-50%);
                                pointer-events: none;
                                color: rgba(255,255,255,.55);
                                line-height: 0;
                            }
                            .comm-search-input {
                                width: 100%;
                                padding-left: 2.5rem !important;
                                padding-right: .75rem !important;
                                padding-top: .5rem !important;
                                padding-bottom: .5rem !important;
                            }
                        </style>

                        {{-- Mensagens --}}
                        @if (session('success'))
                            <div class="mb-5 rounded-xl border border-emerald-400/25 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-5 rounded-xl border border-red-400/25 bg-red-400/10 px-4 py-3 text-sm text-red-100">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Cabeçalho --}}
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
                            <div>
                                <div class="text-xs tracking-wide text-white/55 uppercase">
                                    Administração
                                </div>
                                <h3 class="mt-1 text-2xl font-semibold text-white">
                                    Utilizadores registados
                                </h3>
                                <p class="mt-1 text-sm text-white/65">
                                    Total: <span class="font-semibold text-white/85">{{ $utilizadores->total() ?? 0 }}</span>
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
                                                window.location.href = @js(route('admin.utilizadores.index'));
                                                return;
                                            }
                                            this.$refs.searchForm.submit();
                                        }, 250);
                                    }
                                 }"
                            >

                                {{-- Pesquisa LIVE (sem Enter) --}}
                                <form x-ref="searchForm" method="GET" action="{{ route('admin.utilizadores.index') }}" class="flex items-center gap-2">
                                    <div class="comm-search">
                                        <span class="comm-search-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.3 6.3a7.5 7.5 0 0 0 10.35 10.35Z" />
                                            </svg>
                                        </span>

                                        <input
                                            type="text"
                                            name="q"
                                            x-model="q"
                                            @input="submitLive()"
                                            placeholder="Pesquisar por nome, email, ID ou tipo…"
                                            class="comm-search-input rounded-xl border text-sm text-white placeholder:text-white/45 focus:outline-none focus:ring-2 focus:ring-sky-400/35"
                                            style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.04);"
                                            autocomplete="off"
                                        />
                                    </div>

                                    @if (request('q'))
                                        <a
                                            href="{{ route('admin.utilizadores.index') }}"
                                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-sm font-semibold text-white/80 border transition hover:text-white hover:brightness-110"
                                            style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                                        >
                                            Limpar
                                        </a>
                                    @endif
                                </form>

                                <a
                                    href="{{ route('admin.utilizadores.create') }}"
                                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                           text-white border transition
                                           hover:brightness-110"
                                    style="
                                        border-color: rgba(255,255,255,.12);
                                        background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));
                                    "
                                >
                                    Criar utilizador
                                </a>
                            </div>
                        </div>

                        {{-- Tabela (scroll interno SEMPRE visível) --}}
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
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10" style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10" style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Nome
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10" style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10" style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Tipo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10" style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-white/60 sticky top-0 z-10" style="background: rgba(255,255,255,.04); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y" style="divide-color: rgba(255,255,255,.08);">
                                    @forelse ($utilizadores as $utilizador)
                                        <tr class="transition" style="background: rgba(255,255,255,.02);" onmouseover="this.style.background='rgba(255,255,255,.05)'" onmouseout="this.style.background='rgba(255,255,255,.02)'">
                                            <td class="px-6 py-4 text-sm font-semibold text-white/85">
                                                {{ $utilizador->id }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/85">
                                                {{ $utilizador->nome }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/65">
                                                {{ $utilizador->email }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/75">
                                                <div class="flex items-center gap-2">
                                                    <span>
                                                        {{ $utilizador->tipoUtilizador->nome ?? 'Sem tipo' }}
                                                    </span>

                                                    @if ($utilizador->tipoUtilizador?->nome === 'Administrador')
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border"
                                                              style="border-color: rgba(248,113,113,.35); background: rgba(248,113,113,.12); color: rgba(254,202,202,.95);">
                                                            Admin
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 text-sm">
                                                @php
                                                    $isActive = $utilizador->status === 'ativo';
                                                @endphp

                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                                      style="
                                                            border-color: {{ $isActive ? 'rgba(52,211,153,.30)' : 'rgba(251,191,36,.30)' }};
                                                            background: {{ $isActive ? 'rgba(52,211,153,.10)' : 'rgba(251,191,36,.10)' }};
                                                            color: {{ $isActive ? 'rgba(167,243,208,.95)' : 'rgba(253,230,138,.95)' }};
                                                      ">
                                                    {{ ucfirst($utilizador->status) }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                                <div class="inline-flex items-center gap-4">
                                                    <a
                                                        href="{{ route('admin.utilizadores.edit', $utilizador) }}"
                                                        class="font-semibold hover:underline"
                                                        style="color: rgba(96,165,250,.95);"
                                                    >
                                                        Editar
                                                    </a>

                                                    <button
                                                        type="button"
                                                        @click="
                                                            showDeleteModal = true;
                                                            deleteUrl = '{{ route('admin.utilizadores.destroy', $utilizador) }}';
                                                        "
                                                        class="font-semibold hover:underline disabled:opacity-40 disabled:cursor-not-allowed"
                                                        style="color: rgba(248,113,113,.95);"
                                                        x-bind:disabled="{{ $utilizador->id === auth()->id() ? 'true' : 'false' }}"
                                                        title="{{ $utilizador->id === auth()->id() ? 'Não podes eliminar a tua própria conta.' : 'Eliminar utilizador' }}"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-sm text-white/60">
                                                Nenhum utilizador encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginação --}}
                        <div class="mt-6 text-white/80">
                            {{ $utilizadores->links() }}
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Modal de confirmação --}}
        <div
            x-show="showDeleteModal"
            x-on:keydown.escape.window="showDeleteModal = false"
            style="display: none;"
        >
            <x-confirm-modal
                name="user-delete"
                title="Eliminar utilizador"
                content="Tem a certeza que deseja eliminar este utilizador? Esta ação não pode ser revertida."
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>
    </div>
</x-app-layout>
