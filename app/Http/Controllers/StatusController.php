<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;
use App\Http\Requests\StatusRequest;

class StatusController extends Controller
{
    /**
     * Retorna a pagina de cadastro do status
     * @return View
     */
    public function cadastroStatus(){
        return view("Status/Status_new");
    }

    /**
     * Recebe uma request via POST valida os dados, se validado cadastra no banco
     * Se não retorna o erro
     * @param Request $request
     * @return Redirect
     */
    public function createStatus(StatusRequest  $request){
        $request->validated();

        Status::create([
            'nome' => $request->nome,
            'cor' => $request->cor
        ]);

        session()->flash('mensagem', 'Status registrado com sucesso');

        return redirect()->route('readStatus');
    }

    /**
     * retorna os status cadastrados no banco
     * @return Array
     */
    public function readStatus(){
        $Status = Status::all();
        return view('Status/Status_show', ['Status'=> $Status]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do Status 
     * @param int $id
     * @return array
     */
    public function alteracaoStatus($id){
        $Status = Status::findOrFail($id);
        return view('Status/Status_edit', ['Status' => $Status]);
    }

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param Request
     * @param int $id
     * @return Redirect
     */
    public function updateStatus(StatusRequest $request, $id){
        $request->validated();

        $Status = Status::findOrFail($id);

        $Status->update([
            'nome' => $request->nome,
            'cor' => $request->cor
        ]);

        session()->flash('mensagem', 'Status Alterado com sucesso');

        return redirect()->route('readStatus');
    }

    /**
     * recebe o id e deleta o Status vinculado nesse ID
     * @param int $id
     * @return view
     */
    public function deleteStatus($id){
        $Status = Status::findOrFail($id);

        $Status->delete();

        return redirect()->route('readStatus');
    }

}
