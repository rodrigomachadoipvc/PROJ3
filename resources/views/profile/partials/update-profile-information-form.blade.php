<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    {{-- Form para reenvio de verificação de email --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    @php
        // Compatibilidade: a tua coluna é "nome", mas em alguns pontos Breeze usa "name"
        $displayName = $user->nome ?? $user->name;

        // Iniciais para o avatar default
        $initials = '';

        if ($displayName) {
            $parts = preg_split('/\s+/', trim($displayName));
            $parts = array_filter($parts);

            $i = 0;
            foreach ($parts as $part) {
                $initials .= mb_substr($part, 0, 1);
                $i++;
                if ($i >= 2) {
                    break;
                }
            }

            $initials = strtoupper($initials);
        }
    @endphp

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
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                value="{{ old('name', $displayName) }}"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button
                            form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Avatar --}}
        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />

            <div class="mt-2 flex items-center gap-4">
                @if ($user->avatar_url)
                    <img
                        src="{{ asset('storage/' . $user->avatar_url) }}"
                        alt="Avatar"
                        class="h-16 w-16 rounded-full object-cover shadow-sm"
                    >
                @else
                    <div class="h-16 w-16 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xl font-semibold shadow-sm">
                        {{ $initials ?: 'U' }}
                    </div>
                @endif

                <div class="space-y-2">
                    <input
                        id="avatar"
                        name="avatar"
                        type="file"
                        accept="image/*"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >

                    @if ($user->avatar_url)
                        <button
                            type="submit"
                            form="remove-avatar-form"
                            class="text-sm font-semibold text-red-600 hover:text-red-700 hover:underline"
                        >
                            {{ __('Remove avatar') }}
                        </button>
                    @endif
                </div>
            </div>

            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        {{-- Botões / mensagens de estado --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('Saved.') }}
                </p>
            @elseif (session('status') === 'avatar-removed')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('Avatar removed.') }}
                </p>
            @endif
        </div>
    </form>

    {{-- Form para remover avatar --}}
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
