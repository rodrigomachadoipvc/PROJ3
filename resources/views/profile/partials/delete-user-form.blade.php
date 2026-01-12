<section class="space-y-6">

    <header>
        <h2 class="text-lg font-semibold text-white">
            Eliminar conta
        </h2>

        <p class="mt-1 text-sm text-white/65 max-w-2xl">
            Ao eliminares a tua conta, todos os dados, mensagens e recursos associados serão removidos de forma permanente.
            Esta ação não pode ser revertida. Antes de continuar, certifica-te de que guardaste qualquer informação que pretendas manter.
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

            <h2 class="text-lg font-semibold text-white">
                Confirmar eliminação de conta
            </h2>

            <p class="mt-2 text-sm text-white/65 max-w-xl">
                Esta ação irá eliminar permanentemente a tua conta e todos os dados associados.
                Para confirmares, introduz a tua palavra-passe.
            </p>

            <div class="mt-6">
                <x-input-label
                    for="password"
                    value="Palavra-passe"
                    class="sr-only"
                />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full bg-white/5 text-white border-white/10
                           placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                    placeholder="Palavra-passe"
                />

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"
                />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button
                    x-on:click="$dispatch('close')"
                >
                    Cancelar
                </x-secondary-button>

                <x-danger-button>
                    Eliminar conta
                </x-danger-button>
            </div>
        </form>
    </x-modal>

</section>
