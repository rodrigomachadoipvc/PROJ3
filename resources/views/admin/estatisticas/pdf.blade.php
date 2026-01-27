<!doctype html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <title>Relatório — Estatísticas Gerais</title>

    <style>
        /* ✅ Controla MESMO as margens do PDF (DomPDF respeita @page) */
        @page {
            margin: 8mm 8mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        .header {
            padding: 10px 12px;
            border-bottom: 2px solid #2563EB;
        }

        .brand {
            font-size: 18px;
            font-weight: 800;
            color: #0b1220;
        }

        .sub {
            color: #374151;
            margin-top: 3px;
            font-size: 11px;
        }

        .meta {
            margin-top: 8px;
            font-size: 10.5px;
            color: #374151;
            line-height: 1.35;
        }

        .meta strong {
            color: #111827;
        }

        .spacer-10 { height: 10px; }
        .spacer-14 { height: 14px; }

        /* KPI layout (tabela para máxima compatibilidade DomPDF) */
        .kpi-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-top: 10px;
        }

        .kpi {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px;
            vertical-align: top;
            background: #ffffff;
        }

        .kpi-title {
            font-size: 10px;
            color: #6b7280;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .02em;
        }

        .kpi-value {
            font-size: 18px;
            font-weight: 900;
            margin-top: 6px;
            color: #111827;
        }

        .kpi-note {
            font-size: 10px;
            color: #6b7280;
            margin-top: 6px;
            line-height: 1.25;
        }

        h2 {
            font-size: 13px;
            margin: 14px 0 8px;
            color: #111827;
        }

        /* Cards dos gráficos */
        .chart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .chart-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 10px;
            background: #ffffff;
            vertical-align: top;
        }

        .chart-title {
            font-size: 11px;
            font-weight: 800;
            color: #111827;
            margin: 0 0 8px;
        }

        .chart-img {
            width: 100%;
            display: block;
        }

        /* ✅ Força alturas “bonitas” (sobretudo para o donut) */
        .chart-fixed-260 { height: 260px; }
        .chart-fixed-320 { height: 320px; }
        .chart-fixed-360 { height: 360px; }

        /* DomPDF não respeita sempre height no img; usamos wrapper */
        .img-wrap {
            width: 100%;
            overflow: hidden;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th, table.data td {
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 6px;
            text-align: left;
            font-size: 11px;
        }

        table.data th {
            background: #f9fafb;
            font-size: 11px;
            color: #374151;
        }

        .right { text-align: right; }

        .footer {
            margin-top: 14px;
            font-size: 10px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
@php
    $delta = $kpis['deltaPct'];
    $deltaLabel = is_null($delta) ? '—' : (($delta >= 0 ? '+' : '') . $delta . '%');

    // Labels curtas para reduzir “ruído” e tamanho do URL do gráfico (se for QuickChart)
    $labelsSerie = array_map(function ($d) {
        $d = (string) $d;
        return strlen($d) >= 10 ? substr($d, 5) : $d; // "YYYY-MM-DD" -> "MM-DD"
    }, $serie['labels'] ?? []);

    $dataSerie = array_map(fn($n) => (int) $n, $serie['data'] ?? []);

    $dm = (int) ($kpis['dm'] ?? 0);
    $grupo = (int) ($kpis['grupo'] ?? 0);

    $topUsersLabels = $topUsers->pluck('nome')->map(function ($n) {
        $n = (string) $n;
        return mb_strlen($n) > 14 ? (mb_substr($n, 0, 14) . '…') : $n;
    })->toArray();

    $topUsersData = $topUsers->pluck('total')->map(fn($n) => (int) $n)->toArray();

    /**
     * ✅ Gerador de URL de gráfico (QuickChart).
     * Se estiveres a usar outro gerador, adapta apenas esta função.
     */
    $qcBase = 'https://quickchart.io/chart';

    $qcUrl = function (array $config, int $width, int $height, int $dpr = 2) use ($qcBase) {
        $json = json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $qcBase
            . '?width=' . $width
            . '&height=' . $height
            . '&devicePixelRatio=' . $dpr
            . '&backgroundColor=white'
            . '&c=' . rawurlencode($json);
    };

    // ✅ Chart.js “compatível” (estilo v2) para reduzir surpresas no renderer remoto
    $lineConfig = [
        'type' => 'line',
        'data' => [
            'labels' => $labelsSerie,
            'datasets' => [[
                'label' => 'Mensagens',
                'data' => $dataSerie,
                'borderColor' => '#2563EB',
                'backgroundColor' => 'rgba(37, 99, 235, 0.10)',
                'fill' => false,
                'lineTension' => 0.25,
                'pointRadius' => 2,
                'pointHoverRadius' => 3,
            ]],
        ],
        'options' => [
            'responsive' => false,
            'maintainAspectRatio' => false,
            'legend' => ['display' => false],
            'scales' => [
                'xAxes' => [[
                    'ticks' => [
                        'fontSize' => 14,
                        'maxRotation' => 45,
                        'minRotation' => 45,
                        'fontColor' => '#111827',
                    ],
                    'gridLines' => ['color' => 'rgba(17,24,39,0.08)'],
                ]],
                'yAxes' => [[
                    'ticks' => [
                        'fontSize' => 14,
                        'beginAtZero' => true,
                        'precision' => 0,
                        'fontColor' => '#111827',
                    ],
                    'gridLines' => ['color' => 'rgba(17,24,39,0.08)'],
                ]],
            ],
        ],
    ];

    $donutConfig = [
        'type' => 'doughnut',
        'data' => [
            'labels' => ['DM', 'Grupo'],
            'datasets' => [[
                'data' => [$dm, $grupo],
                'backgroundColor' => ['#2563EB', '#F43F5E'],
                'borderWidth' => 0,
            ]],
        ],
        'options' => [
            'responsive' => false,
            'maintainAspectRatio' => false,
            'cutoutPercentage' => 65,
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'fontSize' => 14,
                    'fontColor' => '#111827',
                    'boxWidth' => 14,
                ],
            ],
        ],
    ];

    $barConfig = [
        'type' => 'bar',
        'data' => [
            'labels' => $topUsersLabels,
            'datasets' => [[
                'label' => 'Mensagens',
                'data' => $topUsersData,
                'backgroundColor' => 'rgba(37, 99, 235, 0.55)',
                'borderWidth' => 0,
            ]],
        ],
        'options' => [
            'responsive' => false,
            'maintainAspectRatio' => false,
            'legend' => ['display' => false],
            'scales' => [
                'xAxes' => [[
                    'ticks' => [
                        'fontSize' => 13,
                        'fontColor' => '#111827',
                    ],
                    'gridLines' => ['display' => false],
                ]],
                'yAxes' => [[
                    'ticks' => [
                        'fontSize' => 14,
                        'beginAtZero' => true,
                        'precision' => 0,
                        'fontColor' => '#111827',
                    ],
                    'gridLines' => ['color' => 'rgba(17,24,39,0.08)'],
                ]],
            ],
        ],
    ];

    // ✅ URLs em alta resolução (DPR=2) e dimensões pensadas para PDF
    $urlLine  = $qcUrl($lineConfig, 1400, 520, 2);
    $urlDonut = $qcUrl($donutConfig, 700, 520, 2);
    $urlBar   = $qcUrl($barConfig, 1200, 520, 2);
