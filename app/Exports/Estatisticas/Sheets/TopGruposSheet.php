<?php

namespace App\Exports\Estatisticas\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TopGruposSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private $rows) {}

    public function title(): string
    {
        return 'Top grupos';
    }

    public function collection(): Collection
    {
        return collect($this->rows);
    }

    public function headings(): array
    {
        return ['ID', 'Nome', 'Total mensagens'];
    }

    public function map($row): array
    {
        return [
            $row->id ?? null,
            $row->nome ?? '—',
            (int)($row->total ?? 0),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
