<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LaudoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RelatorioLaudoController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\IndicadoresController;
use App\Http\Controllers\ZappyController;
use App\Http\Controllers\Documentos_TecnicosController;
use App\Http\Controllers\CRMController;

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
    Route::get('/cliente/filtered', 'filterCliente')->name('filter.cliente');

    Route::get('/cliente/cadastro','cadastroCliente')->name('cadastro.cliente'); # retorna o formulario de cadastro de cliente
    Route::post('/cliente/cadastro','createCliente')->name('create.cliente'); # salva o cliente no bd

    Route::get('/cliente/alteracao/{id}','alteracaoCliente')->name('alteracao.cliente'); # retorna o formulario de edição do cliente
    Route::post('/cliente/alteracao/{id}','updateCliente')->name('update.cliente'); # faz o update do cliente no banco

    Route::get('/cliente/excluir/{id}','deleteCliente')->name('delete.cliente'); # faz a exclusão do cliente no banco
});

/** --------------------------------------------- */
/**              Rotas Classe File                */
Route::middleware(['checkUserType:comercial,admin'])->controller(FileController::class)->group(function () {
    Route::get('/orcamento', 'entradaOrcamento')->name('entrada.orcamento'); # Retorna a view de orcamento_new0
    Route::get('/orcamento/formulario', 'formularioOrcamento')->name('gerar.orcamento'); # Retorna a view de orcament_new dado os parâmetros da new0

    Route::post('/orcamento/gerar', 'gerarOrcamento')->name('baixar.orcamento'); # Faz o download do orçamento

    Route::post('/orcamento/aprovar/{fileName}/{lead_id?}', 'saveOrcamento')->name('orcamento.aprovar');
    Route::post('/orcamento/retificar', 'retificarOrcamento')->name('orcamento.retificar');
    Route::get('/orcamento/download/{fileName}', 'downloadOrcamento')->name('orcamento.download');
});

/** --------------------------------------------- */
/**              Rotas Classe Laudo               */
Route::middleware(['checkUserType:comercial,admin'])->controller(LaudoController::class)->group(function () {
    Route::get('/laudo','readLaudo')->name('readLaudo'); # retorna a view contendo os laudos
    Route::get('/laudo/filtered', 'filterCliente')->name('filter.laudo-cliente'); # Filtro de cliente para a showLaudo

    Route::get('/laudo/cadastro','cadastroLaudo')->name('cadastro.laudo'); # retorna o formulario de cadastro do laudo
    Route::post('/laudo/cadastro', 'createLaudo')->name('create.laudo'); # salva o laudo no bd

    Route::get('/laudo/alteracao/{id}','alteracaoLaudo')->name('alteracao.laudo'); # retorna o formulario de edição do laudo cadastrado
    Route::post('/laudo/alteracao/{id}','updateLaudo')->name('update.laudo'); # faz o update no banco

    Route::get('/laudo/excluir/{id}','deleteLaudo')->name('delete.laudo');
});

Route::middleware(['checkUserType:admin'])->controller(LaudoController::class)->group(function () { # separado da group function padrão, por ser algo apenas de user admin
    Route::get('/laudo/excluidos-anteriormente','laudosExcluidos')->name('read.deletedLaudo'); # Abre a view dos excluídos anteriormente

    Route::get('/laudo/excluidos-anteriormente/restaurar/{id}', 'restoreLaudo')->name('restore.deletedLaudo'); # restaura o dado excluído
    Route::post('/dashboard/kanban/update-all-positions', 'updateAllPositions')->name('update.all.positions');
    Route::post('/dashboard/kanban/update-column-position', 'updateColumnPosition')->name('update.column.position');
});