@endphp

    <div class="header">
        <div class="brand">Communico — Relatório de Estatísticas Gerais</div>
        <div class="sub">Resumo executivo + indicadores principais</div>

        <div class="meta">
            <strong>Intervalo:</strong>
            {{ $f['start']->toDateString() }} → {{ $f['end']->toDateString() }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Gerado em:</strong> {{ now()->format('Y-m-d H:i') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Filtros:</strong>
            Grupo: {{ $f['grupoId'] ?? 'Todos' }} • Utilizador: {{ $f['userId'] ?? 'Todos' }}
        </div>
    </div>

    <table class="kpi-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi" style="width:25%;">
                <div class="kpi-title">Total mensagens</div>
                <div class="kpi-value">{{ number_format($kpis['totalMensagens']) }}</div>
                <div class="kpi-note">Variação vs anterior: {{ $deltaLabel }}</div>
            </td>
            <td class="kpi" style="width:25%;">
                <div class="kpi-title">Média por dia</div>
                <div class="kpi-value">{{ $kpis['avgPerDay'] }}</div>
                <div class="kpi-note">Base: {{ $f['daysLen'] }} dia(s)</div>
            </td>
            <td class="kpi" style="width:25%;">
                <div class="kpi-title">DM / Grupo</div>
                <div class="kpi-value">{{ number_format($kpis['dm']) }} / {{ number_format($kpis['grupo']) }}</div>
                <div class="kpi-note">Distribuição por tipo de conversa</div>
            </td>
            <td class="kpi" style="width:25%;">
                <div class="kpi-title">Anexos</div>
                <div class="kpi-value">{{ number_format($kpis['anexosTotal']) }}</div>
                <div class="kpi-note">{{ $kpis['anexosMb'] }} MB • {{ number_format($kpis['anexosImagens']) }} imagens</div>
            </td>
        </tr>
    </table>

    <div class="spacer-10"></div>

    <h2>Gráficos</h2>

    <table class="chart-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="chart-card" style="width:100%;">
                <p class="chart-title">Mensagens por dia</p>
                <div class="img-wrap chart-fixed-360">
                    <img class="chart-img" src="{{ $urlLine }}" alt="Mensagens por dia">
                </div>
            </td>
        </tr>

        <tr>
            <td style="width:100%; padding:0;">
                <table class="chart-table" cellpadding="0" cellspacing="0" style="border-spacing: 0; width:100%;">
                    <tr>
                        <td class="chart-card" style="width:38%; padding:10px; border-right:0; border-top-right-radius:0; border-bottom-right-radius:0;">
                            <p class="chart-title">DM vs Grupo</p>
                            <div class="img-wrap chart-fixed-260">
                                <img class="chart-img" src="{{ $urlDonut }}" alt="DM vs Grupo">
                            </div>
                        </td>
                        <td class="chart-card" style="width:62%; padding:10px; border-left:0; border-top-left-radius:0; border-bottom-left-radius:0;">
                            <p class="chart-title">Top utilizadores (mensagens)</p>
                            <div class="img-wrap chart-fixed-260">
                                <img class="chart-img" src="{{ $urlBar }}" alt="Top utilizadores">
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="spacer-14"></div>

    <h2>Top utilizadores</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Utilizador</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topUsers as $u)
                <tr>
                    <td>{{ $u->nome }}</td>
                    <td class="right" style="font-weight:800;">{{ number_format($u->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="color:#6b7280;">Sem dados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="spacer-10"></div>

    <h2>Top grupos</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Grupo</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topGrupos as $g)
                <tr>
                    <td>{{ $g->nome }}</td>
                    <td class="right" style="font-weight:800;">{{ number_format($g->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="color:#6b7280;">Sem dados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Este relatório é gerado automaticamente com base na base de dados do Communico.
        Mensagens apagadas (soft delete) não são incluídas.
    </div>

</body>
</html>
