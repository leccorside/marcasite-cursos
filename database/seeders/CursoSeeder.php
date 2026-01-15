<?php

namespace Database\Seeders;

use App\Models\Curso;
use Illuminate\Database\Seeder;

class CursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cursos = [
            [
                'nome' => 'Curso de UX',
                'descricao' => 'Aprenda os fundamentos de User Experience Design e crie experiências incríveis para seus usuários.',
                'categoria' => 'Design',
                'valor' => 1250.00,
                'vagas_total' => 30,
                'vagas_disponiveis' => 28,
                'data_inicio_inscricoes' => now()->subDays(5),
                'data_fim_inscricoes' => now()->addDays(15),
                'ativo' => true,
            ],
            [
                'nome' => 'Curso de PHP',
                'descricao' => 'Do básico ao avançado em PHP. Desenvolva aplicações web robustas e modernas.',
                'categoria' => 'Programação',
                'valor' => 2250.00,
                'vagas_total' => 50,
                'vagas_disponiveis' => 35,
                'data_inicio_inscricoes' => now()->subDays(10),
                'data_fim_inscricoes' => now()->addDays(20),
                'ativo' => false,
            ],
            [
                'nome' => 'Curso de Photoshop',
                'descricao' => 'Domine o Photoshop e crie designs profissionais. Do básico ao nível avançado.',
                'categoria' => 'Design',
                'valor' => 1099.00,
                'vagas_total' => 25,
                'vagas_disponiveis' => 0,
                'data_inicio_inscricoes' => now()->subDays(3),
                'data_fim_inscricoes' => now()->addDays(7),
                'ativo' => true,
            ],
            [
                'nome' => 'Curso de Illustrator',
                'descricao' => 'Aprenda Illustrator do zero. Crie ilustrações vetoriais profissionais.',
                'categoria' => 'Design',
                'valor' => 1099.00,
                'vagas_total' => 40,
                'vagas_disponiveis' => 30,
                'data_inicio_inscricoes' => now()->subDays(2),
                'data_fim_inscricoes' => now()->addDays(18),
                'ativo' => true,
            ],
            [
                'nome' => 'Curso de Laravel',
                'descricao' => 'Desenvolva aplicações web modernas com Laravel. Framework PHP mais popular do mundo.',
                'categoria' => 'Programação',
                'valor' => 1899.00,
                'vagas_total' => 35,
                'vagas_disponiveis' => 20,
                'data_inicio_inscricoes' => now()->subDays(7),
                'data_fim_inscricoes' => now()->addDays(13),
                'ativo' => true,
            ],
            [
                'nome' => 'Curso de Vue.js',
                'descricao' => 'Framework JavaScript progressivo para construir interfaces de usuário modernas.',
                'categoria' => 'Programação',
                'valor' => 1599.00,
                'vagas_total' => 30,
                'vagas_disponiveis' => 25,
                'data_inicio_inscricoes' => now()->subDays(4),
                'data_fim_inscricoes' => now()->addDays(16),
                'ativo' => true,
            ],
        ];

        foreach ($cursos as $curso) {
            Curso::create($curso);
        }
    }
}
