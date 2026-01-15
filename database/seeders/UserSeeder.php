<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@marcasite.com.br',
            'password' => Hash::make('password'),
            'tipo' => 'admin',
            'ativo' => true,
        ]);

        // Criar alguns usuários de teste
        User::factory()->count(5)->create([
            'tipo' => 'admin',
        ]);

        User::factory()->count(20)->create([
            'tipo' => 'aluno',
        ]);
    }
}
