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

class AnexosSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private Collection $rows) {}

    public function title(): string
    {
        return 'Anexos';
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Data/Hora',
            'Nome Original',
            'MIME Type',
            'Tamanho (bytes)',
            'Emissor',
            'Grupo',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id ?? null,
            isset($row->created_at) ? (string)$row->created_at : null,
            $row->nome_original ?? '',
            $row->mime_type ?? '',
            $row->tamanho_bytes ?? 0,
            $row->emissor ?? '—',
            $row->grupo ?? '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