/** --------------------------------------------- */
/**              Rotas Classe documentos          */
Route::middleware(['checkUserType:admin,seguranca'])->controller(Documentos_TecnicosController::class)->group(function (){
    Route::get('/documentos', 'readDocsTecnicos')->name('readDocs'); # retorna a listagem do documentos técnicos

    Route::get('/documentos/cadastro', 'cadastroDocTecnico')->name('cadastro.documento'); # retorna a view da tela de cadastro de documento tecnico
    Route::post('/documentos/cadastro', 'createDocTecnico')->name('create.documento'); # salva o documento tecnico no banco

    Route::get('/documentos/alteracao/{id}', 'alteracaoDocTecnico')->name('alteracao.documento'); # retrona a view de edição do documento técnico
    Route::post('/documentos/alteracao/{id}', 'updateDocTecnico')->name('update.documento'); # da update no documento no banco

    Route::get('documentos/excluir/{id}', 'deleteDocTecnico')->name('delete.documento'); # deleta o documento no banco

    Route::get('documentos/controle', 'indexDocTecnico')->name('show.docIndex');
    Route::post('documentos/controle', 'updateDocIndex')->name('update.docIndex');
    Route::get('documentos/controle/filtered', 'filterDocIndex')->name('filter.docIndex');
});

Route::middleware(['checkUserType:admin'])->controller(Documentos_TecnicosController::class)->group(function () { # separado da group function padrão, por ser algo apenas de user admin
    Route::get('/documentos/excluidos-anteriormente','docsExcluidos')->name('read.deletedDoc'); # Abre a view dos excluídos anteriormente

    Route::get('/documentos/excluidos-anteriormente/restaurar/{id}', 'restoreDoc')->name('restore.deletedDoc'); # restaura o dado excluído
});

/** --------------------------------------------- */
/**    Rotas Classe Laudo para main dashboard     */
Route::middleware(['checkUserType:seguranca,comercial,admin'])->controller(LaudoController::class)->group(function (){
    Route::get('/dashboard','showDashboard')->name('dashboard.show');
    Route::get('/','showDashboard')->name('dashboard.show');

    Route::get('/dashboard/kanban', 'showKanban')->name('kanban.show');

    Route::get('/dashboard/filtered', 'filterDashboard')->name('dashboard.filter');

    Route::post('/dashboard','updateLaudoIndex')->name('update.laudoIndex');
    Route::post('/dashboard/envia-email', 'enviaEmailCli')->name('envia-email.cliente');

    Route::post('/dashboard/kanban', 'updateLaudoKanban')->name('update.laudoKanban');
});

/** --------------------------------------------- */
/**             Rotas Classe indicadores          */
Route::middleware(['checkUserType:admin'])->controller(IndicadoresController::class)->group(function (){
    Route::get('/graphs', 'dashboardGerencial')->name('dashboard.indicadores'); # tela somente para admins, por isso não vai fazer parte da group class anterior
});

/** --------------------------------------------- */
/**              Rotas Classe Relatorio           */
Route::middleware(['checkUserType:admin,comercial,seguranca'])->controller(RelatorioLaudoController::class)->group(function (){
    Route::get('/relatorios', 'tipoRelatorio')->name('tipo.relatorio');
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

/** --------------------------------------------- */
/**              Rotas Classe integracao          */
Route::middleware(['checkUserType:admin,comercial,seguranca'])->post('dashboard/atendimento', [ZappyController::class, 'createAtendimento'])->name('atendimento.zappy'); # rota para criação de atendimentos no zappy
Route::middleware(['checkUserType:admin,comercial,seguranca'])->post('orcamento/enviar', [ZappyController::class, 'encaminhaOrcamentoCliente'])->name('orcamento.zappy'); # rota para criação de atendimentos no zappy
Route::middleware(['checkUserType:admin,comercial,seguranca'])->post('CRM/atendimento', [ZappyController::class, 'encaminhaWhatsLead'])->name('orcamento.zappy'); # rota para criação de atendimentos no zappy

/** --------------------------------------------- */
/**                  Rotas Classe CRM             */
Route::middleware(['checkUserType:admin'])->controller(CRMController::class)->group(function (){
    Route::get('/CRM', 'showCRM')->name('show.CRM'); # Retorna a view do CRM
    Route::post('/CRM/cadastrar-lead', 'createLead')->name('create.lead'); # Cria um LEAD baseado a etapa id
    Route::get('/CRM/mudar-etapa/{lead_id}/{etapa_id}', 'alterStatusLead')->name('alterStatus.lead'); # altera a etapa do lead
    Route::get('/CRM/gerar-orcamento/{lead_id}', 'formularioOrcamento')->name('gerar.orcamentoLead');
});
