<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Nova resposta automática
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
                            Configuração da resposta automática
                        </h3>
                        <p class="mt-2 text-sm text-white/65">
                            Define as regras e o conteúdo da resposta automática.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('admin.auto-replies.store') }}" class="space-y-6">
                        @csrf

                        {{-- Disparador --}}
                        @php
                            $suffix = old('trigger_suffix', '');
                        @endphp

                        <div x-data="{ suffix: @js($suffix) }">
                            <x-input-label :value="__('Disparador')" class="text-white/80" />

                            <div
                                class="mt-1 flex items-center rounded-xl border overflow-hidden"
                                style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                            >
                                <span
                                    class="px-3 py-2 text-sm font-semibold whitespace-nowrap"
                                    style="color: rgba(255,255,255,.85); border-right: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.02);"
                                >
                                    @sistema
                                </span>

                                <input
                                    type="text"
                                    x-model="suffix"
                                    name="trigger_suffix"
                                    class="w-full px-3 py-2 text-sm text-white placeholder-white/40 focus:outline-none"
                                    style="background: transparent;"
                                    placeholder="Ex.: horário, ajuda, contacto"
                                />
                            </div>

                            {{-- Valor real guardado --}}
                            <input
                                type="hidden"
                                name="trigger"
                                :value="suffix.trim() ? ('@sistema ' + suffix.trim()) : '@sistema'"
                            />

                            <x-input-error :messages="$errors->get('trigger')" class="mt-2" />
                        </div>

                        {{-- Resposta --}}
                        <div>
                            <x-input-label for="response" :value="__('Resposta automática')" class="text-white/80" />
                            <textarea
                                id="response"
                                name="response"
                                rows="4"
                                class="mt-1 block w-full rounded-xl border bg-white/5 text-white
                                       placeholder-white/40 focus:ring-0 focus:border-white/20"
                                style="border-color: rgba(255,255,255,.10);"
                                placeholder="Texto enviado automaticamente quando o disparador for detetado"
                            >{{ old('response') }}</textarea>
                            <x-input-error :messages="$errors->get('response')" class="mt-2" />
                        </div>

                        <div class="border-t pt-6" style="border-color: rgba(255,255,255,.10);"></div>

                        {{-- Prioridade e estado --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="priority" :value="__('Prioridade')" class="text-white/80" />
                                <x-text-input
                                    id="priority"
                                    type="number"
                                    name="priority"
                                    :value="old('priority', 0)"
                                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                                />
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-3 sm:pt-7">
                                <input
                                    id="active"
                                    type="checkbox"
                                    name="active"
                                    value="1"
                                    checked
                                    class="h-4 w-4 rounded border-white/20 bg-transparent focus:ring-0"
                                    style="accent-color: rgb(45, 212, 191);"
                                >
                                <label for="active" class="text-sm font-semibold text-white/80">
                                    Ativa
                                </label>
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center justify-end gap-4 pt-2">
                            <a
                                href="{{ route('admin.auto-replies.index') }}"
                                class="text-sm font-semibold text-white/70 hover:text-white hover:underline"
                            >
                                Cancelar
                            </a>

                            <x-primary-button>
                                Guardar
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
