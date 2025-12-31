<x-guest-layout>
    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900">
                Criar conta
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Regista-te para começar a usar o Communico
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Nome -->
            <div>
                <x-input-label for="nome" :value="__('Name')" />

                <x-text-input
                    id="nome"
                    type="text"
                    name="nome"
                    :value="old('nome')"
                    required
                    autofocus
                    autocomplete="name"
                    class="mt-1 block w-full
                           rounded-lg
                           bg-gray-50 border border-gray-300
                           focus:bg-white
                           focus:border-brand-secondary
                           focus:ring-brand-secondary"
                />

                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" />

                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
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
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-2">
                <a
                    href="{{ route('login') }}"
                    class="text-sm font-medium
                           text-gray-600
                           hover:text-brand-secondary
                           focus:outline-none focus:ring-2
                           focus:ring-offset-2
                           focus:ring-brand-secondary
                           rounded-md"
                >
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="px-6 py-3">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
