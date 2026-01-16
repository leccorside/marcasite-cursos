<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pagamentos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inscricao_id',
        'transaction_id',
        'payment_id',
        'status',
        'metodo_pagamento',
        'valor',
        'dados_mercado_pago',
        'data_pagamento',
        'observacoes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'dados_mercado_pago' => 'array',
            'data_pagamento' => 'datetime',
        ];
    }

    /**
     * Relacionamento: Pagamento pertence a uma Inscrição
     */
    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(Inscricao::class);
    }

    /**
     * Verifica se o pagamento está aprovado
     */
    public function estaAprovado(): bool
    {
        return $this->status === 'aprovado';
    }

    /**
     * Verifica se o pagamento está pendente
     */
    public function estaPendente(): bool
    {
        return $this->status === 'pendente';
    }

    /**
     * Scope para pagamentos aprovados
     */
    public function scopeAprovados($query)
    {
        return $query->where('status', 'aprovado');
    }

    /**
     * Scope para pagamentos pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }
}
