<?php

namespace App\Services;

use App\Models\Mensagem;
use App\Models\Anexo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\CarbonPeriod;

class EstatisticasService
{
    /**
     * LEGACY (mantido por segurança / não refatorar sem pedido).
     * Nota: este método parece ser um fluxo antigo (params inicio/fim). Não é usado pelo módulo atual.
     */
    public function gerar(array $params): array
    {
        $start = $this->toCarbon($params['inicio'] ?? null, true) ?? now()->subDays(7)->startOfDay();
        $end   = $this->toCarbon($params['fim'] ?? null, false) ?? now()->endOfDay();

        // Garantir ordem (se o user troca datas)
        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $start = $start->copy()->startOfDay();
        $end   = $end->copy()->endOfDay();

        $grupoId = !empty($params['grupo_id']) ? (int) $params['grupo_id'] : null;
        $userId  = !empty($params['user_id']) ? (int) $params['user_id'] : null;

        $daysLen = (int) $start->diffInDays($end) + 1;

        // Query base (mensagens) com join a conversas para DM vs Grupo e filtros por grupo
        $baseMensagens = Mensagem::query()
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->whereBetween('mensagens.created_at', [$start, $end])
            ->when($userId, fn ($q) => $q->where('mensagens.emissor_id', $userId))
            ->when($grupoId, fn ($q) => $q->where('conversas.grupo_id', $grupoId));

        $totalMensagens = (clone $baseMensagens)->count('mensagens.id');

        $dmCount = (clone $baseMensagens)
            ->where('conversas.tipo', 'dm')
            ->count('mensagens.id');

        $grupoCount = (clone $baseMensagens)
            ->where('conversas.tipo', 'grupo')
            ->count('mensagens.id');

        $avgPerDay = $daysLen > 0 ? round($totalMensagens / $daysLen, 2) : 0;

        // Período anterior (mesma duração, imediatamente antes do start)
        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $start->copy()->subDays($daysLen)->startOfDay();

        $prevTotal = Mensagem::query()
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->whereBetween('mensagens.created_at', [$prevStart, $prevEnd])
            ->when($userId, fn ($q) => $q->where('mensagens.emissor_id', $userId))
            ->when($grupoId, fn ($q) => $q->where('conversas.grupo_id', $grupoId))
            ->count('mensagens.id');

        $deltaPct = null;
        if ($prevTotal > 0) {
            $deltaPct = round((($totalMensagens - $prevTotal) / $prevTotal) * 100, 1);
        }

        // Anexos (filtram pelas mesmas mensagens / conversas)
        $baseAnexos = Anexo::query()
            ->join('mensagens', 'anexos.mensagem_id', '=', 'mensagens.id')
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->whereNull('mensagens.deleted_at')
            ->whereBetween('mensagens.created_at', [$start, $end])
            ->when($userId, fn ($q) => $q->where('mensagens.emissor_id', $userId))
            ->when($grupoId, fn ($q) => $q->where('conversas.grupo_id', $grupoId));

        $anexosTotal = (clone $baseAnexos)->count('anexos.id');
        $anexosBytes = (int) ((clone $baseAnexos)->sum('anexos.tamanho_bytes') ?? 0);
        $anexosImagens = (clone $baseAnexos)
            ->where('anexos.mime_type', 'like', 'image/%')
            ->count('anexos.id');

        $anexosMb = round($anexosBytes / 1024 / 1024, 2);

        // Mensagens por dia (série temporal)
        $porDiaRaw = (clone $baseMensagens)
            ->selectRaw('DATE(mensagens.created_at) as dia, COUNT(mensagens.id) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia')
            ->toArray();

        $labelsDia = [];
        $dataDia = [];
        foreach (CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()) as $d) {
            $key = $d->toDateString();
            $labelsDia[] = $d->format('d/m');
            $dataDia[] = (int) ($porDiaRaw[$key] ?? 0);
        }

