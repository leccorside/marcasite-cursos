<?php

namespace App\Http\Controllers;

use App\Http\Requests\CursoRequest;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CursoController extends Controller
{
    /**
     * Listar cursos com paginação
     */
    public function index(Request $request): JsonResponse
    {
        $query = Curso::query();

        // Busca por nome
        if ($request->has('search') && $request->search) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        // Filtro por status (ativo/inativo)
        if ($request->has('ativo')) {
            $query->where('ativo', $request->ativo === 'true' || $request->ativo === '1');
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $cursos = $query->paginate($perPage);

        return response()->json([
            'data' => $cursos->items(),
            'current_page' => $cursos->currentPage(),
            'last_page' => $cursos->lastPage(),
            'per_page' => $cursos->perPage(),
            'total' => $cursos->total(),
        ]);
    }

    /**
     * Exibir um curso específico
     */
    public function show(Curso $curso): JsonResponse
    {
        return response()->json($curso->load('materiais'));
    }

    /**
     * Criar um novo curso
     */
    public function store(CursoRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Upload da Thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('cursos/thumbnails', 'public');
        }

        $data['vagas_disponiveis'] = $data['vagas_total'];
        
        $curso = \DB::transaction(function () use ($data, $request) {
            $curso = Curso::create($data);

            // Upload de Materiais
            if ($request->hasFile('materiais')) {
                foreach ($request->file('materiais') as $index => $arquivo) {
                    $curso->materiais()->create([
                        'nome' => $arquivo->getClientOriginalName(),
                        'arquivo' => $arquivo->store('cursos/materiais', 'public'),
                        'tipo_arquivo' => $arquivo->getClientOriginalExtension(),
                        'ordem' => $index,
                    ]);
                }
            }

            return $curso;
        });

        return response()->json([
            'message' => 'Curso criado com sucesso',
            'data' => $curso->load('materiais'),
        ], 201);
    }

    /**
     * Atualizar um curso
     */
    public function update(CursoRequest $request, Curso $curso): JsonResponse
    {
        $data = $request->validated();

        // Se vagas_total foi alterado, recalcular vagas_disponiveis
        if (isset($data['vagas_total']) && $data['vagas_total'] != $curso->vagas_total) {
            $diferenca = $data['vagas_total'] - $curso->vagas_total;
            $data['vagas_disponiveis'] = max(0, $curso->vagas_disponiveis + $diferenca);
        }

        // Upload da nova Thumbnail se houver
        if ($request->hasFile('thumbnail')) {
            // Remover antiga
            if ($curso->thumbnail) {
                \Storage::disk('public')->delete($curso->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('cursos/thumbnails', 'public');
        }

        \DB::transaction(function () use ($data, $request, $curso) {
            $curso->update($data);

            // Remover materiais marcados para exclusão
            if ($request->has('materiais_removidos')) {
                foreach ($request->materiais_removidos as $id) {
                    $material = \App\Models\CursoMaterial::find($id);
                    if ($material && $material->curso_id === $curso->id) {
                        \Storage::disk('public')->delete($material->arquivo);
                        $material->delete();
                    }
                }
            }

            // Upload de novos materiais
            if ($request->hasFile('materiais')) {
                foreach ($request->file('materiais') as $index => $arquivo) {
                    $curso->materiais()->create([
                        'nome' => $arquivo->getClientOriginalName(),
                        'arquivo' => $arquivo->store('cursos/materiais', 'public'),
                        'tipo_arquivo' => $arquivo->getClientOriginalExtension(),
                        'ordem' => $curso->materiais()->count() + $index,
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Curso atualizado com sucesso',
            'data' => $curso->load('materiais'),
        ]);
    }

    /**
     * Listar inscritos de um curso específico
     */
    public function inscritos(Curso $curso): JsonResponse
    {
        $inscritos = $curso->inscricoes()
            ->with('aluno')
            ->get()
            ->map(function ($inscricao) {
                return [
                    'id' => $inscricao->id,
                    'aluno_nome' => $inscricao->aluno->nome,
                    'aluno_email' => $inscricao->aluno->email,
                    'aluno_cpf' => $inscricao->aluno->cpf_formatado,
                    'status' => $inscricao->status,
                    'data_inscricao' => $inscricao->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json($inscritos);
    }

    /**
     * Excluir um curso (soft delete)
     */
    public function destroy(Curso $curso): JsonResponse
    {
        // Verificar se há inscrições ativas
        $inscricoesAtivas = $curso->inscricoes()
            ->whereIn('status', ['pendente', 'pago'])
            ->count();

        if ($inscricoesAtivas > 0) {
            return response()->json([
                'message' => 'Não é possível excluir o curso. Existem inscrições ativas associadas a ele.',
            ], 422);
        }

        $curso->delete();

        return response()->json([
            'message' => 'Curso excluído com sucesso',
        ]);
    }
}
