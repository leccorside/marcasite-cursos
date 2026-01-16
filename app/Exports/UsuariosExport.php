<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsuariosExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Email',
            'Tipo',
            'Ativo',
            'Data de Cadastro',
        ];
    }

    public function map($usuario): array
    {
        return [
            $usuario->id,
            $usuario->name,
            $usuario->email,
            $usuario->tipo === 'admin' ? 'Administrador' : 'Aluno',
            $usuario->ativo ? 'Sim' : 'Não',
            $usuario->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