        // Top utilizadores
        $topUsers = (clone $baseMensagens)
            ->join('users', 'mensagens.emissor_id', '=', 'users.id')
            ->when(Schema::hasColumn('users', 'is_system'), fn($q) => $q->where('users.is_system', 0))
            ->select('users.id', 'users.nome', DB::raw('COUNT(mensagens.id) as total'))
            ->groupBy('users.id', 'users.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Top grupos (apenas conversas de grupo)
        $topGrupos = (clone $baseMensagens)
            ->where('conversas.tipo', 'grupo')
            ->join('grupos', 'conversas.grupo_id', '=', 'grupos.id')
            ->select('grupos.id', 'grupos.nome', DB::raw('COUNT(mensagens.id) as total'))
            ->groupBy('grupos.id', 'grupos.nome')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $chartData = [
            'mensagensPorDia' => [
                'labels' => $labelsDia,
                'data' => $dataDia,
            ],
            'dmVsGrupo' => [
                'labels' => ['DM', 'Grupo'],
                'data' => [(int) $dmCount, (int) $grupoCount],
            ],
            'topUsers' => [
                'labels' => $topUsers->pluck('nome')->values(),
                'data' => $topUsers->pluck('total')->map(fn ($v) => (int) $v)->values(),
            ],
        ];

        return [
            'f' => [
                'start' => $start,
                'end' => $end,
                'daysLen' => $daysLen,
                'grupoId' => $grupoId,
                'userId' => $userId,
                'range' => $params['range'] ?? null,
            ],
            'kpis' => [
                'totalMensagens' => (int) $totalMensagens,
                'avgPerDay' => $avgPerDay,
                'dm' => (int) $dmCount,
                'grupo' => (int) $grupoCount,
                'deltaPct' => $deltaPct,
                'anexosTotal' => (int) $anexosTotal,
                'anexosMb' => $anexosMb,
                'anexosImagens' => (int) $anexosImagens,
            ],
            'topUsers' => $topUsers,
            'topGrupos' => $topGrupos,
            'chartData' => $chartData,
        ];
    }

