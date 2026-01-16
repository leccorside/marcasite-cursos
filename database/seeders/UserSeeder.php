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
        // Criar usuário admin apenas se não existir (idempotente)
        // Os dados podem ser configurados via variáveis de ambiente no .env
        $adminEmail = env('ADMIN_EMAIL', 'admin@marcasite.com.br');
        $adminName = env('ADMIN_NAME', 'Administrador');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'tipo' => 'admin',
                'ativo' => true,
            ]
        );

        // Criar alguns usuários de teste (apenas se não existirem)
        // Evita criar duplicatas ao rodar o seeder múltiplas vezes
        if (User::where('tipo', 'admin')->count() < 6) {
            User::factory()->count(5)->create([
                'tipo' => 'admin',
            ]);
        }

        if (User::where('tipo', 'aluno')->count() < 21) {
            User::factory()->count(20)->create([
                'tipo' => 'aluno',
            ]);
        }
    }
}
