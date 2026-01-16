<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Inscricao;
use App\Models\Pagamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class InscricaoController extends Controller
{
    /**
     * Listar inscrições do aluno logado
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $aluno = $user->aluno;

        if (!$aluno) {
            return response()->json(['data' => []]);
        }

        $query = $aluno->inscricoes()->with('curso.materiais');

        if ($request->has('search') && $request->search) {
            $query->whereHas('curso', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->get('per_page', 9);
        $inscricoes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $inscricoes->items(),
            'current_page' => $inscricoes->currentPage(),
            'last_page' => $inscricoes->lastPage(),
            'per_page' => $inscricoes->perPage(),
            'total' => $inscricoes->total(),
        ]);
    }

    /**
     * Estatísticas do aluno logado
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();
        $aluno = $user->aluno;

        if (!$aluno) {
            return response()->json([
                'total_inscricoes' => 0,
                'total_pendentes' => 0,
            ]);
        }

        $totalInscricoes = $aluno->inscricoes()->count();
        $totalPendentes = $aluno->inscricoes()->where('status', 'pendente')->count();

        return response()->json([
            'total_inscricoes' => $totalInscricoes,
            'total_pendentes' => $totalPendentes,
        ]);
    }

    /**
     * Iniciar processo de inscrição em um curso
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
        ]);

        $user = Auth::user();
        $aluno = $user->aluno;

        if (!$aluno) {
            return response()->json(['message' => 'Perfil de aluno não encontrado.'], 404);
        }

        $curso = Curso::findOrFail($request->curso_id);

        \Log::info("Tentativa de inscrição: Curso ID {$curso->id}, Valor: {$curso->valor}");

        // Verificar se já está inscrito e pago
        $jaInscrito = Inscricao::where('aluno_id', $aluno->id)
            ->where('curso_id', $curso->id)
            ->whereIn('status', ['pago', 'pendente'])
            ->first();

        if ($jaInscrito) {
            if ($jaInscrito->status === 'pago') {
                return response()->json(['message' => 'Você já está inscrito neste curso.'], 422);
            }

            $valorFloat = (float) $curso->valor;

            // Se o curso agora é gratuito mas a inscrição estava pendente, aprova automaticamente
            if ($valorFloat <= 0) {
                $jaInscrito->update(['status' => 'pago', 'valor_pago' => 0]);
                $curso->atualizarVagas();
                return response()->json([
                    'message' => 'Sua inscrição foi confirmada! Como este curso é gratuito, seu acesso já está liberado.',
                    'inscricao' => $jaInscrito,
                    'checkout_url' => null
                ]);
            }

            // Se estiver pendente e não for gratuito, retorna o link de pagamento
            return response()->json([
                'message' => 'Você possui uma inscrição pendente para este curso.',
                'inscricao' => $jaInscrito,
                'checkout_url' => $this->gerarLinkPagamento($jaInscrito)
            ]);
        }

        // Verificar vagas
        if (!$curso->temVagas()) {
            return response()->json(['message' => 'Desculpe, não há mais vagas disponíveis para este curso.'], 422);
        }

        try {
            return DB::transaction(function () use ($aluno, $curso) {
                $valorFloat = (float) $curso->valor;
                $isGratuito = $valorFloat <= 0;

                // Criar inscrição
                $inscricao = Inscricao::create([
                    'aluno_id' => $aluno->id,
                    'curso_id' => $curso->id,
                    'status' => $isGratuito ? 'pago' : 'pendente',
                    'valor_pago' => $curso->valor,
                    'data_inscricao' => now(),
                ]);

                // Se for gratuito, atualiza as vagas e não gera link de pagamento
                if ($isGratuito) {
                    $curso->atualizarVagas();
                    return response()->json([
                        'message' => 'Inscrição realizada com sucesso! Como este curso é gratuito, seu acesso já está liberado.',
                        'inscricao' => $inscricao,
                        'checkout_url' => null
                    ], 201);
                }

                // Gerar preferência no Mercado Pago
                $checkoutUrl = $this->gerarLinkPagamento($inscricao);

                return response()->json([
                    'message' => 'Inscrição iniciada com sucesso!',
                    'inscricao' => $inscricao,
                    'checkout_url' => $checkoutUrl
                ], 201);
            });
        } catch (\Exception $e) {
            \Log::error('Erro ao criar inscrição: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao processar sua inscrição. Tente novamente.'], 500);
        }
    }

    /**
     * Gerar link de pagamento via Mercado Pago
     */
    private function gerarLinkPagamento(Inscricao $inscricao)
    {
        $accessToken = config('services.mercadopago.token');
        
        if (!$accessToken) {
            // Em desenvolvimento, se não houver token, retorna um link fictício
            return '#mock-payment-url-' . $inscricao->id;
        }

        try {
            MercadoPagoConfig::setAccessToken($accessToken);
            $client = new PreferenceClient();

            $appUrl = rtrim(config('app.url'), '/');
            
            $preference = $client->create([
                "items" => [
                    [
                        "id" => (string) $inscricao->curso->id,
                        "title" => $inscricao->curso->nome,
                        "quantity" => 1,
                        "unit_price" => (float) $inscricao->valor_pago
                    ]
                ],
                "payer" => [
                    "name" => $inscricao->aluno->nome,
                    "email" => $inscricao->aluno->email,
                ],
                "external_reference" => (string) $inscricao->id,
                "back_urls" => [
                    "success" => $appUrl . '/pagamento/retorno?status=success',
                    "failure" => $appUrl . '/pagamento/retorno?status=failure',
                    "pending" => $appUrl . '/pagamento/retorno?status=pending',
                ],
                "auto_return" => "all",
                "notification_url" => $appUrl . '/api/webhook/mercadopago',
            ]);

            return $preference->init_point;
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar preferência Mercado Pago: ' . $e->getMessage());
            if (method_exists($e, 'getApiResponse')) {
                \Log::error('MP Response: ' . json_encode($e->getApiResponse()?->getContent()));
            }
            return null;
        }
    }

    /**
     * Retorno do checkout (redirecionamento do Mercado Pago)
     */
    public function retorno(Request $request)
    {
        $status = $request->query('status');
        // Redireciona de volta para o Vue.js
        return redirect("/meus-cursos?payment_status={$status}");
    }

    /**
     * Webhook para receber notificações do Mercado Pago
     */
    public function webhook(Request $request)
    {
        // Log para debug
        \Log::info('Webhook Mercado Pago recebido:', $request->all());

        $accessToken = config('services.mercadopago.token');
        if (!$accessToken) return response()->json(['message' => 'Token não configurado'], 500);

        // O Mercado Pago envia o ID do pagamento no corpo da requisição ou via query param
        $paymentId = $request->data['id'] ?? $request->id;
        $topic = $request->type ?? $request->topic;

        if ($topic === 'payment' && $paymentId) {
            try {
                MercadoPagoConfig::setAccessToken($accessToken);
                $client = new \MercadoPago\Client\Payment\PaymentClient();
                $payment = $client->get($paymentId);

                $inscricaoId = $payment->external_reference;
                $status = $payment->status; // approved, pending, rejected, etc.

                $inscricao = Inscricao::find($inscricaoId);

                if ($inscricao) {
                    // Mapear status do MP para o nosso banco
                    $novoStatus = 'pendente';
                    if ($status === 'approved') {
                        $novoStatus = 'pago';
                    } elseif (in_array($status, ['rejected', 'cancelled', 'refunded'])) {
                        $novoStatus = 'cancelado';
                    }

                    $inscricao->update(['status' => $novoStatus]);

                    // Registrar o pagamento
                    Pagamento::updateOrCreate(
                        ['payment_id' => (string) $paymentId],
                        [
                            'inscricao_id' => $inscricao->id,
                            'status' => $status,
                            'metodo_pagamento' => $payment->payment_method_id,
                            'valor' => $payment->transaction_amount,
                            'dados_mercado_pago' => (array) $payment,
                            'data_pagamento' => now(),
                        ]
                    );

                    // Atualizar vagas se aprovado
                    if ($novoStatus === 'pago') {
                        $inscricao->curso->atualizarVagas();
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao processar webhook MP: ' . $e->getMessage());
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