    private function toCarbon($value, bool $startOfDay): ?Carbon
    {
        if (!$value) return null;

        if ($value instanceof Carbon) {
            return $startOfDay ? $value->copy()->startOfDay() : $value->copy()->endOfDay();
        }

        try {
            $c = Carbon::parse($value);
            return $startOfDay ? $c->startOfDay() : $c->endOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    public function buildFilters(Request $request): array
    {
        $range = $request->string('range')->toString(); // 7|30|90|custom
        $grupoId = $request->integer('grupo_id') ?: null;
        $userId  = $request->integer('user_id') ?: null;

        $now = now();

        $startProvided = $request->filled('start');
        $endProvided   = $request->filled('end');

        // ✅ Default sólido: se não vier range e não vierem datas → 7 dias
        if ($range === '') {
            $range = ($startProvided || $endProvided) ? 'custom' : '7';
        }

        if (in_array($range, ['7','30','90'], true)) {
            $days  = (int) $range;
            $start = $now->copy()->subDays($days - 1)->startOfDay();
            $end   = $now->copy()->endOfDay();
        } else {
            $start = $startProvided
                ? Carbon::parse($request->string('start'))->startOfDay()
                : $now->copy()->subDays(6)->startOfDay();

            $end = $endProvided
                ? Carbon::parse($request->string('end'))->endOfDay()
                : $now->copy()->endOfDay();

            $range = 'custom';
        }

        // ✅ Se o utilizador trocar as datas, corrigir aqui (impacta daysLen + prev period)
        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $daysLen = (int) $start->diffInDays($end) + 1;

        $prevEnd = $start->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($daysLen - 1)->startOfDay();

        return compact('range','start','end','grupoId','userId','daysLen','prevStart','prevEnd');
    }

    private function baseMensagemQuery(array $f)
    {
        return Mensagem::query()
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->whereBetween('mensagens.created_at', [$f['start'], $f['end']])
            ->when($f['userId'], fn($q) => $q->where('mensagens.emissor_id', $f['userId']))
            ->when($f['grupoId'], fn($q) => $q->where('conversas.grupo_id', $f['grupoId']));
    }

    private function baseMensagemQueryPrev(array $f)
    {
        return Mensagem::query()
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->whereBetween('mensagens.created_at', [$f['prevStart'], $f['prevEnd']])
            ->when($f['userId'], fn($q) => $q->where('mensagens.emissor_id', $f['userId']))
            ->when($f['grupoId'], fn($q) => $q->where('conversas.grupo_id', $f['grupoId']));
    }

    public function kpis(array $f): array
    {
        $total = (clone $this->baseMensagemQuery($f))->count();
        $prevTotal = (clone $this->baseMensagemQueryPrev($f))->count();

        $avgPerDay = $f['daysLen'] > 0 ? round($total / $f['daysLen'], 2) : 0;

        $dmGrupo = (clone $this->baseMensagemQuery($f))
            ->selectRaw("conversas.tipo, COUNT(*) as total")
            ->groupBy('conversas.tipo')
            ->pluck('total', 'tipo')
            ->toArray();

        $dm = (int)($dmGrupo['dm'] ?? 0);
        $grupo = (int)($dmGrupo['grupo'] ?? 0);

        $anexos = Anexo::query()
            ->join('mensagens', 'anexos.mensagem_id', '=', 'mensagens.id')
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->whereBetween('mensagens.created_at', [$f['start'], $f['end']])
            ->when($f['userId'], fn($q) => $q->where('mensagens.emissor_id', $f['userId']))
            ->when($f['grupoId'], fn($q) => $q->where('conversas.grupo_id', $f['grupoId']))
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(anexos.tamanho_bytes),0) as total_bytes,
                SUM(CASE WHEN anexos.mime_type LIKE 'image/%' THEN 1 ELSE 0 END) as total_imagens
            ")
            ->first();

        $deltaPct = ($prevTotal > 0) ? round((($total - $prevTotal) / $prevTotal) * 100, 1) : null;

        return [
            'totalMensagens' => $total,
            'prevTotalMensagens' => $prevTotal,
            'deltaPct' => $deltaPct,
            'avgPerDay' => $avgPerDay,
            'dm' => $dm,
            'grupo' => $grupo,
            'anexosTotal' => (int)($anexos->total ?? 0),
            'anexosMb' => round(((int)($anexos->total_bytes ?? 0)) / 1024 / 1024, 2),
            'anexosImagens' => (int)($anexos->total_imagens ?? 0),
        ];
    }

    public function mensagensPorDia(array $f): array
    {
        $rows = (clone $this->baseMensagemQuery($f))
            ->selectRaw("DATE(mensagens.created_at) as dia, COUNT(*) as total")
            ->groupBy(DB::raw("DATE(mensagens.created_at)"))
            ->orderBy('dia')
            ->get();

        return [
            'labels' => $rows->pluck('dia')->map(fn($d) => (string)$d)->toArray(),
            'data' => $rows->pluck('total')->map(fn($n) => (int)$n)->toArray(),
        ];
    }

    public function topUsers(array $f, int $limit = 10)
    {
        return (clone $this->baseMensagemQuery($f))
            ->join('users', 'mensagens.emissor_id', '=', 'users.id')
            ->when(Schema::hasColumn('users', 'is_system'), fn($q) => $q->where('users.is_system', 0))
            ->selectRaw("users.id, users.nome, COUNT(*) as total")
            ->groupBy('users.id', 'users.nome')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    public function topGrupos(array $f, int $limit = 10)
    {
        return (clone $this->baseMensagemQuery($f))
            ->where('conversas.tipo', 'grupo')
            ->leftJoin('grupos', 'conversas.grupo_id', '=', 'grupos.id')
            ->selectRaw("grupos.id, COALESCE(grupos.nome,'(Sem nome)') as nome, COUNT(*) as total")
            ->groupBy('grupos.id', 'grupos.nome')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    public function datasetsRaw(array $f): array
    {
        $mensagens = (clone $this->baseMensagemQuery($f))
            ->leftJoin('users', 'mensagens.emissor_id', '=', 'users.id')
            ->leftJoin('grupos', 'conversas.grupo_id', '=', 'grupos.id')
            ->select([
                'mensagens.id',
                'mensagens.created_at',
                'conversas.id as conversa_id',
                'conversas.tipo as conversa_tipo',
                'grupos.nome as grupo',
                'users.nome as emissor',
                'mensagens.conteudo',
            ])->orderBy('mensagens.created_at')
            ->get();

        $anexos = Anexo::query()
            ->join('mensagens', 'anexos.mensagem_id', '=', 'mensagens.id')
            ->join('conversas', 'mensagens.conversa_id', '=', 'conversas.id')
            ->leftJoin('users', 'mensagens.emissor_id', '=', 'users.id')
            ->leftJoin('grupos', 'conversas.grupo_id', '=', 'grupos.id')
            ->whereBetween('mensagens.created_at', [$f['start'], $f['end']])
            ->when($f['userId'], fn($q) => $q->where('mensagens.emissor_id', $f['userId']))
            ->when($f['grupoId'], fn($q) => $q->where('conversas.grupo_id', $f['grupoId']))
            ->select([
                'anexos.id',
                'mensagens.created_at',
                'anexos.nome_original',
                'anexos.mime_type',
                'anexos.tamanho_bytes',
                'users.nome as emissor',
                'grupos.nome as grupo',
            ])->orderBy('mensagens.created_at')
            ->get();

        return compact('mensagens','anexos');
    }
}
