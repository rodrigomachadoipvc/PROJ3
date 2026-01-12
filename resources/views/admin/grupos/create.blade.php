<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Criar novo grupo de chat
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 text-white/90">

                    <div class="mb-8">
                        <div class="text-xs tracking-wide text-white/55 uppercase">
                            Administração
                        </div>
                        <h3 class="mt-1 text-2xl font-semibold text-white">
                            Detalhes do grupo
                        </h3>
                        <p class="mt-2 text-sm text-white/65">
                            Define as informações base do novo grupo de chat.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.grupos.store') }}" class="space-y-8">
                        @csrf

                        {{-- Nome --}}
                        <div>
                            <x-input-label for="nome" :value="__('Nome do grupo')" class="text-white/80" />
                            <x-text-input
                                id="nome"
                                type="text"
                                name="nome"
                                :value="old('nome')"
                                required
                                autofocus
                                class="mt-1 block w-full bg-white/5 text-white border-white/10
                                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                                placeholder="Ex.: Projeto Alpha, Equipa Comercial"
                            />
                            <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                        </div>

                        {{-- Descrição --}}
                        <div>
                            <x-input-label for="descricao" :value="__('Descrição (opcional)')" class="text-white/80" />
                            <textarea
                                id="descricao"
                                name="descricao"
                                rows="4"
                                class="mt-1 block w-full rounded-xl border bg-white/5 text-white
                                       placeholder-white/40 focus:ring-0 focus:border-white/20"
                                style="border-color: rgba(255,255,255,.10);"
                                placeholder="Breve descrição do objetivo do grupo"
                            >{{ old('descricao') }}</textarea>
                            <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-2">
                            <a
                                href="{{ route('admin.grupos.index') }}"
                                class="text-sm font-semibold text-white/70 hover:text-white hover:underline"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar grupo
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
