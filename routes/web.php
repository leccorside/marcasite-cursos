<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação (usam sessões via middleware web)
Route::prefix('api')->group(function () {
    // Rota para obter CSRF token
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    });

    // Rotas públicas de autenticação
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    // Rotas protegidas
    Route::middleware(['auth'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Rotas de Cursos (apenas admin)
        Route::middleware(['admin'])->prefix('cursos')->group(function () {
            Route::get('/', [\App\Http\Controllers\CursoController::class, 'index']);
            Route::get('/{curso}', [\App\Http\Controllers\CursoController::class, 'show']);
            Route::get('/{curso}/inscritos', [\App\Http\Controllers\CursoController::class, 'inscritos']);
            Route::post('/', [\App\Http\Controllers\CursoController::class, 'store']);
            Route::put('/{curso}', [\App\Http\Controllers\CursoController::class, 'update']);
            Route::delete('/{curso}', [\App\Http\Controllers\CursoController::class, 'destroy']);
        });
    });

    // Rota para verificar autenticação (sem middleware, retorna null se não autenticado)
    Route::get('/check-auth', [AuthController::class, 'user']);
});

// Rota para reset de senha (usada no email de recuperação)
// Esta rota é necessária para o Laravel gerar o link no email
Route::get('/reset-password/{token}', function ($token) {
    return redirect('/reset-password?token=' . $token);
})->name('password.reset');

// Rota catch-all para SPA (Single Page Application)
// Todas as rotas devem retornar a view do Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*')->name('spa');
