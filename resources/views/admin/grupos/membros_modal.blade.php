<div x-show="showMembrosModal" x-cloak>
    
    {{-- Background overlay --}}
    <div x-show="showMembrosModal" x-transition:opacity
         class="fixed inset-0 z-50 bg-gray-500 bg-opacity-75 transition-opacity" 
         aria-hidden="true"></div>

    {{-- Modal content --}}
    <div x-show="showMembrosModal" x-transition 
         class="fixed inset-0 z-50 overflow-y-auto">
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl transform transition-all max-w-2xl w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-xl font-medium text-gray-900 border-b pb-3">
                        {{ __('Gerir Membros do Grupo') }}: <span x-text="grupoNome"></span>
                    </h3>

                    @php
                        // Carrega todos os utilizadores (necessário para o formulário de Adição)
                        $users = \App\Models\User::orderBy('nome')->get();
                    @endphp

                    {{-- Formulário de Adição (POST) --}}
                    <div class="mt-4 border p-4 rounded-lg bg-gray-50">
                        <h4 class="font-semibold mb-2">{{ __('Adicionar Novo Membro') }}</h4>
                        
                        {{-- O action é dinâmico: usa x-bind:action e a variável grupoId --}}
                        <form x-bind:action="'{{ url('admin/grupos') }}' + '/' + grupoId + '/membros'" method="POST" class="flex space-x-3">
                            @csrf
                            
                            <select name="user_id" class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">{{ __('Selecione um Utilizador...') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->nome }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                {{ __('Adicionar') }}
                            </button>
                        </form>
                    </div>

                    {{-- Lista de Membros Atuais (Para Remover) --}}
                    <div class="mt-6">
                        <h4 class="font-semibold mb-2">{{ __('Membros Atuais') }}</h4>
                        <ul class="divide-y divide-gray-200 border rounded-lg">
                            @if (isset($grupo))
                                @forelse ($grupo->membros as $membro)
                                    <li class="p-3 flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">{{ $membro->nome }}</p>
                                            <p class="text-sm text-gray-500">{{ $membro->email }}</p>
                                        </div>
                                        
                                        {{-- [MODIFICADO] Botão para abrir o Modal de Confirmação --}}
                                        <button 
                                            @click="showMembrosModal = false; showMemberDeleteModal = true; memberDeleteUrl = '{{ route('admin.grupos.membros.destroy', ['grupo' => $grupo->id, 'user' => $membro->id]) }}'; memberName = '{{ $membro->nome }}'"
                                            type="button"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium ml-4">
                                            {{ __('Remover') }}
                                        </button>
                                    </li>
                                @empty
                                    <li class="p-3 text-gray-500">{{ __('Este grupo ainda não tem membros.') }}</li>
                                @endforelse
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button @click="showMembrosModal = false" type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Fechar') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>