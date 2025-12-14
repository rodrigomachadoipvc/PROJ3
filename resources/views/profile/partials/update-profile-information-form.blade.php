<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

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
            <x-input-label for="nome" :value="__('Name')" />
            <x-text-input
                id="nome"
                name="nome"
                type="text"
                class="mt-1 block w-full"
                :value="old('nome', $user->nome)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('nome')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        {{-- Avatar --}}
        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />

            <div class="mt-2 flex items-center gap-4">
                <x-avatar :user="$user" size="lg" />

                <div class="space-y-2">
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept="image/*"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >

                    @if ($user->avatar_url_public)
                        <button
                            type="submit"
                            form="remove-avatar-form"
                            class="text-sm font-semibold text-red-600 hover:underline"
                        >
                            {{ __('Remove avatar') }}
                        </button>
                    @endif
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @elseif (session('status') === 'avatar-removed')
                <p class="text-sm text-gray-600">{{ __('Avatar removed.') }}</p>
            @endif
        </div>
    </form>

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
