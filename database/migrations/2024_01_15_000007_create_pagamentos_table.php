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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscricao_id')->constrained('inscricoes')->onDelete('cascade');
            $table->string('transaction_id')->unique()->nullable();
            $table->string('payment_id')->nullable(); // ID do Mercado Pago
            $table->enum('status', ['pendente', 'aprovado', 'recusado', 'cancelado', 'reembolsado'])->default('pendente');
            $table->enum('metodo_pagamento', ['credit_card', 'debit_card', 'pix', 'boleto', 'other'])->nullable();
            $table->decimal('valor', 10, 2);
            $table->json('dados_mercado_pago')->nullable(); // Dados completos da resposta do Mercado Pago
            $table->timestamp('data_pagamento')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('inscricao_id');
            $table->index('transaction_id');
            $table->index('payment_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
