<div x-show="showMembrosModal" x-cloak>
    {{-- Overlay (mais forte, para não se ver o conteúdo atrás) --}}
    <div
        x-show="showMembrosModal"
        x-transition.opacity
        class="fixed inset-0 z-50"
        style="
            background: rgba(0,0,0,.72);
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
        "
    ></div>

    {{-- Modal --}}
    <div
        x-show="showMembrosModal"
        x-transition
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="flex items-center justify-center min-h-screen p-4">
            <div
                class="rounded-2xl max-w-2xl w-full overflow-hidden border"
                style="
                    border-color: rgba(255,255,255,.12);
                    background: linear-gradient(180deg, rgba(11,18,32,.97), rgba(7,11,20,.96));
                    box-shadow: 0 30px 90px rgba(0,0,0,.65);
                "
            >
                <div class="px-6 pt-6 pb-5 text-white/90">

                    {{-- Título --}}
                    <div class="pb-4 mb-6 border-b" style="border-color: rgba(255,255,255,.10);">
                        <h3 class="text-xl font-semibold text-white">
                            Gerir membros do grupo
                        </h3>
                        <p class="mt-1 text-sm text-white/65">
                            Grupo: <span class="font-semibold text-white/85" x-text="grupoNome"></span>
                        </p>
                    </div>

                    @php
                        $users = \App\Models\User::orderBy('nome')->get();
                    @endphp

                    {{-- Adicionar membro --}}
                    <div
                        class="mb-8 rounded-2xl border p-5"
                        style="
                            border-color: rgba(255,255,255,.12);
                            background: rgba(11,18,32,.75);
                        "
                    >
                        <h4 class="font-semibold text-white mb-3">
                            Adicionar membro
                        </h4>

                        <form
                            x-bind:action="'/admin/grupos/' + grupoId + '/membros'"
                            method="POST"
                            class="flex flex-col sm:flex-row gap-3"
                        >
                            @csrf

                            <select
                                name="user_id"
                                class="w-full rounded-xl border px-3 py-2 text-sm focus:ring-0"
                                style="
                                    border-color: rgba(255,255,255,.12);
                                    background: rgba(7,11,20,.92);
                                    color: rgba(255,255,255,.92);
                                    color-scheme: dark;
                                "
                            >
                                <option value="" style="background:#0B1220; color: rgba(255,255,255,.90);">
                                    Selecionar utilizador…
                                </option>

                                @foreach ($users as $user)
                                    <option
                                        value="{{ $user->id }}"
                                        style="background:#0B1220; color: rgba(255,255,255,.90);"
                                    >
                                        {{ $user->nome }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>

                            <button
                                type="submit"
                                class="px-4 py-2 rounded-xl text-sm font-semibold text-white border transition hover:brightness-110"
                                style="
                                    border-color: rgba(255,255,255,.12);
                                    background: linear-gradient(180deg, rgba(45,212,191,.22), rgba(96,165,250,.12));
                                "
                            >
                                Adicionar
                            </button>
                        </form>
                    </div>

                    {{-- Lista de membros --}}
                    <div>
                        <h4 class="font-semibold text-white mb-3">
                            Membros atuais
                        </h4>

                        <div
                            class="rounded-2xl border overflow-hidden"
                            style="
                                border-color: rgba(255,255,255,.12);
                                background: rgba(7,11,20,.70);
                            "
                        >
                            {{-- Sem membros --}}
                            <template x-if="membros.length === 0">
                                <div class="p-5 text-sm text-white/65">
                                    Este grupo ainda não tem membros.
                                </div>
                            </template>

                            {{-- Membros --}}
                            <template x-for="m in membros" :key="m.id">
                                <div
                                    class="px-5 py-4 flex justify-between items-center border-t"
                                    style="
                                        border-color: rgba(255,255,255,.10);
                                        background: rgba(255,255,255,.02);
                                    "
                                >
                                    <div>
                                        <p class="font-semibold text-white/90" x-text="m.nome"></p>
                                        <p class="text-sm text-white/60" x-text="m.email"></p>
                                    </div>

                                    <button
                                        @click="
                                            showMembrosModal = false;
                                            showMemberDeleteModal = true;
                                            deleteUrl = `/admin/grupos/${grupoId}/membros/${m.id}`;
                                            memberName = m.nome;
                                        "
                                        class="text-sm font-semibold hover:underline"
                                        style="color: rgba(248,113,113,.95);"
                                    >
                                        Remover
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 flex justify-end border-t"
                    style="
                        border-color: rgba(255,255,255,.10);
                        background: rgba(11,18,32,.78);
                    "
                >
                    <button
                        @click="showMembrosModal = false"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white/85 border transition hover:text-white"
                        style="
                            border-color: rgba(255,255,255,.12);
                            background: rgba(255,255,255,.05);
                        "
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
