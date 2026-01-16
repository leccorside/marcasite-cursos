<?php

namespace App\Exports;

use App\Models\Curso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CourseExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Curso::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Categoria',
            'Valor',
            'Vagas Total',
            'Vagas Disponíveis',
            'Data Início Inscrições',
            'Data Fim Inscrições',
            'Ativo',
        ];
    }

    public function map($curso): array
    {
        return [
            $curso->id,
            $curso->nome,
            $curso->categoria,
            'R$ ' . number_format($curso->valor, 2, ',', '.'),
            $curso->vagas_total,
            $curso->vagas_disponiveis,
            $curso->data_inicio_inscricoes->format('d/m/Y'),
            $curso->data_fim_inscricoes->format('d/m/Y'),
            $curso->ativo ? 'Sim' : 'Não',
        ];
    }
}
