<?php

namespace App\Exports\Estatisticas\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResumoSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private array $f, private array $kpis) {}

    public function title(): string
    {
        return 'Resumo';
    }

    public function array(): array
    {
        return [
            ['Relatório Communico — Estatísticas Gerais'],
            ['Intervalo', $this->f['start']->toDateString().' → '.$this->f['end']->toDateString()],
            ['Grupo ID', $this->f['grupoId'] ?? '—'],
            ['Utilizador ID', $this->f['userId'] ?? '—'],
            [],
            ['KPI', 'Valor'],
            ['Total mensagens', $this->kpis['totalMensagens']],
            ['Média/dia', $this->kpis['avgPerDay']],
            ['DM', $this->kpis['dm']],
            ['Grupo', $this->kpis['grupo']],
            ['Anexos (total)', $this->kpis['anexosTotal']],
            ['Anexos (MB)', $this->kpis['anexosMb']],
            ['Imagens', $this->kpis['anexosImagens']],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true]],
        ];
    }
}
