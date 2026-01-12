<section class="space-y-6">

    <header>
        <h2 class="text-lg font-semibold text-white">
            Informação do perfil
        </h2>

        <p class="mt-1 text-sm text-white/65 max-w-2xl">
            Atualiza os teus dados pessoais e o endereço de email associado à conta.
        </p>
    </header>

    {{-- Reenvio de email de verificação --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
        enctype="multipart/form-data"
    >
        @csrf
        @method('patch')

        {{-- Nome --}}
        <div>
            <x-input-label for="nome" value="Nome" class="text-white/80" />

            <x-text-input
                id="nome"
                name="nome"
                type="text"
                class="mt-1 block w-full bg-white/5 text-white border-white/10
                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                :value="old('nome', $user->nome)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error class="mt-2" :messages="$errors->get('nome')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Endereço de email" class="text-white/80" />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full bg-white/5 text-white border-white/10
                       placeholder-white/40 focus:border-white/20 focus:ring-0 rounded-xl"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        {{-- Avatar --}}
        <div>
            <x-input-label for="avatar" value="Avatar" class="text-white/80" />

            <div class="mt-3 flex items-center gap-4">
                <x-avatar :user="$user" size="lg" />

                <div class="space-y-2">
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept="image/*"
                        class="block w-full text-sm text-white/80
                               rounded-xl border cursor-pointer
                               focus:outline-none focus:ring-0"
                        style="border-color: rgba(255,255,255,.12); background: rgba(255,255,255,.03);"
                    >

                    @if ($user->avatar_url_public)
                        <button
                            type="submit"
                            form="remove-avatar-form"
                            class="text-sm font-semibold hover:underline"
                            style="color: rgba(248,113,113,.95);"
                        >
                            Remover avatar
                        </button>
                    @endif
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                Guardar alterações
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-white/65">
                    Dados atualizados com sucesso.
                </p>
            @elseif (session('status') === 'avatar-removed')
                <p class="text-sm text-white/65">
                    Avatar removido com sucesso.
                </p>
            @endif
        </div>
    </form>

    {{-- Form oculto para remover avatar --}}
    <form
        id="remove-avatar-form"
        method="POST"
        action="{{ route('profile.avatar.remove') }}"
        class="hidden"
    >
        @csrf
        @method('delete')
    </form>

</section>
