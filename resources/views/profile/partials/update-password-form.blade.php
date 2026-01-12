<section class="space-y-6">

    <header>
        <h2 class="text-lg font-semibold text-white">
            Alterar palavra-passe
        </h2>

        <p class="mt-1 text-sm text-white/65 max-w-2xl">
            Para manter a tua conta segura, utiliza uma palavra-passe forte, única e difícil de adivinhar.
        </p>
    </header>

    <form method="post"
          action="{{ route('password.update') }}"
          class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- Palavra-passe atual --}}
        <div>
            <x-input-label
                for="update_password_current_password"
                value="Palavra-passe atual"
                class="text-white/80"
            />

            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full bg-white/5 text-white border-white/10
                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />
        </div>

        {{-- Nova palavra-passe --}}
        <div>
            <x-input-label
                for="update_password_password"
                value="Nova palavra-passe"
                class="text-white/80"
            />

            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full bg-white/5 text-white border-white/10
                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />
        </div>

        {{-- Confirmar nova palavra-passe --}}
        <div>
            <x-input-label
                for="update_password_password_confirmation"
                value="Confirmar nova palavra-passe"
                class="text-white/80"
            />

            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full bg-white/5 text-white border-white/10
                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"
            />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                Guardar alterações
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-white/65"
                >
                    Palavra-passe atualizada com sucesso.
                </p>
            @endif
        </div>
    </form>

</section>
