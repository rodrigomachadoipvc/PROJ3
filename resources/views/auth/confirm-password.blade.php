<x-guest-layout>
    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">
                Confirmar palavra-passe
            </h1>
            <p class="mt-2 text-sm text-gray-500">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
            @csrf

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />

                <x-text-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full
                           rounded-lg
                           bg-gray-50 border border-gray-300
                           focus:bg-white
                           focus:border-brand-secondary
                           focus:ring-brand-secondary"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <x-primary-button class="w-full justify-center py-3">
                    {{ __('Confirm') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
