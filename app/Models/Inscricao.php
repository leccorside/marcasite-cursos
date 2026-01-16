<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscricao extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inscricoes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'aluno_id',
        'curso_id',
        'status',
        'valor_pago',
        'data_inscricao',
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
            'valor_pago' => 'decimal:2',
            'data_inscricao' => 'date',
        ];
    }

    /**
     * Relacionamento: Inscrição pertence a um Aluno
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    /**
     * Relacionamento: Inscrição pertence a um Curso
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Relacionamento: Inscrição tem muitos Pagamentos
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Relacionamento: Inscrição tem um Pagamento aprovado (último)
     */
    public function pagamentoAprovado()
    {
        return $this->hasOne(Pagamento::class)
            ->where('status', 'aprovado')
            ->latest();
    }

    /**
     * Verifica se a inscrição está paga
     */
    public function estaPaga(): bool
    {
        return $this->status === 'pago';
    }

    /**
     * Verifica se a inscrição pode ser cancelada
     */
    public function podeCancelar(): bool
    {
        return in_array($this->status, ['pendente', 'pago']);
    }

    /**
     * Atualiza o status da inscrição baseado no pagamento
     */
    public function atualizarStatus(): void
    {
        $pagamentoAprovado = $this->pagamentos()
            ->where('status', 'aprovado')
            ->exists();

        if ($pagamentoAprovado && $this->status !== 'pago') {
            $this->status = 'pago';
            $this->save();
            
            // Atualiza vagas do curso
            $this->curso->atualizarVagas();
        }
    }
}
