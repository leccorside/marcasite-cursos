<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cursos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
        'valor',
        'vagas_total',
        'vagas_disponiveis',
        'data_inicio_inscricoes',
        'data_fim_inscricoes',
        'thumbnail',
        'ativo',
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
            'vagas_total' => 'integer',
            'vagas_disponiveis' => 'integer',
            'data_inicio_inscricoes' => 'date',
            'data_fim_inscricoes' => 'date',
            'ativo' => 'boolean',
        ];
    }

    /**
     * Relacionamento: Curso tem muitas Inscrições
     */
    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class);
    }

    /**
     * Relacionamento: Curso tem muitos Materiais
     */
    public function materiais(): HasMany
    {
        return $this->hasMany(CursoMaterial::class)->orderBy('ordem');
    }

    /**
     * Scope para cursos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para cursos com inscrições abertas
     */
    public function scopeInscricoesAbertas($query)
    {
        $hoje = now()->format('Y-m-d');
        return $query->where('ativo', true)
            ->where('data_inicio_inscricoes', '<=', $hoje)
            ->where('data_fim_inscricoes', '>=', $hoje)
            ->where('vagas_disponiveis', '>', 0);
    }

    /**
     * Verifica se o curso tem vagas disponíveis
     */
    public function temVagas(): bool
    {
        return $this->vagas_disponiveis > 0;
    }

    /**
     * Verifica se as inscrições estão abertas
     */
    public function inscricoesAbertas(): bool
    {
        $hoje = now()->format('Y-m-d');
        return $this->ativo 
            && $this->data_inicio_inscricoes <= $hoje
            && $this->data_fim_inscricoes >= $hoje
            && $this->temVagas();
    }

    /**
     * Atualiza vagas disponíveis
     */
    public function atualizarVagas(): void
    {
        $inscritos = $this->inscricoes()
            ->whereIn('status', ['pendente', 'pago'])
            ->count();
        
        $this->vagas_disponiveis = max(0, $this->vagas_total - $inscritos);
        $this->save();
    }
}
