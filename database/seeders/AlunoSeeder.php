<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AlunoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar alunos associados a usuários do tipo 'aluno'
        $usersAlunos = User::where('tipo', 'aluno')->get();

        foreach ($usersAlunos as $user) {
            Aluno::create([
                'nome' => $user->name,
                'email' => $user->email,
                'cpf' => $this->gerarCPF(),
                'telefone' => $this->gerarTelefone(),
                'celular' => $this->gerarCelular(),
                'data_nascimento' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
                'sexo' => fake()->randomElement(['M', 'F', 'Outro']),
                'cep' => $this->gerarCEP(),
                'endereco' => fake()->streetName(),
                'numero' => fake()->buildingNumber(),
                'complemento' => fake()->optional()->secondaryAddress(),
                'bairro' => fake()->citySuffix(),
                'cidade' => fake()->city(),
                'estado' => fake()->stateAbbr(),
                'user_id' => $user->id,
            ]);
        }

        // Criar alguns alunos sem usuário (caso necessário)
        Aluno::factory()->count(10)->create();
    }

    /**
     * Gera um CPF válido
     */
    private function gerarCPF(): string
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - ($d1 % 11);
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - ($d2 % 11);
        if ($d2 >= 10) {
            $d2 = 0;
        }
        return sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', $n1, $n2, $n3, $n4, $n5, $n6, $n7, $n8, $n9, $d1, $d2);
    }

    /**
     * Gera um telefone válido
     */
    private function gerarTelefone(): string
    {
        return sprintf('(%s) %s-%s', 
            rand(10, 99), 
            rand(1000, 9999), 
            rand(1000, 9999)
        );
    }

    /**
     * Gera um celular válido
     */
    private function gerarCelular(): string
    {
        return sprintf('(%s) %s-%s', 
            rand(10, 99), 
            rand(9000, 9999), 
            rand(1000, 9999)
        );
    }

    /**
     * Gera um CEP válido
     */
    private function gerarCEP(): string
    {
        return sprintf('%s%s%s%s%s-%s%s%s', 
            rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), 
            rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9)
        );
    }
}
