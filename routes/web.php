<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Op_TecnicoController;
use App\Http\Controllers\Op_ComercialController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LaudoController;

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
    return view('index');
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

/** --------------------------------------------- */
/**              Rotas Classe Cliente             */
Route::controller(ClienteController::class)->group(function () {
    Route::get('/cliente','readCliente')->name('readCliente'); # retorna a view contendo os clientes cadastrados

    Route::get('/cliente/cadastro','cadastroCliente')->name('cadastro.cliente'); # retorna o formulario de cadastro de cliente
    Route::post('/cliente/cadastro','createCliente')->name('create.cliente'); # salva o cliente no bd

    Route::get('/cliente/alteracao/{id}','alteracaoCliente')->name('alteracao.cliente'); # retorna o formulario de edição do cliente
    Route::post('/cliente/alteracao/{id}','updateCliente')->name('update.cliente'); # faz o update do cliente no banco

    Route::get('/cliente/excluir/{id}','deleteCliente')->name('delete.cliente'); # faz a exclusão do cliente no banco
});

/** --------------------------------------------- */
/**              Rotas Classe Laudo               */
Route::controller(LaudoController::class)->group(function () {
    Route::get('/laudo','readLaudo')->name('readLaudo'); # retorna a view contendo os laudos

    Route::get('/laudo/cadastro','cadastroLaudo')->name('cadastro.laudo'); # retorna o formulario de cadastro do laudo
    Route::post('/laudo/cadastro', 'createLaudo')->name('create.laudo'); # salva o laudo no bd

    Route::get('/laudo/alteracao/{id}','alteracaoLaudo')->name('alteracao.laudo'); # retorna o formulario de edição do laudo cadastrado
    Route::post('/laudo/alteracao/{id}','updateLaudo')->name('update.laudo'); # faz o update no banco

    Route::get('/laudo/excluir/{id}','deleteLaudo')->name('delete.laudo');
});