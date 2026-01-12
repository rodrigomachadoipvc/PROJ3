<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-white">
            Respostas automáticas
        </h2>
    </x-slot>

    <div x-data="{ showDeleteModal: false, deleteUrl: '' }">
        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="comm-card rounded-2xl overflow-hidden">
                    <div class="p-6 sm:p-8 text-white/90">

                        {{-- Mensagens de feedback --}}
                        @if (session('success'))
                            <div class="mb-6 rounded-xl border px-4 py-3 text-sm"
                                 style="border-color: rgba(34,197,94,.25); background: rgba(34,197,94,.10); color: rgba(220,252,231,.95);">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-6 rounded-xl border px-4 py-3 text-sm"
                                 style="border-color: rgba(248,113,113,.25); background: rgba(248,113,113,.10); color: rgba(254,202,202,.95);">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- Cabeçalho --}}
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-6">
                            <div>
                                <div class="text-xs tracking-wide text-white/55 uppercase">
                                    Administração
                                </div>
                                <h3 class="mt-1 text-2xl font-semibold text-white">
                                    Regras de resposta automática
                                </h3>
                                <p class="mt-1 text-sm text-white/65">
                                    Total: <span class="font-semibold text-white/85">{{ $autoReplies->count() }}</span>
                                </p>
                            </div>

                            <a
                                href="{{ route('admin.auto-replies.create') }}"
                                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                       text-white border transition hover:brightness-110"
                                style="
                                    border-color: rgba(255,255,255,.12);
                                    background: linear-gradient(180deg, rgba(96,165,250,.20), rgba(45,212,191,.12));
                                "
                            >
                                Nova resposta
                            </a>
                        </div>

                        {{-- Tabela --}}
                        <div class="overflow-x-auto rounded-2xl border" style="border-color: rgba(255,255,255,.10);">
                            <table class="min-w-full">
                                <thead style="background: rgba(255,255,255,.04);">
                                    <tr class="border-b" style="border-color: rgba(255,255,255,.10);">
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">
                                            Disparador
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-white/60">
                                            Resposta automática
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-white/60">
                                            Prioridade
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-white/60">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-white/60">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y" style="divide-color: rgba(255,255,255,.08);">
                                    @forelse ($autoReplies as $reply)
                                        <tr
                                            class="transition"
                                            style="background: rgba(255,255,255,.02);"
                                            onmouseover="this.style.background='rgba(255,255,255,.05)'"
                                            onmouseout="this.style.background='rgba(255,255,255,.02)'"
                                        >
                                            <td class="px-6 py-4 text-sm font-semibold text-white/85">
                                                {{ $reply->trigger }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-white/70 max-w-md truncate">
                                                {{ $reply->response }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-center text-white/85">
                                                {{ $reply->priority }}
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                @php $isActive = (bool) $reply->active; @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                                                      style="
                                                            border-color: {{ $isActive ? 'rgba(52,211,153,.30)' : 'rgba(255,255,255,.18)' }};
                                                            background: {{ $isActive ? 'rgba(52,211,153,.10)' : 'rgba(255,255,255,.06)' }};
                                                            color: {{ $isActive ? 'rgba(167,243,208,.95)' : 'rgba(255,255,255,.70)' }};
                                                      ">
                                                    {{ $isActive ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                                <div class="inline-flex items-center gap-4">
                                                    <a
                                                        href="{{ route('admin.auto-replies.edit', $reply) }}"
                                                        class="font-semibold hover:underline"
                                                        style="color: rgba(96,165,250,.95);"
                                                    >
                                                        Editar
                                                    </a>

                                                    <button
                                                        @click="
                                                            showDeleteModal = true;
                                                            deleteUrl = '{{ route('admin.auto-replies.destroy', $reply) }}'
                                                        "
                                                        class="font-semibold hover:underline"
                                                        style="color: rgba(248,113,113,.95);"
                                                    >
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-sm text-white/60">
                                                Não existem respostas automáticas definidas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        {{-- Modal de confirmação --}}
        <div
            x-show="showDeleteModal"
            x-on:keydown.escape.window="showDeleteModal = false"
            style="display:none"
        >
            <x-confirm-modal
                name="auto-reply-delete"
                title="Eliminar resposta automática"
                content="Tem a certeza de que pretende eliminar esta resposta automática?"
                method="DELETE"
                x-bind:action-route="deleteUrl"
            />
        </div>
    </div>
</x-app-layout>
