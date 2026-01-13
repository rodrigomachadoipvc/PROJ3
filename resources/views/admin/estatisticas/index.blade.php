<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl leading-tight text-white">
                    Estatísticas gerais
                </h2>
                <p class="mt-1 text-sm text-white/60">
                    Indicadores globais, gráficos e exportação. Só administradores.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.estatisticas.export.excel', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition
                           hover:bg-white/10"
                    style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                >
                    Exportar Excel
                </a>

                <a
                    href="{{ route('admin.estatisticas.export.pdf', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition
                           hover:bg-white/10"
                    style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                >
                    PDF relatório
                </a>

                <div class="hidden sm:flex items-center gap-2">
                    <a
                        href="{{ route('admin.estatisticas.export.csv', array_merge(request()->query(), ['dataset' => 'mensagens'])) }}"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition
                               hover:bg-white/10"
                        style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                    >
                        CSV Mensagens
                    </a>

                    <a
                        href="{{ route('admin.estatisticas.export.csv', array_merge(request()->query(), ['dataset' => 'anexos'])) }}"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition
                               hover:bg-white/10"
                        style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                    >
                        CSV Anexos
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="comm-card rounded-2xl overflow-hidden">
                <div class="p-6 sm:p-8 space-y-10">

                    {{-- FILTROS --}}
                    <div
                        x-data="{
                            range: '{{ $f['range'] ?? '7' }}',
                            start: '{{ request('start') }}',
                            end: '{{ request('end') }}',
                            setQuickRange(v) {
                                this.range = String(v);
                                // ✅ evita colisão: não enviar start/end quando não é custom
                                this.start = '';
                                this.end = '';
                                this.$nextTick(() => this.$refs.form.submit());
                            }
                        }"
                        class="comm-card rounded-2xl p-6"
                    >
                        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    Filtros
                                </h3>
                                <p class="mt-1 text-sm text-white/60">
                                    Selecione um intervalo e, opcionalmente, filtre por grupo e/ou utilizador.
                                </p>
                            </div>

                            <form
                                x-ref="form"
                                method="GET"
                                action="{{ route('admin.estatisticas.index') }}"
                                class="w-full lg:w-auto flex flex-col lg:flex-row gap-3 lg:items-end"
                            >
                                {{-- Range rápido --}}
                                <div class="flex items-center gap-2 flex-wrap">
                                    @foreach ([7, 30, 90] as $d)
                                        <button
                                            type="button"
                                            @click="setQuickRange({{ $d }})"
                                            class="px-4 py-2 rounded-full text-sm font-semibold border transition whitespace-nowrap
                                                   {{ (string)($f['range'] ?? '') === (string)$d ? 'text-white' : 'text-white/70 hover:text-white' }}"
                                            style="
                                                border-color: rgba(255,255,255,.10);
                                                background: {{ (string)($f['range'] ?? '') === (string)$d ? 'rgba(45,212,191,.18)' : 'rgba(255,255,255,.04)' }};
                                            "
                                        >
                                            Últimos {{ $d }} dias
                                        </button>
                                    @endforeach

                                    <button
                                        type="button"
                                        @click="range='custom'"
                                        class="px-4 py-2 rounded-full text-sm font-semibold border transition whitespace-nowrap
                                               {{ ($f['range'] ?? '') === 'custom' ? 'text-white' : 'text-white/70 hover:text-white' }}"
                                        style="
                                            border-color: rgba(255,255,255,.10);
                                            background: {{ ($f['range'] ?? '') === 'custom' ? 'rgba(96,165,250,.18)' : 'rgba(255,255,255,.04)' }};
                                        "
                                    >
                                        Personalizado
                                    </button>
                                </div>

                                {{-- range (única fonte de verdade) --}}
                                <input type="hidden" name="range" :value="range">

                                {{-- Datas custom --}}
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full lg:w-auto"
                                    x-show="range==='custom'"
                                    x-cloak
                                >
                                    <div>
                                        <label class="block text-xs font-semibold text-white/70 mb-1">Início</label>
                                        <input
                                            type="date"
                                            name="start"
                                            x-model="start"
                                            :disabled="range!=='custom'"
                                            class="w-full rounded-xl border text-sm text-white placeholder:text-white/40
                                                   focus:outline-none focus:ring-2"
                                            style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04); ring-color: rgba(96,165,250,.35);"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-white/70 mb-1">Fim</label>
                                        <input
                                            type="date"
                                            name="end"
                                            x-model="end"
                                            :disabled="range!=='custom'"
                                            class="w-full rounded-xl border text-sm text-white placeholder:text-white/40
                                                   focus:outline-none focus:ring-2"
                                            style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                                        >
                                    </div>
                                </div>

                                {{-- Grupo --}}
                                <div class="w-full sm:w-56">
                                    <label class="block text-xs font-semibold text-white/70 mb-1">Grupo</label>
                                    <select
                                        name="grupo_id"
                                        class="w-full rounded-xl border text-sm text-white focus:outline-none focus:ring-2"
                                        style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                                    >
                                        <option value="" class="bg-[#0B1220]">Todos</option>
                                        @foreach ($grupos as $g)
                                            <option value="{{ $g->id }}"
                                                class="bg-[#0B1220]"
                                                {{ (string)request('grupo_id') === (string)$g->id ? 'selected' : '' }}
                                            >
                                                {{ $g->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Utilizador --}}
                                <div class="w-full sm:w-56">
                                    <label class="block text-xs font-semibold text-white/70 mb-1">Utilizador</label>
                                    <select
                                        name="user_id"
                                        class="w-full rounded-xl border text-sm text-white focus:outline-none focus:ring-2"
                                        style="border-color: rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                                    >
                                        <option value="" class="bg-[#0B1220]">Todos</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}"
                                                class="bg-[#0B1220]"
                                                {{ (string)request('user_id') === (string)$u->id ? 'selected' : '' }}
                                            >
                                                {{ $u->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="submit"
                                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition
                                               hover:-translate-y-0.5"
                                        style="background: rgba(45,212,191,.18); color: var(--c-t1); border: 1px solid rgba(45,212,191,.25);"
                                    >
                                        Aplicar
                                    </button>

                                    <a
                                        href="{{ route('admin.estatisticas.index', ['range' => 7]) }}"
                                        class="px-5 py-2.5 rounded-xl text-sm font-semibold transition
                                               hover:bg-white/10"
                                        style="border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.04);"
                                    >
                                        Limpar
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="mt-4 text-sm text-white/60">
                            <span class="font-semibold text-white/80">Intervalo:</span>
                            {{ $f['start']->toDateString() }} → {{ $f['end']->toDateString() }}
                            <span class="mx-2">•</span>
                            <span class="font-semibold text-white/80">Dias:</span> {{ $f['daysLen'] }}
                        </div>
                    </div>

                    {{-- KPI CARDS --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        @php
                            $delta = $kpis['deltaPct'];
                            $deltaLabel = is_null($delta) ? '—' : (($delta >= 0 ? '+' : '') . $delta . '%');
                            $deltaColor = is_null($delta) ? 'rgba(255,255,255,.55)' : ($delta >= 0 ? 'var(--c-a2)' : 'rgba(248,113,113,.95)');
                        @endphp

                        <div class="comm-card rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/60">Total de mensagens</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($kpis['totalMensagens']) }}</p>
                            <p class="mt-2 text-xs text-white/60">
                                vs período anterior:
                                <span class="font-semibold" style="color: {{ $deltaColor }};">
                                    {{ $deltaLabel }}
                                </span>
                            </p>
                        </div>

                        <div class="comm-card rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/60">Média por dia</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $kpis['avgPerDay'] }}</p>
                            <p class="mt-2 text-xs text-white/60">Base: {{ $f['daysLen'] }} dia(s)</p>
                        </div>

                        <div class="comm-card rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/60">DM vs Grupo</p>
                            <div class="mt-2 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-lg font-semibold text-white">{{ number_format($kpis['dm']) }}</p>
                                    <p class="text-xs text-white/60">DM</p>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-white">{{ number_format($kpis['grupo']) }}</p>
                                    <p class="text-xs text-white/60">Grupo</p>
                                </div>
                            </div>
                        </div>

                        <div class="comm-card rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/60">Anexos</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ number_format($kpis['anexosTotal']) }}</p>
                            <p class="mt-2 text-xs text-white/60">
                                {{ $kpis['anexosMb'] }} MB • {{ number_format($kpis['anexosImagens']) }} imagens
                            </p>
                        </div>

                        <div class="comm-card rounded-2xl p-5">
                            <p class="text-xs font-semibold text-white/60">Estado</p>
                            <p class="mt-2 text-sm text-white/80 leading-relaxed">
                                Filtros aplicados:
                                <span class="font-semibold" style="color: var(--c-a1);">
                                    {{ ($f['grupoId'] ? 'Grupo' : 'Todos') }}
                                </span>
                                e
                                <span class="font-semibold" style="color: var(--c-a2);">
                                    {{ ($f['userId'] ? 'Utilizador' : 'Todos') }}
                                </span>
                            </p>
                            <p class="mt-2 text-xs text-white/55">
                                Relatórios para decisão devem ser tirados com intervalos ≥ 30 dias.
                            </p>
                        </div>
                    </div>

                    {{-- GRÁFICOS --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="comm-card rounded-2xl p-6 lg:col-span-2">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold text-white">Mensagens por dia</h3>
                                <span class="text-xs text-white/60">Série temporal</span>
                            </div>
                            <div class="mt-4">
                                <canvas id="chartMensagensDia" height="120"></canvas>
                            </div>
                        </div>

                        <div class="comm-card rounded-2xl p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold text-white">DM vs Grupo</h3>
                                <span class="text-xs text-white/60">Distribuição</span>
                            </div>
                            <div class="mt-4">
                                <canvas id="chartDmGrupo" height="200"></canvas>
                            </div>
                        </div>

                        <div class="comm-card rounded-2xl p-6 lg:col-span-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-base font-semibold text-white">Top utilizadores</h3>
                                <span class="text-xs text-white/60">Atividade (mensagens)</span>
                            </div>
                            <div class="mt-4">
                                <canvas id="chartTopUsers" height="90"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- TABELAS --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="comm-card rounded-2xl p-6">
                            <h3 class="text-base font-semibold text-white">Top utilizadores</h3>
                            <p class="mt-1 text-sm text-white/60">Quem mais envia mensagens no intervalo selecionado.</p>

                            <div class="mt-4 overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-white/70">
                                            <th class="text-left py-2 border-b" style="border-color: rgba(255,255,255,.10);">Utilizador</th>
                                            <th class="text-right py-2 border-b" style="border-color: rgba(255,255,255,.10);">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topUsers as $u)
                                            <tr>
                                                <td class="py-2 border-b" style="border-color: rgba(255,255,255,.06);">
                                                    {{ $u->nome }}
                                                </td>
                                                <td class="py-2 text-right border-b font-semibold" style="border-color: rgba(255,255,255,.06); color: var(--c-a2);">
                                                    {{ number_format($u->total) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="py-3 text-white/60">Sem dados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="comm-card rounded-2xl p-6">
                            <h3 class="text-base font-semibold text-white">Top grupos</h3>
                            <p class="mt-1 text-sm text-white/60">Onde se concentram as mensagens (conversas de grupo).</p>

                            <div class="mt-4 overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-white/70">
                                            <th class="text-left py-2 border-b" style="border-color: rgba(255,255,255,.10);">Grupo</th>
                                            <th class="text-right py-2 border-b" style="border-color: rgba(255,255,255,.10);">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topGrupos as $g)
                                            <tr>
                                                <td class="py-2 border-b" style="border-color: rgba(255,255,255,.06);">
                                                    {{ $g->nome }}
                                                </td>
                                                <td class="py-2 text-right border-b font-semibold" style="border-color: rgba(255,255,255,.06); color: var(--c-a1);">
                                                    {{ number_format($g->total) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="py-3 text-white/60">Sem dados.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <p class="mt-4 text-xs text-white/55">
                                Nota: se filtraste por um grupo específico, esta tabela tende a ficar “plana” (e é esperado).
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- CHARTS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($chartData);

            const init = () => {
                if (!window.Chart) {
                    // Se por algum motivo o module ainda não executou, tenta outra vez rapidamente.
                    setTimeout(init, 50);
                    return;
                }

                const ctx1 = document.getElementById('chartMensagensDia');
                if (ctx1) {
                    new Chart(ctx1, {
                        type: 'line',
                        data: {
                            labels: data.mensagensPorDia.labels,
                            datasets: [{
                                label: 'Mensagens',
                                data: data.mensagensPorDia.data,
                                tension: 0.25
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { ticks: { color: 'rgba(255,255,255,.65)' }, grid: { color: 'rgba(255,255,255,.06)' } },
                                y: { ticks: { color: 'rgba(255,255,255,.65)' }, grid: { color: 'rgba(255,255,255,.06)' } }
                            }
                        }
                    });
                }

                const ctx2 = document.getElementById('chartDmGrupo');
                if (ctx2) {
                    new Chart(ctx2, {
                        type: 'doughnut',
                        data: {
                            labels: data.dmVsGrupo.labels,
                            datasets: [{ data: data.dmVsGrupo.data }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { labels: { color: 'rgba(255,255,255,.75)' } } }
                        }
                    });
                }

                const ctx3 = document.getElementById('chartTopUsers');
                if (ctx3) {
                    new Chart(ctx3, {
                        type: 'bar',
                        data: {
                            labels: data.topUsers.labels,
                            datasets: [{
                                label: 'Mensagens',
                                data: data.topUsers.data
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { ticks: { color: 'rgba(255,255,255,.65)' }, grid: { color: 'rgba(255,255,255,.06)' } },
                                y: { ticks: { color: 'rgba(255,255,255,.65)' }, grid: { color: 'rgba(255,255,255,.06)' } }
                            }
                        }
                    });
                }
            };

            init();
        });
    </script>
</x-app-layout>