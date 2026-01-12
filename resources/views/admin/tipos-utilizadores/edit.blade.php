<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Editar tipo de utilizador
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 text-white/90">

                    {{-- Título --}}
                    <div class="mb-8 flex items-start justify-between gap-6">
                        <div>
                            <div class="text-xs tracking-wide text-white/55 uppercase">
                                Administração
                            </div>
                            <h3 class="mt-1 text-2xl font-semibold text-white">
                                {{ $tipo->nome }}
                            </h3>
                            <p class="mt-2 text-sm text-white/65">
                                Atualiza os dados e permissões associadas a este tipo de utilizador.
                            </p>
                        </div>

                        @if ($tipo->nome === 'Administrador')
                            <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border"
                                  style="border-color: rgba(248,113,113,.35); background: rgba(248,113,113,.12); color: rgba(254,202,202,.95);">
                                Tipo base
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.tipos-utilizadores.update', $tipo) }}" class="space-y-8">
                        @csrf
                        @method('PUT')

                        {{-- Nome --}}
                        <div>
                            <x-input-label for="nome" :value="__('Nome do tipo')" class="text-white/80" />

                            <x-text-input
                                id="nome"
                                type="text"
                                name="nome"
                                :value="old('nome', $tipo->nome)"
                                required
                                autofocus
                                class="mt-1 block w-full bg-white/5 text-white border-white/10
                                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                            />

                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        {{-- Descrição --}}
                        <div>
                            <x-input-label for="descricao" :value="__('Descrição')" class="text-white/80" />

                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="3"
                                class="mt-1 block w-full rounded-xl border bg-white/5 text-white
                                       placeholder-white/40 focus:ring-0 focus:border-white/20"
                                style="border-color: rgba(255,255,255,.10);"
                            >{{ old('descricao', $tipo->descricao) }}</textarea>

                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <div class="border-t" style="border-color: rgba(255,255,255,.10);"></div>

                        {{-- Permissões --}}
                        <div>
                            <div class="flex items-end justify-between gap-6 mb-3">
                                <div>
                                    <div class="text-sm font-semibold text-white">
                                        Permissões associadas
                                    </div>
                                    <p class="mt-1 text-sm text-white/65">
                                        Seleciona as permissões que este tipo de utilizador deverá possuir.
                                    </p>
                                </div>

                                <div class="hidden sm:block text-xs text-white/55">
                                    Dica: passa o rato para ver a descrição
                                </div>
                            </div>

                            @error('permissoes')
                                <div class="mb-4 rounded-xl border border-red-400/25 bg-red-400/10 px-4 py-3 text-sm text-red-100">
                                    Seleciona pelo menos uma permissão válida.
                                </div>
                            @enderror

                            <div
                                class="rounded-2xl border p-4 sm:p-5 overflow-y-auto h-96"
                                style="
                                    border-color: rgba(255,255,255,.10);
                                    background: rgba(255,255,255,.03);
                                "
                            >
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    @forelse ($permissoes as $permissao)
                                        <label
                                            for="permissao_{{ $permissao->id }}"
                                            class="group flex items-center gap-3 rounded-xl border px-3 py-2 cursor-pointer select-none transition"
                                            style="border-color: rgba(255,255,255,.08); background: rgba(255,255,255,.02);"
                                            title="{{ $permissao->descricao }}"
                                        >
                                            <input
                                                id="permissao_{{ $permissao->id }}"
                                                name="permissoes[]"
                                                type="checkbox"
                                                value="{{ $permissao->id }}"
                                                class="h-4 w-4 rounded border-white/20 bg-transparent text-emerald-300 focus:ring-0"
                                                style="accent-color: rgb(45, 212, 191);"
                                                {{ in_array($permissao->id, old('permissoes', $permissoesAtuais)) ? 'checked' : '' }}
                                            >

                                            <span class="text-sm font-medium text-white/80 group-hover:text-white transition">
                                                {{ $permissao->nome }}
                                            </span>
                                        </label>
                                    @empty
                                        <div class="col-span-4 rounded-xl border px-4 py-6 text-center text-sm text-white/60"
                                             style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.02);">
                                            Não existem permissões definidas.
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <p class="mt-2 text-xs text-white/55 sm:hidden">
                                Passa o rato sobre o nome da permissão para ver a descrição detalhada.
                            </p>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center justify-end gap-4 pt-2">
                            <a
                                href="{{ route('admin.tipos-utilizadores.index') }}"
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
