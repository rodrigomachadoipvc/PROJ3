<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Criar utilizador
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 text-white/90">

                    {{-- Título --}}
                    <div class="mb-8">
                        <div class="text-xs tracking-wide text-white/55 uppercase">
                            Administração
                        </div>
                        <h3 class="mt-1 text-2xl font-semibold text-white">
                            Detalhes do novo utilizador
                        </h3>
                        <p class="mt-2 text-sm text-white/65">
                            Preenche os dados base, define o tipo de utilizador e o estado inicial da conta.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.utilizadores.store') }}" class="space-y-6">
                        @csrf

                        {{-- Nome --}}
                        <div>
                            <x-input-label for="nome" :value="__('Nome completo')" class="text-white/80" />
                            <x-text-input
                                id="nome"
                                type="text"
                                name="nome"
                                :value="old('nome')"
                                required
                                autofocus
                                class="mt-1 block w-full bg-white/5 text-white border-white/10
                                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                            />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Endereço de email')" class="text-white/80" />
                            <x-text-input
                                id="email"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                class="mt-1 block w-full bg-white/5 text-white border-white/10
                                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Password --}}
                        <div>
                            <x-input-label for="password" :value="__('Password')" class="text-white/80" />
                            <p class="mt-1 text-xs text-white/55">
                                Mínimo 8 caracteres, incluindo uma letra maiúscula, um número e um símbolo.
                            </p>

                            <x-text-input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="mt-2 block w-full bg-white/5 text-white border-white/10
                                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Confirmar Password --}}
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar password')" class="text-white/80" />
                            <x-text-input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                class="mt-1 block w-full bg-white/5 text-white border-white/10
                                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="border-t pt-6" style="border-color: rgba(255,255,255,.10);"></div>

                        {{-- Tipo de utilizador --}}
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
                                        {{ old('tipo_utilizador_id') == $tipo->id ? 'selected' : '' }}
                                    >
                                        {{ $tipo->nome }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('tipo_utilizador_id')" class="mt-2" />
                        </div>

                        {{-- Estado --}}
                        <div>
                            <x-input-label for="status" :value="__('Estado inicial da conta')" class="text-white/80" />

                            <select
                                id="status"
                                name="status"
                                required
                                class="mt-1 block w-full rounded-xl border bg-white/5 text-white
                                       focus:ring-0 focus:border-white/20"
                                style="border-color: rgba(255,255,255,.10);"
                            >
                                <option value="ativo" class="bg-slate-900" {{ old('status', 'ativo') === 'ativo' ? 'selected' : '' }}>
                                    Ativo
                                </option>
                                <option value="inativo" class="bg-slate-900" {{ old('status') === 'inativo' ? 'selected' : '' }}>
                                    Inativo
                                </option>
                            </select>

                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <a
                                href="{{ route('admin.utilizadores.index') }}"
                                class="text-sm font-semibold text-white/70 hover:text-white hover:underline"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar utilizador
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
