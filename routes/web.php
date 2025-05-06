<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LaudoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RelatorioLaudoController;

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

/** --------------------------------------------- */
/**         Rotas Classe Auth (para admins)       */
Route::middleware(['checkUserType:admin'])->controller(AuthController::class)->group(function (){
    Route::get('/user','readUsers')->name('readUsers');

    Route::get('/user/register','register')->name('cadastro.user');
    Route::post('/user/register','createUser')->name('create.user');

    Route::get('/user/alteracao/{id}','alteracaoUser')->name('alteracao.user'); # retorna a view de formulario de cadastro do tecnico
    Route::post('/user/alteracao/{id}','updateUser')->name('update.user'); # faz o update do tecnico no banco

    Route::get('/user/excluir/{id}','deleteUser')->name('delete.user'); # faz a exclusão do tecnico no banco
});

/** --------------------------------------------- */
/**              Rotas Classe Status              */
Route::middleware(['checkUserType:seguranca,admin'])->controller(StatusController::class)->group(function () { 
    Route::get('/status','readStatus')->name('readStatus'); # retorna a view contendo os status cadastrados

    Route::get('/status/cadastro','cadastroStatus')->name('cadastro.status'); # retorna o formulario de cadastro de status
    Route::post('/status/cadastro','createStatus')->name('create.status'); # salva o status no banco

    Route::get('/status/alteracao/{id}','alteracaoStatus')->name('alteracao.status'); # retorna o formulario de edição do status
    Route::post('/status/alteracao/{id}','updateStatus')->name('update.status'); # faz o update do status no banco

    Route::get('/status/excluir/{id}', 'deleteStatus')->name('delete.status'); # faz a exclusão do status no banco
});

/** --------------------------------------------- */
/**              Rotas Classe Cliente             */
Route::middleware(['checkUserType:comercial,admin'])->controller(ClienteController::class)->group(function () {
    Route::get('/cliente','readCliente')->name('readCliente'); # retorna a view contendo os clientes cadastrados

    Route::get('/cliente/cadastro','cadastroCliente')->name('cadastro.cliente'); # retorna o formulario de cadastro de cliente
    Route::post('/cliente/cadastro','createCliente')->name('create.cliente'); # salva o cliente no bd

    Route::get('/cliente/alteracao/{id}','alteracaoCliente')->name('alteracao.cliente'); # retorna o formulario de edição do cliente
    Route::post('/cliente/alteracao/{id}','updateCliente')->name('update.cliente'); # faz o update do cliente no banco

    Route::get('/cliente/excluir/{id}','deleteCliente')->name('delete.cliente'); # faz a exclusão do cliente no banco
});

/** --------------------------------------------- */
/**              Rotas Classe Laudo               */
Route::middleware(['checkUserType:comercial,admin'])->controller(LaudoController::class)->group(function () {
    Route::get('/laudo','readLaudo')->name('readLaudo'); # retorna a view contendo os laudos

    Route::get('/laudo/cadastro','cadastroLaudo')->name('cadastro.laudo'); # retorna o formulario de cadastro do laudo
    Route::post('/laudo/cadastro', 'createLaudo')->name('create.laudo'); # salva o laudo no bd

    Route::get('/laudo/alteracao/{id}','alteracaoLaudo')->name('alteracao.laudo'); # retorna o formulario de edição do laudo cadastrado
    Route::post('/laudo/alteracao/{id}','updateLaudo')->name('update.laudo'); # faz o update no banco

    Route::get('/laudo/excluir/{id}','deleteLaudo')->name('delete.laudo');
});

Route::middleware(['checkUserType:admin'])->controller(LaudoController::class)->group(function () { # separado da group function padrão, por ser algo apenas de user admin
    Route::get('/laudo/excluidos-anteriormente','laudosExcluidos')->name('read.deletedLaudo'); # Abre a view dos excluídos anteriormente
});

/** --------------------------------------------- */
/**    Rotas Classe Laudo para main dashboard     */
Route::middleware(['checkUserType:seguranca,comercial,admin'])->controller(LaudoController::class)->group(function (){
    Route::get('/dashboard','showDashboard')->name('dashboard.show');
    Route::get('/','showDashboard')->name('dashboard.show');

    Route::get('/dashboard/filtered', 'filterDashboard')->name('dashboard.filter');

    Route::post('/dashboard','updateLaudoIndex')->name('update.laudoIndex');
});

/** --------------------------------------------- */
/**              Rotas Classe Relatorio           */
Route::middleware(['checkUserType:admin'])->controller(RelatorioLaudoController::class)->group(function (){
    Route::get('/relatorios', 'tipoRelatorio')->name('tipo.relatorio');
    Route::post('/relatorios', 'requestTipoRelatorio')->name('request.tipoRelatorio');

    Route::post('/relatorios/download', 'gerarRelatorio')->name('gerar.relatorio');

});

/** --------------------------------------------- */
/**              Rotas Classe Auth                */
Route::controller(AuthController::class)->group(function (){
    Route::get('/login','login')->name('login.show');

    Route::get('/recuperar-senha','emailUserForgotPass')->name('email.solicitaPass');
    Route::post('/recuperar-senha', 'tokenUserForgotPass')->name('token.pass');

    Route::post('/recuperar-senha/token', 'validateTokenPass')->name('token.validate');
    Route::post('/recuperar-senha/alterar-senha', 'alterPassUser')->name('alter.password');

    Route::post('/login/auth','tryLogin')->name('login.try');

    Route::get('/logout', 'logout')->name('logout');
});

