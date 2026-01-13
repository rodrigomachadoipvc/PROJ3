<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Relatório — Estatísticas Gerais</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0b1220; }
        .header { padding: 14px 16px; border-bottom: 2px solid #60A5FA; }
        .brand { font-size: 18px; font-weight: 700; }
        .sub { color: #374151; margin-top: 4px; }

        .meta { margin-top: 10px; font-size: 11px; color: #374151; }
        .meta strong { color: #111827; }

        .grid { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; vertical-align: top; }
        .kpi-title { font-size: 10px; color: #6b7280; font-weight: 700; text-transform: uppercase; }
        .kpi-value { font-size: 18px; font-weight: 800; margin-top: 6px; }
        .kpi-note { font-size: 10px; color: #6b7280; margin-top: 6px; }

        h2 { font-size: 14px; margin: 18px 0 8px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #e5e7eb; padding: 8px 6px; text-align: left; }
        th { background: #f9fafb; font-size: 11px; color: #374151; }

        .bars { margin-top: 8px; }
        .bar-row { margin: 6px 0; }
        .bar-label { width: 80px; display: inline-block; font-size: 10px; color: #374151; }
        .bar { display: inline-block; height: 10px; background: #2DD4BF; border-radius: 999px; }
        .bar2 { background: #60A5FA; }

        .footer { margin-top: 18px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>

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

    @php
        $delta = $kpis['deltaPct'];
        $deltaLabel = is_null($delta) ? '—' : (($delta >= 0 ? '+' : '') . $delta . '%');
        $max = 0;
        foreach (($serie['data'] ?? []) as $n) { if ($n > $max) $max = $n; }
        $max = $max > 0 ? $max : 1;
    @endphp

    <table class="grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="card" style="width: 20%;">
                <div class="kpi-title">Total mensagens</div>
                <div class="kpi-value">{{ number_format($kpis['totalMensagens']) }}</div>
                <div class="kpi-note">Variação vs anterior: {{ $deltaLabel }}</div>
            </td>
            <td style="width: 2%"></td>
            <td class="card" style="width: 20%;">
                <div class="kpi-title">Média por dia</div>
                <div class="kpi-value">{{ $kpis['avgPerDay'] }}</div>
                <div class="kpi-note">Base: {{ $f['daysLen'] }} dia(s)</div>
            </td>
            <td style="width: 2%"></td>
            <td class="card" style="width: 20%;">
                <div class="kpi-title">DM / Grupo</div>
                <div class="kpi-value">{{ number_format($kpis['dm']) }} / {{ number_format($kpis['grupo']) }}</div>
                <div class="kpi-note">Distribuição de conversa</div>
            </td>
            <td style="width: 2%"></td>
            <td class="card" style="width: 20%;">
                <div class="kpi-title">Anexos</div>
                <div class="kpi-value">{{ number_format($kpis['anexosTotal']) }}</div>
                <div class="kpi-note">{{ $kpis['anexosMb'] }} MB • {{ number_format($kpis['anexosImagens']) }} imagens</div>
            </td>
        </tr>
    </table>

    <h2>Mensagens por dia (representação)</h2>
    <div class="bars">
        @php
            $labels = $serie['labels'] ?? [];
            $data = $serie['data'] ?? [];
        @endphp

        @forelse ($labels as $i => $dia)
            @php
                $v = (int)($data[$i] ?? 0);
                $w = (int)round(($v / $max) * 420); // largura em px
            @endphp
            <div class="bar-row">
                <span class="bar-label">{{ substr($dia, 5) }}</span>
                <span class="bar" style="width: {{ $w }}px;"></span>
                <span style="font-size: 10px; color:#374151;">&nbsp;{{ $v }}</span>
            </div>
        @empty
            <div style="color:#6b7280;">Sem dados para o intervalo selecionado.</div>
        @endforelse
    </div>

    <h2>Top utilizadores</h2>
    <table>
        <thead>
            <tr>
                <th>Utilizador</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topUsers as $u)
                <tr>
                    <td>{{ $u->nome }}</td>
                    <td style="text-align:right; font-weight:700;">{{ number_format($u->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" style="color:#6b7280;">Sem dados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Top grupos</h2>
    <table>
        <thead>
            <tr>
                <th>Grupo</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topGrupos as $g)
                <tr>
                    <td>{{ $g->nome }}</td>
                    <td style="text-align:right; font-weight:700;">{{ number_format($g->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" style="color:#6b7280;">Sem dados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Este relatório é gerado automaticamente com base na base de dados do Communico. Mensagens apagadas (soft delete) não são incluídas.
    </div>

</body>
</html>
