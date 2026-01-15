<?php

use Illuminate\Support\Facades\Route;

// Rota catch-all para SPA (Single Page Application)
// Todas as rotas devem retornar a view do Vue.js
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*')->name('spa');
