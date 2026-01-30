<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-white">
                Entrar
            </h1>
            <p class="mt-1 text-sm text-white/65">
                Acede à tua conta Communico
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-white/80" />

                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                />

                <x-input-error :messages="$errors->get('Email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Palavra-passe')" class="text-white/80" />

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

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="h-4 w-4 rounded border-white/20 bg-transparent focus:ring-0"
                        style="accent-color: rgb(45, 212, 191);"
                    >
                    <span class="ms-2 text-sm text-white/70">
                        {{ __('Relembrar') }}
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm font-semibold hover:underline"
                        style="color: rgba(96,165,250,.95);"
                    >
                        {{ __('Esqueceu sua palavra-passe?') }}
                    </a>
                @endif
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <x-primary-button class="w-full justify-center py-3">
                    {{ __('Entrar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
