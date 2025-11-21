<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestão de Grupos de Chat') }}
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

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        {{-- Flash messages --}}
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50">
                                {{ session('error') }}
                            </div>
                        @endif


                        {{-- Header --}}
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold">
                                {{ __('Grupos Criados') }} ({{ $grupos->total() }})
                            </h3>

                            <a href="{{ route('admin.grupos.create') }}"
                               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Criar Novo Grupo') }}
                            </a>
                        </div>


                        {{-- Tabela --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium">Criador</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium">Membros</th>
                                        <th class="px-6 py-3"></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($grupos as $grupo)
                                        <tr>
                                            <td class="px-6 py-4">{{ $grupo->id }}</td>
                                            <td class="px-6 py-4">{{ $grupo->nome }}</td>
                                            <td class="px-6 py-4">{{ $grupo->descricao ?? '--' }}</td>
                                            <td class="px-6 py-4">{{ $grupo->criador->nome ?? '--' }}</td>
                                            <td class="px-6 py-4">{{ $grupo->membros->count() }} Membros</td>

                                            <td class="px-6 py-4 text-right space-x-3">

                                                {{-- Gerir Membros --}}
                                                <button
                                                    @click="
                                                        membros = [];             
                                                        grupoId = {{ $grupo->id }};
                                                        grupoNome = '{{ $grupo->nome }}';

                                                        // 1. Primeiro carregamos os membros
                                                        fetch(`/admin/grupos/${grupoId}/membros-json`)
                                                            .then(res => res.json())
                                                            .then(data => {
                                                                membros = data;

                                                                // 2. Só agora abrimos o modal, com a lista já carregada
                                                                showMembrosModal = true;
                                                            });
                                                    "
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Gerir Membros
                                                </button>

                                                {{-- Editar --}}
                                                <a href="{{ route('admin.grupos.edit', $grupo) }}"
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    Editar
                                                </a>

                                                {{-- Eliminar --}}
                                                <button
                                                    @click="
                                                        showDeleteModal = true;
                                                        deleteUrl = '{{ route('admin.grupos.destroy', $grupo) }}';
                                                    "
                                                    class="text-red-600 hover:text-red-900">
                                                    Eliminar
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                                Nenhum grupo encontrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                        <div class="mt-4">{{ $grupos->links() }}</div>

                    </div>
                </div>
            </div>
        </div>


        {{-- Modal eliminar grupo --}}
        <div x-show="showDeleteModal" style="display:none;">
            <x-confirm-modal
                name="group-delete"
                title="Eliminar Grupo"
                content="Tem a certeza que deseja eliminar este grupo?"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>


        {{-- Modal remover membro --}}
        <div x-show="showMemberDeleteModal" style="display:none;">
            <x-confirm-modal
                name="member-delete"
                title="Remover Membro"
                x-bind:content="'Tem a certeza que deseja remover ' + memberName + ' deste grupo?'"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>


        {{-- Inclui o modal de membros --}}
        @include('admin.grupos.membros_modal')

    </div>
</x-app-layout>
