<?php

namespace App\Exports\Estatisticas;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Estatisticas\Sheets\ResumoSheet;
use App\Exports\Estatisticas\Sheets\MensagensSheet;
use App\Exports\Estatisticas\Sheets\AnexosSheet;
use App\Exports\Estatisticas\Sheets\SerieMensagensPorDiaSheet;
use App\Exports\Estatisticas\Sheets\TopUsersSheet;
use App\Exports\Estatisticas\Sheets\TopGruposSheet;

class EstatisticasExcelExport implements WithMultipleSheets
{
    public function __construct(
        private array $f,
        private array $kpis,
        private array $serie,
        private $topUsers,
        private $topGrupos,
        private array $raw
    ) {}

    public function sheets(): array
    {
        return [
            new ResumoSheet($this->f, $this->kpis),
            new MensagensSheet(collect($this->raw['mensagens'])),
            new AnexosSheet(collect($this->raw['anexos'])),
            new SerieMensagensPorDiaSheet($this->serie),
            new TopUsersSheet($this->topUsers),
            new TopGruposSheet($this->topGrupos),
        ];
    }
}
