<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Op_TecnicoController;
use App\Http\Controllers\Op_ComercialController;
use App\Http\Controllers\StatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('templateMain');
});

/** --------------------------------------------- */
/**              Rotas Classe Segurança           */
Route::controller(Op_TecnicoController::class)->group(function () {
    Route::get('/tecnico','readTecnico')->name('readTecnico'); # retorna a view contendo todos os técnicos cadastrados

    Route::get('/tecnico/cadastro','cadastroTecnico')->name('cadastro.tecnico'); # retorna a view de formulario de cadastro do tecnico
    Route::post('/tecnico/cadastro', 'createTecnico')->name('create.tecnico'); # faz o cadastro do tecnico no bd

    Route::get('/tecnico/alteracao/{id}','alteracaoTecnico')->name('alteracao.tecnico'); # retorna a view de formulario de cadastro do tecnico
    Route::post('/tecnico/alteracao/{id}','updateTecnico')->name('update.tecnico'); # faz o update do tecnico no banco

    Route::get('/tecnico/excluir/{id}','deleteTecnico')->name('delete.tecnico'); # faz a exclusão do tecnico no banco
});

/** --------------------------------------------- */
/**              Rotas Classe Comercial           */
Route::controller(Op_ComercialController::class)->group(function () {
    Route::get('/comercial','readComercial')->name('readComercial'); # retorna a view contendo todos os opcomercial cadastrados

    Route::get('/comercial/cadastro','cadastroComercial')->name('cadastro.comercial'); # retorna a view de formulario de cadastro do opcomercial
    Route::post('/comercial/cadastro', 'createComercial')->name('create.comercial'); # faz o cadastro do opcomercial no bd

    Route::get('/comercial/alteracao/{id}','alteracaoComercial')->name('alteracao.comercial'); # retorna a view de formulario de cadastro do opcomercial
    Route::post('/comercial/alteracao/{id}','updateComercial')->name('update.comercial'); # faz o update do opcomercial no banco

    Route::get('/comercial/excluir/{id}','deleteComercial')->name('delete.comercial'); # faz a exclusão do opcomercial no banco
});

/** --------------------------------------------- */
/**              Rotas Classe Status              */
Route::controller(StatusController::class)->group(function () { 
    Route::get('/status','readStatus')->name('readStatus'); # retorna a view contendo os status cadastrados

    Route::get('/status/cadastro','cadastroStatus')->name('cadastro.status'); # retorna o formulario de cadastro de status
    Route::post('/status/cadastro','createStatus')->name('create.status'); # salva o status no banco

    Route::get('/status/alteracao/{id}','alteracaoStatus')->name('alteracao.status'); # retorna o formulario de edição do status
    Route::post('/status/alteracao/{id}','updateStatus')->name('update.status'); # faz o update do status no banco

    Route::get('/status/excluir/{id}', 'deleteStatus')->name('delete.status'); # faz a exclusão do status no banco
});