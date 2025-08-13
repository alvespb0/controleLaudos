<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Recomendadores;
use App\Http\Requests\RecomendadorRequest;

class RecomendadoresController extends Controller
{
    /**
     * Retorna a pagina de cadastro do recomendador
     * @return View
     */
    public function cadastroRecomendador(){
        return view("Recomendadores/Recomendador_new");
    }

    /**
     * Recebe uma request via POST valida os dados, se validado cadastra no banco
     * Se não retorna o erro
     * @param Request $request
     * @return Redirect
     */
    public function createRecomendador(RecomendadorRequest $request){
        $request->validated();

        Recomendadores::create([
            'nome' => $request->nome,
            'cpf' => $request->cpf
        ]);

        session()->flash('mensagem', 'Recomendador registrado com sucesso');

        return redirect()->route('read.recomendador');
    }

    /**
     * retorna os recomendadores cadastrados no banco
     * @return Array
     */
    public function readRecomendador(){
        $recomendador = Recomendadores::all();
        return view('Recomendadores/Recomendador_show', ['recomendadores'=> $recomendador]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do Recomendador 
     * @param int $id
     * @return array
     */
    public function alteracaoRecomendador($id){
        $recomendador = Recomendadores::findOrFail($id);
        return view('Recomendadores/Recomendador_edit', ['recomendador' => $recomendador]);
    }

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param Request
     * @param int $id
     * @return Redirect
     */
    public function updateRecomendador(RecomendadorRequest $request, $id){
        $request->validated();

        $recomendador = Recomendadores::findOrFail($id);

        $recomendador->update([
            'nome' => $request->nome,
            'cpf' => $request->cpf
        ]);

        session()->flash('mensagem', 'Recomendador alterado com sucesso');

        return redirect()->route('read.recomendador');
    }

    /**
     * recebe o id e deleta o recomendador vinculado nesse ID
     * @param int $id
     * @return view
     */
    public function deleteRecomendador($id){
        $recomendador = Recomendadores::findOrFail($id);

        $recomendador->delete();

        session()->flash('mensagem', 'Recomendador Excluido com sucesso');

        return redirect()->route('read.recomendador');
    }

}
