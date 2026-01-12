<x-guest-layout>
    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-white">
                Confirmar palavra-passe
            </h1>
            <p class="mt-2 text-sm text-white/65">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
            @csrf

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-white/80" />

                <x-text-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
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
