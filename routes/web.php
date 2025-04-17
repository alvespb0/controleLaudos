<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Op_TecnicoController;

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
    return view('welcome');
});

/** --------------------------------------------- */
/**              Rotas Classe Segurança           */
Route::controller(Op_TecnicoController::class)->group(function () {
    Route::get('/tecnico','readTecnico')->name('readTecnico'); # retorna a view contendo todos os técnicos cadastrados

    Route::get('/tecnico/cadastro','cadastroTecnico')->name('cadastro.tecnico'); # retorna a view de formulario de cadastro do tecnico
    Route::post('/tecnico/cadastro', 'createTecnico')->name('create.tecnico'); # faz o cadastro do tecnico no bd

    Route::get('/tecnico/alteracao/{id}','alteracaoTecnico')->name('alteracao.tecnico'); # retorna a view de formulario de cadastro do tecnico
    Route::post('/tecnico/alteracao/{id}','updateTecnico')->name('update.tecnico');




});