<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\User;
use App\Models\Inscricao;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Obter estatísticas do dashboard administrativo
     */
    public function stats(Request $request): JsonResponse
    {
        $filtroCurso = $request->get('curso_id');
        $filtroDataInicio = $request->get('data_inicio');
        $filtroDataFim = $request->get('data_fim');

        // Base query para inscrições
        $queryInscricoes = Inscricao::query();
        
        if ($filtroCurso) {
            $queryInscricoes->where('curso_id', $filtroCurso);
        }

        if ($filtroDataInicio) {
            $queryInscricoes->whereDate('created_at', '>=', $filtroDataInicio);
        }

        if ($filtroDataFim) {
            $queryInscricoes->whereDate('created_at', '<=', $filtroDataFim);
        }

        // Estatísticas Gerais
        $totalCursos = Curso::count();
        $totalCursosAtivos = Curso::where('ativo', true)->count();
        $totalUsuarios = User::count();
        $totalAlunos = User::where('tipo', 'aluno')->count();
        $totalAdmins = User::where('tipo', 'admin')->count();
        
        $totalInscricoes = $queryInscricoes->count();
        $inscricoesPagas = (clone $queryInscricoes)->where('status', 'pago')->count();
        $inscricoesPendentes = (clone $queryInscricoes)->where('status', 'pendente')->count();
        $inscricoesCanceladas = (clone $queryInscricoes)->where('status', 'cancelado')->count();

        // Financeiro
        $receitaTotal = (clone $queryInscricoes)
            ->where('status', 'pago')
            ->sum('valor_pago');
        
        $receitaMesAtual = (clone $queryInscricoes)
            ->where('status', 'pago')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('valor_pago');

        // Gráfico de Inscrições por Mês (últimos 12 meses)
        $inscricoesPorMes = (clone $queryInscricoes)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function($item) {
                try {
                    return [
                        'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M/Y'),
                        'total' => (int) $item->total
                    ];
                } catch (\Exception $e) {
                    return [
                        'mes' => $item->mes,
                        'total' => (int) $item->total
                    ];
                }
            })
            ->values();

        // Gráfico de Receita por Mês
        $receitaPorMes = (clone $queryInscricoes)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('COALESCE(SUM(valor_pago), 0) as total')
            )
            ->where('status', 'pago')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(function($item) {
                try {
                    return [
                        'mes' => Carbon::createFromFormat('Y-m', $item->mes)->format('M/Y'),
                        'total' => (float) ($item->total ?? 0)
                    ];
                } catch (\Exception $e) {
                    return [
                        'mes' => $item->mes,
                        'total' => (float) ($item->total ?? 0)
                    ];
                }
            })
            ->values();

        // Gráfico de Status de Inscrições
        $statusInscricoes = [
            ['status' => 'Pago', 'total' => $inscricoesPagas],
            ['status' => 'Pendente', 'total' => $inscricoesPendentes],
            ['status' => 'Cancelado', 'total' => $inscricoesCanceladas],
        ];

        // Top 5 Cursos mais populares (por número de inscrições pagas)
        $topCursosQuery = Curso::withCount([
            'inscricoes as inscricoes_pagas' => function($query) use ($filtroDataInicio, $filtroDataFim) {
                $query->where('status', 'pago');
                if ($filtroDataInicio) {
                    $query->whereDate('created_at', '>=', $filtroDataInicio);
                }
                if ($filtroDataFim) {
                    $query->whereDate('created_at', '<=', $filtroDataFim);
                }
            }
        ]);
        
        if ($filtroCurso) {
            $topCursosQuery->where('id', $filtroCurso);
        }
        
        $topCursos = $topCursosQuery
            ->orderBy('inscricoes_pagas', 'desc')
            ->limit(5)
            ->get()
            ->map(function($curso) {
                return [
                    'id' => $curso->id,
                    'nome' => $curso->nome,
                    'total_inscricoes' => $curso->inscricoes_pagas ?? 0
                ];
            });

        // Inscrições por Curso (se não houver filtro de curso específico)
        $inscricoesPorCursoQuery = Curso::withCount([
            'inscricoes as total_inscricoes' => function($query) use ($filtroDataInicio, $filtroDataFim) {
                if ($filtroDataInicio) {
                    $query->whereDate('created_at', '>=', $filtroDataInicio);
                }
                if ($filtroDataFim) {
                    $query->whereDate('created_at', '<=', $filtroDataFim);
                }
            }
        ]);
        
        if ($filtroCurso) {
            $inscricoesPorCursoQuery->where('id', $filtroCurso);
        }
        
        $inscricoesPorCurso = $inscricoesPorCursoQuery
            ->orderBy('total_inscricoes', 'desc')
            ->limit(10)
            ->get()
            ->map(function($curso) {
                return [
                    'nome' => $curso->nome,
                    'total' => $curso->total_inscricoes ?? 0
                ];
            });

        return response()->json([
            'cards' => [
                'total_cursos' => $totalCursos,
                'total_cursos_ativos' => $totalCursosAtivos,
                'total_usuarios' => $totalUsuarios,
                'total_alunos' => $totalAlunos,
                'total_admins' => $totalAdmins,
                'total_inscricoes' => $totalInscricoes,
                'inscricoes_pagas' => $inscricoesPagas,
                'inscricoes_pendentes' => $inscricoesPendentes,
                'inscricoes_canceladas' => $inscricoesCanceladas,
                'receita_total' => $receitaTotal,
                'receita_mes_atual' => $receitaMesAtual,
            ],
            'graficos' => [
                'inscricoes_por_mes' => $inscricoesPorMes,
                'receita_por_mes' => $receitaPorMes,
                'status_inscricoes' => $statusInscricoes,
                'top_cursos' => $topCursos,
                'inscricoes_por_curso' => $inscricoesPorCurso,
            ]
        ]);
    }
}
