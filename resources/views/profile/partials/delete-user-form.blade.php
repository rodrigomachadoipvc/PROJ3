<section class="space-y-6">

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Eliminar conta
        </h2>

        <p class="mt-1 text-sm text-gray-600 max-w-2xl">
            Ao eliminar a sua conta, todos os dados, mensagens e recursos associados
            serão removidos de forma permanente. Esta ação não pode ser revertida.
            Antes de continuar, certifique-se de que guardou qualquer informação
            que pretenda manter.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        Eliminar conta
    </x-danger-button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                Confirmar eliminação de conta
            </h2>

            <p class="mt-2 text-sm text-gray-600 max-w-xl">
                Esta ação irá eliminar permanentemente a sua conta e todos os dados
                associados. Para confirmar, introduza a sua password.
            </p>

            <div class="mt-6">
                <x-input-label
                    for="password"
                    value="Password"
                    class="sr-only"
                />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4
                           border-gray-300
                           focus:border-red-500
                           focus:ring-red-500"
                    placeholder="Password"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"
                />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button
                    x-on:click="$dispatch('close')"
                >
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Eliminar conta
                </x-danger-button>
            </div>
        </form>
    </x-modal>

</section>
