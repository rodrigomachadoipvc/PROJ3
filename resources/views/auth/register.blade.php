<x-guest-layout>
    <div class="w-full">
        {{-- Cabeçalho --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-white">
                Criar conta
            </h1>
            <p class="mt-1 text-sm text-white/65">
                Cadastra-te-te para começar a usar o Communico
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Nome -->
            <div>
                <x-input-label for="nome" :value="__('Nome')" class="text-white/80" />

                <x-text-input
                    id="nome"
                    type="text"
                    name="nome"
                    :value="old('nome')"
                    required
                    autofocus
                    autocomplete="name"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                />

                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-white/80" />

                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                />

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Palavra-passe')" class="text-white/80" />

                <x-text-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmar Palavra-passe')" class="text-white/80" />

                <x-text-input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                />

                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-2">
                <a
                    href="{{ route('login') }}"
                    class="text-sm font-semibold hover:underline"
                    style="color: rgba(96,165,250,.95);"
                >
                    {{ __('Já está cadastrado?') }}
                </a>

                <x-primary-button class="px-6 py-3">
                    {{ __('Cadastrar') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
