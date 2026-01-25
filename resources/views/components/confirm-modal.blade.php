{{-- resources/views/components/confirm-modal.blade.php --}}
@props([
    'name',
    'title' => 'Confirmar ação',
    'content' => '',
    'method' => 'POST',
    'actionRoute' => null,
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
])

@php
    $httpMethod = strtoupper($method ?? 'POST');
@endphp

<div
    x-data="{
        open: true,
        actionStatic: @js($actionRoute),

        close() {
            this.open = false;

            // Fecha também os wrappers mais usados (evita ficar preso em TRUE e não reabrir)
            try { if (typeof showDeleteModal !== 'undefined') showDeleteModal = false; } catch (e) {}
            try { if (typeof showMemberDeleteModal !== 'undefined') showMemberDeleteModal = false; } catch (e) {}
            try { if (typeof showRemoveModal !== 'undefined') showRemoveModal = false; } catch (e) {}
            try { if (typeof showConfirmModal !== 'undefined') showConfirmModal = false; } catch (e) {}
            try { if (typeof showModal !== 'undefined') showModal = false; } catch (e) {}
        },

        action() {
            // 1) rota estática passada como prop (Blade)
            if (this.actionStatic) return this.actionStatic;

            // 2) variável Alpine (se existir)
            try {
                if (typeof actionRoute !== 'undefined' && actionRoute) return actionRoute;
            } catch (e) {}

            // 3) fallback comum usado nas páginas (deleteUrl)
            try {
                if (typeof deleteUrl !== 'undefined' && deleteUrl) return deleteUrl;
            } catch (e) {}

            return '';
        }
    }"
    x-show="open"
    x-cloak
    x-on:keydown.escape.window="close()"
    class="fixed inset-0 z-[9999]"
    style="display: none;"
>
    {{-- Overlay --}}
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0"
        style="background: rgba(0,0,0,.65); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);"
        x-on:click="close()"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition
        class="relative min-h-screen flex items-center justify-center p-4"
    >
        <div
            class="rounded-2xl w-full max-w-lg overflow-hidden border"
            style="
                border-color: rgba(255,255,255,.14);
                background: radial-gradient(900px 450px at 80% 0%, rgba(45,212,191,.10), transparent 60%),
                            radial-gradient(700px 420px at 20% 35%, rgba(96,165,250,.10), transparent 55%),
                            rgba(10,15,24,.94);
                box-shadow: 0 30px 90px rgba(0,0,0,.65);
            "
            role="dialog"
            aria-modal="true"
            aria-labelledby="{{ $name }}-title"
        >
            <div class="px-6 pt-6 pb-5 text-white/90">
                <div class="flex items-start gap-4">
                    <div
                        class="shrink-0 flex items-center justify-center h-11 w-11 rounded-2xl border"
                        style="border-color: rgba(248,113,113,.40); background: rgba(248,113,113,.14);"
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
                            <p class="mt-2 text-sm text-white/70">
                                {{ $content }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 flex flex-col sm:flex-row sm:justify-end gap-3 border-t"
                style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
            >
                <button
                    type="button"
                    class="inline-flex justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white border transition hover:brightness-110"
                    style="border-color: rgba(248,113,113,.45); background: rgba(248,113,113,.22);"
                    x-on:click="document.getElementById('{{ $name }}-form').submit();"
                >
                    {{ $confirmText }}
                </button>

                <button
                    type="button"
                    class="inline-flex justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white/90 border transition hover:text-white hover:brightness-110"
                    style="border-color: rgba(255,255,255,.18); background: rgba(255,255,255,.06);"
                    x-on:click="close()"
                >
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>

    {{-- Form escondido que executa a ação --}}
    <form
        id="{{ $name }}-form"
        method="POST"
        style="display: none;"
        x-bind:action="action()"
    >
        @csrf

        @if ($httpMethod !== 'POST')
            @method($httpMethod)
        @endif
    </form>
</div>
