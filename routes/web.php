<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PerfilController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação (usam sessões via middleware web)
Route::prefix('api')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    });

    // Rotas protegidas
    Route::middleware(['auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/perfil', [PerfilController::class, 'update']);

        // Inscrição (Aluno)
        Route::get('/meus-cursos', [InscricaoController::class, 'index']);
        Route::get('/stats/aluno', [InscricaoController::class, 'stats']);
        Route::post('/inscricoes', [InscricaoController::class, 'store']);
        Route::put('/inscricoes/{inscricao}/status', [InscricaoController::class, 'updateStatus']);

        // Rotas de Cursos (apenas admin)
        Route::get('/cursos', [CursoController::class, 'index']);
        Route::post('/cursos', [CursoController::class, 'store']);
        Route::get('/cursos/{curso}', [CursoController::class, 'show']);
        Route::put('/cursos/{curso}', [CursoController::class, 'update']);
        Route::delete('/cursos/{curso}', [CursoController::class, 'destroy']);
        Route::get('/cursos/{curso}/inscritos', [CursoController::class, 'inscritos']);

        // Usuários (apenas admin)
        Route::get('/usuarios', [UserController::class, 'index']);
    });

    // Rota para verificar autenticação (sem middleware, retorna null se não autenticado)
    Route::get('/check-auth', [AuthController::class, 'user']);

    // Rotas Públicas de Cursos (Vitrine)
    Route::get('/public/cursos', [CursoController::class, 'publicIndex']);
    Route::get('/public/categorias', [CursoController::class, 'categorias']);

    // Webhook Mercado Pago
    Route::post('/webhook/mercadopago', [InscricaoController::class, 'webhook'])->name('webhook.mercadopago');
});

// Rota de retorno do pagamento
Route::get('/pagamento/retorno', [InscricaoController::class, 'retorno'])->name('pagamento.retorno');

// Rota para reset de senha (usada no email de recuperação)
Route::get('/reset-password/{token}', function ($token) {
    return redirect('/reset-password?token=' . $token);
})->name('password.reset');

// Rota catch-all para SPA (Single Page Application)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*')->name('spa');
