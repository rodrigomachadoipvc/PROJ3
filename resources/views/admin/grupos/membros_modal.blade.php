<div x-show="showMembrosModal" x-cloak>

    {{-- Overlay --}}
    <div
        x-show="showMembrosModal"
        x-transition.opacity
        class="fixed inset-0 z-50 bg-gray-800 bg-opacity-60"
    ></div>

    {{-- Modal --}}
    <div
        x-show="showMembrosModal"
        x-transition
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">

                {{-- Conteúdo --}}
                <div class="px-6 pt-6 pb-4">

                    {{-- Título --}}
                    <div class="border-b pb-4 mb-6">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Gerir membros do grupo
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Grupo: <span class="font-medium" x-text="grupoNome"></span>
                        </p>
                    </div>

                    @php
                        $users = \App\Models\User::orderBy('nome')->get();
                    @endphp

                    {{-- Adicionar membro --}}
                    <div class="mb-8 p-4 rounded-lg bg-gray-50 border">
                        <h4 class="font-semibold text-gray-800 mb-3">
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
                                class="rounded-md border-gray-300 shadow-sm w-full
                                       focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Selecionar utilizador…</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->nome }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>

                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white
                                       rounded-md hover:bg-indigo-700 transition"
                            >
                                Adicionar
                            </button>
                        </form>
                    </div>

                    {{-- Lista de membros --}}
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">
                            Membros atuais
                        </h4>

                        <ul class="divide-y divide-gray-200 border rounded-lg">

                            {{-- Sem membros --}}
                            <template x-if="membros.length === 0">
                                <li class="p-4 text-sm text-gray-600">
                                    Este grupo ainda não tem membros.
                                </li>
                            </template>

                            {{-- Membros --}}
                            <template x-for="m in membros" :key="m.id">
                                <li class="p-4 flex justify-between items-center">

                                    <div>
                                        <p class="font-medium text-gray-900" x-text="m.nome"></p>
                                        <p class="text-sm text-gray-500" x-text="m.email"></p>
                                    </div>

                                    <button
                                        @click="
                                            showMembrosModal = false;
                                            showMemberDeleteModal = true;
                                            deleteUrl = `/admin/grupos/${grupoId}/membros/${m.id}`;
                                            memberName = m.nome;
                                        "
                                        class="text-sm font-medium text-red-600 hover:text-red-800"
                                    >
                                        Remover
                                    </button>

                                </li>
                            </template>

                        </ul>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button
                        @click="showMembrosModal = false"
                        class="px-4 py-2 bg-white border border-gray-300
                               rounded-md hover:bg-gray-100 transition"
                    >
                        Fechar
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
