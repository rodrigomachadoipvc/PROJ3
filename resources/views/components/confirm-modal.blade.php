@props(['name', 'title' => 'Confirmar ação', 'content' => '', 'method' => 'POST', 'actionRoute' => null])

<div
    x-data="{ show: true }"
    x-show="show"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-50"
    style="display: none;"
>
    {{-- Overlay --}}
    <div
        x-show="show"
        x-transition.opacity
        class="absolute inset-0"
        style="background: rgba(0,0,0,.55);"
        x-on:click="show = false"
    ></div>

    {{-- Modal --}}
    <div
        x-show="show"
        x-transition
        class="relative min-h-screen flex items-center justify-center p-4"
    >
        <div
            class="comm-card rounded-2xl w-full max-w-lg overflow-hidden"
            style="box-shadow: 0 30px 80px rgba(0,0,0,.55);"
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $name }}-title"
        >
            <div class="px-6 pt-6 pb-5 text-white/90">
                <div class="flex items-start gap-4">
                    <div
                        class="shrink-0 flex items-center justify-center h-11 w-11 rounded-2xl border"
                        style="border-color: rgba(248,113,113,.35); background: rgba(248,113,113,.12);"
                    >
                        <svg class="h-6 w-6" style="color: rgba(254,202,202,.95);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86l-8.2 14.2A1.5 1.5 0 003.4 20h17.2a1.5 1.5 0 001.3-2.24l-8.2-14.2a1.5 1.5 0 00-2.6 0z" />
                        </svg>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-white" id="{{ $name }}-title">
                            {{ $title }}
                        </h3>

                        @if(!empty($content))
                            <p class="mt-2 text-sm text-white/65">
                                {{ $content }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 flex flex-col sm:flex-row sm:justify-end gap-3 border-t"
                style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.03);"
            >
                <button
                    type="button"
                    class="inline-flex justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white border transition hover:brightness-110"
                    style="border-color: rgba(248,113,113,.35); background: rgba(248,113,113,.18);"
                    onclick="document.getElementById('{{ $name }}-form').submit();"
                >
                    Sim, eliminar
                </button>

                <button
                    type="button"
                    x-on:click="show = false"
                    class="inline-flex justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white/85 border transition hover:text-white"
                    style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.04);"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Mantém compatibilidade: se actionRoute for passado, usa actionRoute; senão usa deleteUrl --}}
<form
    id="{{ $name }}-form"
    method="POST"
    x-bind:action="{{ $actionRoute ? 'actionRoute' : 'deleteUrl' }}"
    style="display: none;"
>
    @csrf
