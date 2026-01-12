<x-guest-layout>
    <div class="w-full">
        {{-- Título --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-white">
                Verifica o teu email
            </h1>
            <p class="mt-4 text-sm text-white/65">
                Obrigado por te registares no <strong class="text-white/85">Communico</strong>.
                Antes de continuares, precisamos que confirmes o teu endereço de email.
            </p>
            <p class="mt-2 text-sm text-white/65">
                Enviámos um link de verificação para o teu email.
                Clica nesse link para ativares a tua conta.
            </p>

            @if (session('status') === 'verification-link-sent')
                <div class="mt-4 text-sm font-semibold" style="color: rgba(45,212,191,.95);">
                    Um novo email de verificação foi enviado.
                </div>
            @endif
        </div>

        {{-- Ações --}}
        <div class="mt-2 flex flex-col gap-4">
            {{-- Reenviar email --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <x-primary-button class="w-full justify-center py-3">
                    Reenviar email de verificação
                </x-primary-button>
            </form>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="text-sm font-semibold hover:underline text-center"
                    style="color: rgba(96,165,250,.95);"
                >
                    Terminar sessão
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
