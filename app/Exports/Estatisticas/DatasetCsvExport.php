<?php

namespace App\Exports\Estatisticas;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DatasetCsvExport implements FromCollection, WithHeadings, WithMapping, WithCustomCsvSettings
{
    public function __construct(private string $dataset, private $rows) {}

    public function collection()
    {
        return $this->rows instanceof Collection ? $this->rows : collect($this->rows);
    }

    public function headings(): array
    {
        return match ($this->dataset) {
            'mensagens' => ['id','created_at','conversa_id','conversa_tipo','grupo','emissor','conteudo'],
            'anexos' => ['id','created_at','nome_original','mime_type','tamanho_bytes','emissor','grupo'],
            default => [],
        };
    }

    public function map($row): array
    {
        $get = function ($key, $default = null) use ($row) {
            if (is_array($row)) return $row[$key] ?? $default;
            if (is_object($row)) return $row->{$key} ?? $default;
            return $default;
        };

        return match ($this->dataset) {
            'mensagens' => [
                $get('id'),
                (string)($get('created_at') ?? ''),
                $get('conversa_id'),
                $get('conversa_tipo'),
                $get('grupo', '—'),
                $get('emissor', '—'),
                $get('conteudo', ''),
            ],
            'anexos' => [
                $get('id'),
                (string)($get('created_at') ?? ''),
                $get('nome_original', ''),
                $get('mime_type', ''),
                (int)($get('tamanho_bytes', 0)),
                $get('emissor', '—'),
                $get('grupo', '—'),
            ],
            default => [],
        };
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',   // Excel PT-PT abre muito melhor assim
            'use_bom'   => true,  // evita problemas de acentos
        ];
    }
}
