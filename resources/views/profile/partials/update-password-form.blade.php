<section class="space-y-6">

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Alterar password
        </h2>

        <p class="mt-1 text-sm text-gray-600 max-w-2xl">
            Para manter a sua conta segura, utilize uma password forte,
            única e difícil de adivinhar.
        </p>
    </header>

    <form method="post"
          action="{{ route('password.update') }}"
          class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- Password atual --}}
        <div>
            <x-input-label
                for="update_password_current_password"
                value="Password atual"
            />

            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />
        </div>

        {{-- Nova password --}}
        <div>
            <x-input-label
                for="update_password_password"
                value="Nova password"
            />

            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />
        </div>

        {{-- Confirmar nova password --}}
        <div>
            <x-input-label
                for="update_password_password_confirmation"
                value="Confirmar nova password"
            />

            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
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
                    class="text-sm text-gray-600"
                >
                    Password atualizada com sucesso.
                </p>
            @endif
        </div>
    </form>

</section>
