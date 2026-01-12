<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Editar utilizador
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 text-white/90">

                    {{-- Título --}}
                    <div class="mb-8 flex items-start justify-between gap-6">
                        <div>
                            <div class="text-xs tracking-wide text-white/55 uppercase">
                                Administração
                            </div>
                            <h3 class="mt-1 text-2xl font-semibold text-white">
                                {{ $utilizador->nome }}
                            </h3>
                            <p class="mt-2 text-sm text-white/65">
                                Atualiza os dados da conta, tipo de utilizador e estado.
                            </p>
                        </div>

                        @php $isActive = $utilizador->status === 'ativo'; @endphp
                        <div class="shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border"
                                  style="
                                        border-color: {{ $isActive ? 'rgba(52,211,153,.30)' : 'rgba(251,191,36,.30)' }};
                                        background: {{ $isActive ? 'rgba(52,211,153,.10)' : 'rgba(251,191,36,.10)' }};
                                        color: {{ $isActive ? 'rgba(167,243,208,.95)' : 'rgba(253,230,138,.95)' }};
                                  ">
                                {{ $isActive ? 'Conta ativa' : 'Conta inativa' }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.utilizadores.update', $utilizador->id) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        {{-- Dados base --}}
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-4">
                                Dados do utilizador
                            </h4>

                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="nome" :value="__('Nome completo')" class="text-white/80" />
                                    <x-text-input
                                        id="nome"
                                        type="text"
                                        name="nome"
                                        :value="old('nome', $utilizador->nome)"
                                        required
                                        autofocus
                                        class="mt-1 block w-full bg-white/5 text-white border-white/10
                                               placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                                    />
                                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Endereço de email')" class="text-white/80" />
                                    <x-text-input
                                        id="email"
                                        type="email"
                                        name="email"
                                        :value="old('email', $utilizador->email)"
                                        required
                                        class="mt-1 block w-full bg-white/5 text-white border-white/10
                                               placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                                    />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="border-t" style="border-color: rgba(255,255,255,.10);"></div>

                        {{-- Alterar password --}}
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-2">
                                Alterar password
                            </h4>
                            <p class="text-sm text-white/65 mb-5">
                                Preenche apenas se quiseres alterar a password atual.
                            </p>

                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="password" :value="__('Nova password')" class="text-white/80" />
                                    <x-text-input
                                        id="password"
                                        type="password"
                                        name="password"
                                        autocomplete="new-password"
                                        class="mt-1 block w-full bg-white/5 text-white border-white/10
                                               placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                                    />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirmar nova password')" class="text-white/80" />
                                    <x-text-input
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        class="mt-1 block w-full bg-white/5 text-white border-white/10
                                               placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                                    />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="border-t" style="border-color: rgba(255,255,255,.10);"></div>

                        {{-- Tipo e estado --}}
                        <div>
                            <h4 class="text-lg font-semibold text-white mb-4">
                                Permissões e estado
                            </h4>

                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="tipo_utilizador_id" :value="__('Tipo de utilizador')" class="text-white/80" />

                                    <select
                                        id="tipo_utilizador_id"
                                        name="tipo_utilizador_id"
                                        required
                                        class="mt-1 block w-full rounded-xl border bg-white/5 text-white
                                               focus:ring-0 focus:border-white/20"
                                        style="border-color: rgba(255,255,255,.10);"
                                    >
                                        <option value="" class="bg-slate-900">Selecionar tipo</option>
                                        @foreach ($tiposUtilizador as $tipo)
                                            <option
                                                value="{{ $tipo->id }}"
                                                class="bg-slate-900"
                                                {{ old('tipo_utilizador_id', $utilizador->tipo_utilizador_id) == $tipo->id ? 'selected' : '' }}
                                            >
                                                {{ $tipo->nome }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <x-input-error :messages="$errors->get('tipo_utilizador_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="status" :value="__('Estado da conta')" class="text-white/80" />

                                    <select
                                        id="status"
                                        name="status"
                                        required
                                        class="mt-1 block w-full rounded-xl border bg-white/5 text-white
                                               focus:ring-0 focus:border-white/20"
                                        style="border-color: rgba(255,255,255,.10);"
                                    >
                                        <option value="ativo" class="bg-slate-900" {{ old('status', $utilizador->status) === 'ativo' ? 'selected' : '' }}>
                                            Ativo
                                        </option>
                                        <option value="inativo" class="bg-slate-900" {{ old('status', $utilizador->status) === 'inativo' ? 'selected' : '' }}>
                                            Inativo
                                        </option>
                                    </select>

                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center justify-end gap-4 pt-2">
                            <a
                                href="{{ route('admin.utilizadores.index') }}"
                                class="text-sm font-semibold text-white/70 hover:text-white hover:underline"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar alterações
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
