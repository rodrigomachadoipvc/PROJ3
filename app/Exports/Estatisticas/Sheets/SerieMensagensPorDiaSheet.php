<?php

namespace App\Exports\Estatisticas\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SerieMensagensPorDiaSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private array $serie) {}

    public function title(): string
    {
        return 'Mensagens por dia';
    }

    public function array(): array
    {
        $rows = [['Dia', 'Total']];

        $labels = $this->serie['labels'] ?? [];
        $data   = $this->serie['data'] ?? [];

        foreach ($labels as $i => $dia) {
            $rows[] = [$dia, (int)($data[$i] ?? 0)];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
