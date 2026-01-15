<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('categoria')->nullable();
            $table->decimal('valor', 10, 2);
            $table->integer('vagas_total');
            $table->integer('vagas_disponiveis')->default(0);
            $table->date('data_inicio_inscricoes');
            $table->date('data_fim_inscricoes');
            $table->string('thumbnail')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('ativo');
            $table->index('data_inicio_inscricoes');
            $table->index('data_fim_inscricoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
