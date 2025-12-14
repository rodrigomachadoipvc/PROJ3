<x-guest-layout>
    <div class="max-w-md mx-auto mt-8 p-6 bg-white shadow rounded-lg">

        <h1 class="text-xl font-semibold text-gray-900 text-center">
            Verifica o teu email
        </h1>

        <p class="mt-4 text-sm text-gray-600 text-center">
            Obrigado por te registares no <strong>Communico</strong>.
            Antes de continuares, precisamos que confirmes o teu endereço de email.
        </p>

        <p class="mt-2 text-sm text-gray-600 text-center">
            Enviámos um link de verificação para o teu email.
            Clica nesse link para ativares a tua conta.
        </p>

        @if (session('status') === 'verification-link-sent')
            <div class="mt-4 text-sm text-green-600 text-center font-medium">
                Um novo email de verificação foi enviado.
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3">

            {{-- Reenviar email --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <x-primary-button class="w-full justify-center">
                    Reenviar email de verificação
                </x-primary-button>
            </form>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="w-full text-sm text-gray-600 hover:text-gray-900 underline"
                >
                    Terminar sessão
                </button>
            </form>

        </div>
    </div>
</x-guest-layout>
