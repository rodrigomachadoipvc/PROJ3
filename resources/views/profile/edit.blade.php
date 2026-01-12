<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Perfil do utilizador
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Informação do perfil --}}
            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 max-w-xl text-white/90">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Alterar password --}}
            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 max-w-xl text-white/90">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Eliminar conta (perigo) --}}
            <div class="comm-card rounded-2xl overflow-hidden border"
                 style="border-color: rgba(248,113,113,.25);">
                <div class="p-6 sm:p-8 max-w-xl text-white/90">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
