<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\User;
use App\Services\EstatisticasService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as WriterType;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Estatisticas\EstatisticasExcelExport;
use App\Exports\Estatisticas\DatasetCsvExport;

class EstatisticasController extends Controller
{
    public function __construct(private EstatisticasService $stats) {}

    public function index(Request $request)
    {
        $f = $this->stats->buildFilters($request);

        $kpis = $this->stats->kpis($f);
        $serie = $this->stats->mensagensPorDia($f);
        $topUsers = $this->stats->topUsers($f);
        $topGrupos = $this->stats->topGrupos($f);

        $grupos = Grupo::orderBy('nome')->get(['id','nome']);
        $users  = User::orderBy('nome')->get(['id','nome']);

        $chartData = [
            'mensagensPorDia' => $serie,
            'dmVsGrupo' => [
                'labels' => ['DM', 'Grupo'],
                'data' => [(int)$kpis['dm'], (int)$kpis['grupo']],
            ],
            'topUsers' => [
                'labels' => $topUsers->pluck('nome')->toArray(),
                'data' => $topUsers->pluck('total')->map(fn($n)=>(int)$n)->toArray(),
            ],
        ];

        return view('admin.estatisticas.index', compact(
            'f','kpis','chartData','topUsers','topGrupos','grupos','users'
        ));
    }

    public function exportExcel(Request $request)
    {
        $f = $this->stats->buildFilters($request);
        $kpis = $this->stats->kpis($f);
        $serie = $this->stats->mensagensPorDia($f);
        $topUsers = $this->stats->topUsers($f);
        $topGrupos = $this->stats->topGrupos($f);
        $raw = $this->stats->datasetsRaw($f);

        return Excel::download(
            new EstatisticasExcelExport($f, $kpis, $serie, $topUsers, $topGrupos, $raw),
            'communico_estatisticas.xlsx'
        );
    }

    public function exportCsv(Request $request)
    {
        $dataset = $request->string('dataset')->toString(); // mensagens|anexos
        if (!in_array($dataset, ['mensagens','anexos'], true)) {
            abort(422, 'Dataset inválido.');
        }

        $f = $this->stats->buildFilters($request);
        $raw = $this->stats->datasetsRaw($f);

        $filename = "communico_{$dataset}.csv";

        return Excel::download(
            new DatasetCsvExport($dataset, $raw[$dataset]),
            $filename,
            WriterType::CSV,
            ['Content-Type' => 'text/csv']
        );
    }

    public function exportPdf(Request $request)
    {
        $f = $this->stats->buildFilters($request);

        $kpis = $this->stats->kpis($f);
        $serie = $this->stats->mensagensPorDia($f);
        $topUsers = $this->stats->topUsers($f);
        $topGrupos = $this->stats->topGrupos($f);

        try {
            $pdf = Pdf::loadView('admin.estatisticas.pdf', compact('f','kpis','serie','topUsers','topGrupos'))
                ->setPaper('a4', 'portrait');

            return $pdf->download('communico_relatorio_estatisticas.pdf');
        } catch (\Throwable $e) {
            report($e);
            abort(501, 'Exportação PDF indisponível neste ambiente. Confirma a versão do barryvdh/laravel-dompdf e dependências.');
        }
    }
}
