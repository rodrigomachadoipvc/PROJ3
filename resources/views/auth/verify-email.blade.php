<x-guest-layout>
    <div class="w-full max-w-md mx-auto">

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-8">

            {{-- Título --}}
            <h1 class="text-2xl font-semibold text-gray-900 text-center">
                Verifica o teu email
            </h1>

            {{-- Texto --}}
            <p class="mt-4 text-sm text-gray-600 text-center">
                Obrigado por te registares no <strong>Communico</strong>.
                Antes de continuares, precisamos que confirmes o teu endereço de email.
            </p>

            <p class="mt-2 text-sm text-gray-600 text-center">
                Enviámos um link de verificação para o teu email.
                Clica nesse link para ativares a tua conta.
            </p>

            {{-- Estado --}}
            @if (session('status') === 'verification-link-sent')
                <div class="mt-4 text-sm text-brand-secondary text-center font-medium">
                    Um novo email de verificação foi enviado.
                </div>
            @endif

            {{-- Ações --}}
            <div class="mt-8 flex flex-col gap-4">

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
                        class="text-sm text-gray-600 hover:text-brand-secondary
                               underline text-center"
                    >
                        Terminar sessão
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>
