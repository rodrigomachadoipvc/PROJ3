@php
    $allUsers = $allUsers ?? \App\Models\User::orderBy('nome')->get();

    $usersPayload = $allUsers->map(fn ($u) => [
        'id'    => $u->id,
        'nome'  => $u->nome,
        'email' => $u->email,
    ])->values();
@endphp

<div
    x-show="showMembrosModal"
    x-transition.opacity
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    aria-modal="true"
    role="dialog"
>
    {{-- Overlay --}}
    <div
        class="absolute inset-0"
        style="background: rgba(0,0,0,.65); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
        @click="showMembrosModal = false"
    ></div>

    {{-- Modal --}}
    <div
        class="relative w-full max-w-3xl rounded-2xl border overflow-hidden"
        style="
            border-color: rgba(255,255,255,.12);
            background: radial-gradient(1200px 600px at 70% 10%, rgba(45,212,191,.10), transparent 60%),
                        radial-gradient(900px 500px at 25% 40%, rgba(96,165,250,.10), transparent 55%),
                        rgba(10,15,24,.92);
            box-shadow: 0 30px 80px rgba(0,0,0,.55);
        "
        x-data="{
            users: @js($usersPayload),
            query: '',
            open: false,
            selectedId: null,
            selectedLabel: '',
            maxResults: 50,

            init() {
                this.$watch('grupoId', () => {
                    this.query = '';
                    this.open = false;
                    this.selectedId = null;
                    this.selectedLabel = '';
                });
            },

            inGroup(userId) {
                try {
                    return (membros || []).some(m => Number(m.id) === Number(userId));
                } catch(e) {
                    return false;
                }
            },

            filtered() {
                const q = (this.query || '').trim().toLowerCase();
                if (q.length < 1) return [];

                return this.users
                    .filter(u => !this.inGroup(u.id))
                    .filter(u => {
                        const nome = (u.nome || '').toLowerCase();
                        const email = (u.email || '').toLowerCase();
                        const id = String(u.id || '');
                        return nome.includes(q) || email.includes(q) || id.includes(q);
                    })
                    .slice(0, this.maxResults);
            },

            selectUser(u) {
                this.selectedId = u.id;
                this.selectedLabel = `${u.nome} (${u.email})`;
                this.query = this.selectedLabel;
                this.open = false;
            },

            clearSelection() {
                this.selectedId = null;
                this.selectedLabel = '';
                this.query = '';
                this.open = false;
            }
        }"
    >
        {{-- Header --}}
        <div class="px-6 py-5 border-b" style="border-color: rgba(255,255,255,.10);">
            <h3 class="text-xl font-semibold text-white leading-tight">
                Gerir membros do grupo
            </h3>
            <p class="mt-1 text-sm text-white/65 leading-snug">
                Grupo: <span class="font-semibold text-white/85" x-text="grupoNome"></span>
            </p>
        </div>

        {{-- Body --}}
        <div class="px-6 py-6 space-y-6">

            {{-- Adicionar membro --}}
            <div class="rounded-2xl border p-5" style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h4 class="text-base font-semibold text-white">Adicionar membro</h4>
                        <p class="mt-1 text-sm text-white/60">
                            Pesquise por nome, email ou ID e seleccione o utilizador.
                        </p>
                    </div>
                </div>

                <form
                    method="POST"
                    class="mt-4"
                    x-bind:action="'/admin/grupos/' + grupoId + '/membros'"
                >
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-start">

                        {{-- Pesquisa + limpar + resultados --}}
                        <div class="relative" @click.outside="open = false">
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3" style="color: rgba(255,255,255,.55);">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.3 6.3a7.5 7.5 0 0 0 10.35 10.35Z" />
                                        </svg>
                                    </span>

                                    <input
                                        type="text"
                                        class="w-full rounded-xl border px-10 py-2.5 text-sm text-white placeholder:text-white/45 focus:outline-none focus:ring-2 focus:ring-sky-400/35"
                                        style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.04);"
                                        placeholder="Procurar utilizador por nome, email ou ID…"
                                        x-model="query"
                                        @focus="open = true"
                                        @input="open = true"
                                        @keydown.escape.prevent="open = false"
                                        @keydown.enter.prevent="
                                            const r = filtered();
                                            if (r.length) selectUser(r[0]);
                                        "
                                        autocomplete="off"
                                    />
                                </div>

                                <button
                                    type="button"
                                    x-show="(query || '').trim().length"
                                    x-cloak
                                    class="h-[42px] inline-flex items-center justify-center rounded-xl px-3 text-xs font-semibold border text-white/80 hover:text-white hover:brightness-110"
                                    style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                                    @click="clearSelection()"
                                >
                                    Limpar
                                </button>
                            </div>

                            <div x-show="selectedId" x-cloak class="mt-2 text-xs text-white/70">
                                Seleccionado: <span class="font-semibold text-white/85" x-text="selectedLabel"></span>
                            </div>

                            <div
                                x-show="open && (query || '').trim().length >= 1"
                                x-cloak
                                class="mt-3 rounded-2xl border overflow-hidden"
                                style="border-color: rgba(255,255,255,.12); background: rgba(10,15,24,.92);"
                            >
                                <div
                                    class="divide-y"
                                    style="divide-color: rgba(255,255,255,.08); max-height: 240px; overflow-y: auto;"
                                >
                                    <div
                                        x-show="filtered().length === 0"
                                        class="px-4 py-3 text-sm text-white/60"
                                    >
                                        Sem resultados para a pesquisa atual.
                                    </div>

                                    <template x-for="u in filtered()" :key="u.id">
                                        <button
                                            type="button"
                                            class="w-full text-left px-4 py-3 transition"
                                            style="background: rgba(255,255,255,.02);"
                                            @click="selectUser(u)"
                                            @mouseover="($el.style.background='rgba(255,255,255,.06)')"
                                            @mouseout="($el.style.background='rgba(255,255,255,.02)')"
                                        >
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-semibold text-white/90 truncate" x-text="u.nome"></div>
                                                    <div class="text-xs text-white/60 truncate" x-text="u.email"></div>
                                                </div>

                                                <span class="shrink-0 inline-flex items-center justify-center rounded-full px-3 py-1 text-xs font-semibold border"
                                                      style="border-color: rgba(45,212,191,.25); background: rgba(45,212,191,.10); color: rgba(153,246,228,.95);">
                                                    Selecionar
                                                </span>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <input type="hidden" name="user_id" x-bind:value="selectedId" required>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-semibold text-white border transition hover:brightness-110 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="
                                border-color: rgba(255,255,255,.12);
                                background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));
                            "
                            x-bind:disabled="!selectedId"
                            title="Seleciona um utilizador para adicionar"
                        >
                            Adicionar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Membros atuais --}}
            <div>
                <h4 class="text-base font-semibold text-white">Membros atuais</h4>

                <div class="mt-3 rounded-2xl border overflow-hidden"
                     style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);">

                    <div
                        class="divide-y"
                        style="divide-color: rgba(255,255,255,.08); height: 270px; overflow-y: auto;"
                    >
                        <template x-if="!membros || membros.length === 0">
                            <div class="px-5 py-6 text-sm text-white/60">
                                Sem membros neste grupo.
                            </div>
                        </template>

                        <template x-for="m in membros" :key="m.id">
                            <div class="px-5 py-4 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="text-sm font-semibold text-white/90 truncate" x-text="m.nome"></div>
                                    <div class="text-xs text-white/60 truncate" x-text="m.email"></div>
                                </div>

                                <button
                                    type="button"
                                    class="text-sm font-semibold hover:underline"
                                    style="color: rgba(248,113,113,.95);"
                                    @click="
                                        memberName = m.nome;
                                        deleteUrl = '/admin/grupos/' + grupoId + '/membros/' + m.id;
                                        showMemberDeleteModal = true;
                                    "
                                >
                                    Remover
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t flex justify-end" style="border-color: rgba(255,255,255,.10);">
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white/85 border transition hover:text-white hover:brightness-110"
                style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                @click="showMembrosModal = false"
            >
                Fechar
            </button>
        </div>
    </div>
</div>
