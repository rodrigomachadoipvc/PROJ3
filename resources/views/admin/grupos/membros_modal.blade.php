<div x-show="showMembrosModal" x-cloak>

    {{-- Overlay --}}
    <div x-show="showMembrosModal" x-transition.opacity 
         class="fixed inset-0 z-50 bg-gray-500 bg-opacity-75"></div>

    {{-- Modal --}}
    <div x-show="showMembrosModal" x-transition 
         class="fixed inset-0 z-50 overflow-y-auto">

        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">

                <div class="px-4 pt-5 pb-4 sm:p-6">

                    <h3 class="text-xl font-medium text-gray-900 border-b pb-3">
                        Gerir Membros do Grupo: <span x-text="grupoNome"></span>
                    </h3>

                    @php
                        $users = \App\Models\User::orderBy('nome')->get();
                    @endphp

                    {{-- Adicionar membro --}}
                    <div class="mt-4 border p-4 rounded-lg bg-gray-50">
                        <h4 class="font-semibold mb-2">Adicionar Membro</h4>

                        <form x-bind:action="'/admin/grupos/' + grupoId + '/membros'"
                              method="POST"
                              class="flex space-x-3">
                            @csrf

                            <select name="user_id" class="rounded-md border-gray-300 shadow-sm w-full">
                                <option value="">Selecione...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->nome }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>

                            <button class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Adicionar
                            </button>
                        </form>
                    </div>

                    {{-- Lista de membros --}}
                    <div class="mt-6">
                        <h4 class="font-semibold mb-2">Membros Atuais</h4>

                        <ul class="divide-y divide-gray-200 border rounded-lg">

                            {{-- Sem membros --}}
                            <template x-if="membros.length === 0">
                                <li class="p-3 text-gray-600">Nenhum membro neste grupo.</li>
                            </template>

                            {{-- Membros --}}
                            <template x-for="m in membros" :key="m.id">
                                <li class="p-3 flex justify-between items-center">

                                    <div>
                                        <p class="font-medium" x-text="m.nome"></p>
                                        <p class="text-sm text-gray-500" x-text="m.email"></p>
                                    </div>

                                    <button
                                        @click="
                                            showMembrosModal = false;
                                            showMemberDeleteModal = true;
                                            deleteUrl = `/admin/grupos/${grupoId}/membros/${m.id}`;
                                            memberName = m.nome;
                                        "
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        Remover
                                    </button>

                                </li>
                            </template>

                        </ul>

                    </div>

                </div>

                {{-- Footer --}}
                <div class="bg-gray-50 px-4 py-3 flex justify-end">
                    <button 
                        @click="showMembrosModal = false"
                        class="px-4 py-2 bg-white border rounded hover:bg-gray-50">
                        Fechar
                    </button>
                </div>

            </div>
        </div>

    </div>
</div>
