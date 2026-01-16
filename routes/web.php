<?php

use App\Http\Controllers\InscricaoController;
use Illuminate\Support\Facades\Route;


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
