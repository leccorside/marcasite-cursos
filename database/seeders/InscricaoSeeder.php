<?php

namespace Database\Seeders;

use App\Models\Aluno;
use App\Models\Curso;
use App\Models\Inscricao;
use App\Models\Pagamento;
use Illuminate\Database\Seeder;

class InscricaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cursos = Curso::all();
        $alunos = Aluno::all();

        // Criar inscrições com diferentes status
        foreach ($cursos as $curso) {
            // 30% de ocupação de vagas
            $numeroInscricoes = (int) ($curso->vagas_total * 0.3);
            
            for ($i = 0; $i < $numeroInscricoes && $i < $alunos->count(); $i++) {
                $aluno = $alunos->random();
                
                // Verificar se o aluno já está inscrito neste curso
                $jaInscrito = Inscricao::where('aluno_id', $aluno->id)
                    ->where('curso_id', $curso->id)
                    ->exists();
                
                if (!$jaInscrito) {
                    $status = fake()->randomElement(['pendente', 'pago', 'cancelado']);
                    
                    $inscricao = Inscricao::create([
                        'aluno_id' => $aluno->id,
                        'curso_id' => $curso->id,
                        'status' => $status,
                        'valor_pago' => $curso->valor,
                        'data_inscricao' => fake()->dateTimeBetween(
                            $curso->data_inicio_inscricoes,
                            now()
                        )->format('Y-m-d'),
                        'observacoes' => fake()->optional()->sentence(),
                    ]);

                    // Se a inscrição está paga, criar pagamento aprovado
                    if ($status === 'pago') {
                        Pagamento::create([
                            'inscricao_id' => $inscricao->id,
                            'transaction_id' => 'TEST-' . strtoupper(uniqid()),
                            'payment_id' => 'MP-' . rand(1000000, 9999999),
                            'status' => 'aprovado',
                            'metodo_pagamento' => fake()->randomElement(['credit_card', 'debit_card', 'pix']),
                            'valor' => $curso->valor,
                            'data_pagamento' => fake()->dateTimeBetween(
                                $inscricao->data_inscricao,
                                now()
                            ),
                            'dados_mercado_pago' => [
                                'status' => 'approved',
                                'status_detail' => 'accredited',
                                'payment_method_id' => 'visa',
                            ],
                        ]);
                    } elseif ($status === 'pendente') {
                        // Criar pagamento pendente
                        Pagamento::create([
                            'inscricao_id' => $inscricao->id,
                            'status' => 'pendente',
                            'valor' => $curso->valor,
                        ]);
                    }
                }
            }
        }

        // Atualizar vagas disponíveis de todos os cursos
        foreach ($cursos as $curso) {
            $curso->atualizarVagas();
        }
    }
}
