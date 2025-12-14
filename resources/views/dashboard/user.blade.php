<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">

        @php
            $user = Auth::user();
            $avatar = $user->avatar_url_public;
            $initials = $user->initials;
        @endphp

        {{-- Cabeçalho --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Olá, {{ $user->name }}
            </h1>
            <p class="text-gray-600 text-sm mt-1">
                Bem-vindo ao seu painel. Aqui encontra atalhos e atividade recente.
            </p>
        </div>

        {{-- Grelha Principal --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Conversas Recentes --}}
            <div class="bg-white shadow rounded-xl p-6 border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Conversas Recentes</h2>

                @forelse ($recentConversas as $conversa)
                    @php 
                        $outro = $conversa->participantes->where('id', '!=', $user->id)->first();
                        $ultima = $conversa->ultimaMensagem;
                    @endphp

                    <a href="{{ route('chat.index', ['c' => $conversa->id]) }}"
                       class="block p-3 mb-2 rounded-lg hover:bg-gray-50 transition border border-gray-200">

                        <p class="font-medium text-sm text-gray-800">
                            {{ $outro?->name ?? 'Conversa' }}
                        </p>

                        @if ($ultima)
                            <p class="text-xs text-gray-600 mt-1 truncate">
                                <strong>{{ $ultima->emissor_id == $user->id ? 'Você' : $ultima->emissor?->name }}:</strong>
                                {{ $ultima->conteudo ?? 'Anexo' }}
                            </p>

                            <span class="text-[11px] text-gray-400">
                                {{ $ultima->created_at->diffForHumans() }}
                            </span>
                        @endif
                    </a>

                @empty
                    <p class="text-sm text-gray-500">Sem conversas recentes.</p>
                @endforelse
            </div>

            {{-- Grupos --}}
            <div class="bg-white shadow rounded-xl p-6 border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Os Seus Grupos</h2>

                @forelse ($recentGrupos as $grupo)
                    <a href="{{ route('chat.index', ['g' => $grupo->id]) }}"
                       class="block p-3 mb-2 rounded-lg hover:bg-gray-50 transition border border-gray-200">

                        <p class="font-medium text-sm text-gray-800">{{ $grupo->nome }}</p>

                        <span class="text-xs text-gray-500">
                            Entrou: {{ $grupo->pivot->created_at->format('d/m/Y') }}
                        </span>
                    </a>

                @empty
                    <p class="text-sm text-gray-500">Ainda não pertence a grupos.</p>
                @endforelse
            </div>

            {{-- Perfil --}}
            <div class="bg-white shadow rounded-xl p-6 border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">A Sua Conta</h2>

                <div class="flex items-center space-x-4">

                    {{-- Avatar --}}
                    @if ($avatar)
                        <img src="{{ $avatar }}" class="w-14 h-14 rounded-full object-cover shadow">
                    @else
                        <div class="w-14 h-14 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xl font-semibold">
                            {{ $initials }}
                        </div>
                    @endif

                    <div>
                        <p class="font-semibold text-gray-800">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>

                        <a href="{{ route('profile.edit') }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            Editar Perfil →
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- Ações rápidas --}}
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('chat.index') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-3 px-6 rounded-lg shadow text-center">
                Abrir Chat 💬
            </a>

            <a href="{{ route('chat.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-3 px-6 rounded-lg border border-gray-300 text-center">
                Iniciar Nova Conversa ➕
            </a>

            <a href="{{ route('profile.edit') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold py-3 px-6 rounded-lg border border-gray-300 text-center">
                Gerir Conta ⚙️
            </a>
        </div>

    </div>
</x-app-layout>
