<nav
    x-data="{ open: false }"
    class="border-b"
    style="
        border-color: rgba(255,255,255,.10);
        background: rgba(7,11,20,.55);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    "
>
    @php
        $user = Auth::user();
        $isAdmin = $user?->tipoUtilizador?->nome === 'Administrador';
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Left side --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center">
                        <img
                            src="{{ asset('images/logo_communico.svg') }}"
                            alt="Communico"
                            class="block h-9 w-auto"
                        />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link
                        :href="route('user.dashboard')"
                        :active="request()->routeIs('user.dashboard')"
                        class="text-white/80 hover:text-white"
                    >
                        {{ __('Painel') }}
                    </x-nav-link>

                    @if ($isAdmin)
                        <x-nav-link
                            :href="route('admin.dashboard')"
                            :active="request()->routeIs('admin.*')"
                            class="text-white/80 hover:text-white"
                        >
                            {{ __('Administração') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Right side --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 rounded-md
                                   text-sm leading-4 font-medium
                                   text-white/85 hover:text-white
                                   focus:outline-none transition
                                   border"
                            style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                        >
                            <x-avatar :user="$user" size="sm" />

                            <span class="ms-2">{{ $user->nome }}</span>

                            <svg
                                class="ms-1 h-4 w-4"
                                style="color: rgba(255,255,255,.70);"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path fill-rule="evenodd"
                                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586
                                         l3.293-3.293a1 1 0 111.414 1.414
                                         l-4 4a1 1 0 01-1.414 0l-4-4
                                         a1 1 0 010-1.414z"
                                      clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                {{ __('Terminar sessão') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md
                           text-white/70 hover:text-white
                           focus:outline-none transition border"
                    style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                >
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path
                            :class="{ 'hidden': open, 'inline-flex': ! open }"
                            class="inline-flex"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{ 'hidden': ! open, 'inline-flex': open }"
                            class="hidden"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Navigation --}}
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link
                    :href="route('user.dashboard')"
                    :active="request()->routeIs('user.dashboard')"
                class="text-white/85 hover:text-white"
            >
                {{ __('Painel') }}
            </x-responsive-nav-link>

                @if (($user?->tipoUtilizador?->nome ?? null) === 'Administrador')
                    <x-responsive-nav-link
                        :href="route('admin.dashboard')"
                        :active="request()->routeIs('admin.*')"
                        class="text-white/85 hover:text-white"
                    >
                        {{ __('Administração') }}
                    </x-responsive-nav-link>
                @endif
        </div>

        <div class="pt-4 pb-1 border-t" style="border-color: rgba(255,255,255,.10);">
            <div class="px-4 flex items-center">
                <x-avatar :user="$user" size="md" />

                <div class="ms-3">
                    <div class="font-medium text-base" style="color: rgba(255,255,255,.90);">
                        {{ $user->nome }}
                    </div>
                    <div class="font-medium text-sm" style="color: rgba(255,255,255,.65);">
                        {{ $user->email }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white/85 hover:text-white">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link
                        :href="route('logout')"
                        class="text-white/85 hover:text-white"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        {{ __('Terminar sessão') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
