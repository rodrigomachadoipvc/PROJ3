<x-guest-layout>
    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">
                Recuperar palavra-passe
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />

                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    class="mt-1 block w-full
                           rounded-lg
                           bg-gray-50 border border-gray-300
                           focus:bg-white
                           focus:border-brand-secondary
                           focus:ring-brand-secondary"
                />

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <x-primary-button class="w-full justify-center py-3">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
