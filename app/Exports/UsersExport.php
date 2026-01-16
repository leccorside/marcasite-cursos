<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nome',
            'Email',
            'Tipo',
            'Ativo',
            'CPF',
            'Criado em'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            ucfirst($user->tipo),
            $user->ativo ? 'Sim' : 'Não',
            $user->aluno ? $user->aluno->cpf : 'N/A',
            $user->created_at->format('d/m/Y H:i')
        ];
    }
}
