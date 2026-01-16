<?php

namespace App\Exports;

use App\Models\Curso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CursosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Curso::orderBy('nome')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Categoria',
            'Valor (R$)',
            'Vagas Total',
            'Vagas Disponíveis',
            'Início Inscrições',
            'Fim Inscrições',
            'Ativo',
            'Data de Cadastro',
        ];
    }

    public function map($curso): array
    {
        return [
            $curso->id,
            $curso->nome,
            $curso->categoria ?? '-',
            number_format($curso->valor, 2, ',', '.'),
            $curso->vagas_total,
            $curso->vagas_disponiveis,
            $curso->data_inicio_inscricoes ? \Carbon\Carbon::parse($curso->data_inicio_inscricoes)->format('d/m/Y') : '-',
            $curso->data_fim_inscricoes ? \Carbon\Carbon::parse($curso->data_fim_inscricoes)->format('d/m/Y') : '-',
            $curso->ativo ? 'Sim' : 'Não',
            $curso->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
