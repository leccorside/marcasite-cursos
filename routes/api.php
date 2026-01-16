<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\InscricaoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Rotas públicas de autenticação
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Rotas públicas de cursos (Vitrine)
Route::get('/public/cursos', [CursoController::class, 'publicIndex']);
Route::get('/public/categorias', [CursoController::class, 'categorias']);

// Webhook Mercado Pago (sem autenticação)
Route::post('/webhook/mercadopago', [InscricaoController::class, 'webhook'])->name('webhook.mercadopago');

// Rotas protegidas com JWT
Route::middleware('auth:api')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/check-auth', [AuthController::class, 'user']);
    
    // Perfil (qualquer usuário autenticado)
    Route::put('/perfil', [PerfilController::class, 'update']);

    // Rotas de Aluno
    Route::middleware('aluno')->group(function () {
        Route::get('/meus-cursos', [InscricaoController::class, 'index']);
        Route::get('/stats/aluno', [InscricaoController::class, 'stats']);
        Route::post('/inscricoes', [InscricaoController::class, 'store']);
        Route::put('/inscricoes/{inscricao}/status', [InscricaoController::class, 'updateStatus']);
    });

    // Rotas de Admin
    Route::middleware('admin')->group(function () {
        // Dashboard Admin
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

        // Cursos (apenas admin)
        Route::get('/cursos', [CursoController::class, 'index']);
        Route::post('/cursos', [CursoController::class, 'store']);
        Route::get('/cursos/{curso}', [CursoController::class, 'show']);
        Route::put('/cursos/{curso}', [CursoController::class, 'update']);
        Route::delete('/cursos/{curso}', [CursoController::class, 'destroy']);
        Route::get('/cursos/{curso}/inscritos', [CursoController::class, 'inscritos']);
        Route::get('/cursos/export/excel', [CursoController::class, 'exportExcel']);
        Route::get('/cursos/export/pdf', [CursoController::class, 'exportPdf']);

        // Usuários (apenas admin)
        Route::get('/usuarios', [UserController::class, 'index']);
        Route::post('/usuarios', [UserController::class, 'store']);
        Route::put('/usuarios/{usuario}', [UserController::class, 'update']);
        Route::delete('/usuarios/{usuario}', [UserController::class, 'destroy']);
        Route::get('/usuarios/export/excel', [UserController::class, 'exportExcel']);
        Route::get('/usuarios/export/pdf', [UserController::class, 'exportPdf']);
    });
});
