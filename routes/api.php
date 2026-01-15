<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas de API futuras (que usam Sanctum/tokens)
// As rotas de autenticação estão em web.php para usar sessões

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
