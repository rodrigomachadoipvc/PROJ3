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

class MensagensSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private Collection $rows) {}

    public function title(): string
    {
        return 'Mensagens';
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
            'Conversa ID',
            'Tipo Conversa',
            'Grupo',
            'Emissor',
            'Conteúdo',
        ];
    }

    public function map($row): array
    {
        // $row pode ser stdClass (da query com joins). Aceder por propriedade.
        return [
            $row->id ?? null,
            isset($row->created_at) ? (string)$row->created_at : null,
            $row->conversa_id ?? null,
            $row->conversa_tipo ?? null,
            $row->grupo ?? '—',
            $row->emissor ?? '—',
            $row->conteudo ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
