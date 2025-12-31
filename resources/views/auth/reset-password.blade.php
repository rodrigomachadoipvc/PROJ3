<x-guest-layout>
    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">
                Redefinir palavra-passe
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                Introduz uma nova palavra-passe para a tua conta.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />

                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email', $request->email)"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1 block w-full
                           rounded-lg
                           bg-gray-50 border border-gray-300
                           focus:bg-white
                           focus:border-brand-secondary
                           focus:ring-brand-secondary"
                />

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="mt-1 block w-full
                           rounded-lg
                           bg-gray-50 border border-gray-300
                           focus:bg-white
                           focus:border-brand-secondary
                           focus:ring-brand-secondary"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

                <x-text-input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="mt-1 block w-full
                           rounded-lg
                           bg-gray-50 border border-gray-300
                           focus:bg-white
                           focus:border-brand-secondary
                           focus:ring-brand-secondary"
                />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <x-primary-button class="w-full justify-center py-3">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
