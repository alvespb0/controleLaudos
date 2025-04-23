<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LaudoRequest; 
use App\Http\Requests\LaudoUpdateRequest; 

use App\Models\Laudo;
use App\Models\Cliente;
use App\Models\Op_Comercial;
use App\Models\Op_Tecnico;
use App\Models\Status;


class LaudoController extends Controller
{
    /**
     * Retorna a pagina de cadastro do Laudo junto dos clientes cadastrados e dos operadores comerciais cadastrados
     * @return View
     */
   public function cadastroLaudo(){
        $clientes = Cliente::all();
        $comercial = Op_Comercial::all();
        return view("Laudo/Laudo_new", ['clientes'=> $clientes, 'comerciais'=> $comercial]);
    }

    /**
     * Recebe uma Laudo Request, valida os dados, se validado salva no banco
     * Salvo apenas os dados que vieram no formulario, o restante NULL
     * @param LaudoRequest
     * @return Redirect
     */
    public function createLaudo(LaudoRequest $request){
        $request->validated();

        Laudo::create([
            'nome' => $request->nome,
            'data_previsao' => $request->dataPrevisao,
            'data_conclusao' => null,
            'data_fim_contrato' => $request->dataFimContrato,
            'numero_clientes' => $request->numFuncionarios,
            'tecnico_id' => null,
            'status_id' => null,
            'cliente_id' => $request->cliente,
            'comercial_id' => $request->comercial
        ]);

        session()->flash('mensagem', 'Laudo registrado com sucesso');

        return redirect()->route('readLaudo');
    }

    /**
    * retorna os laudos salvos no banco
    * @return Array
    */
    public function readLaudo(){
        $laudos = Laudo::all();
        return view('Laudo/Laudo_show', ['laudos'=> $laudos]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do Laudo 
     * @param int $id
     * @return array
     */
    public function alteracaoLaudo($id){
        $laudo = Laudo::findOrFail($id);
        $clientes = Cliente::all();
        $comercial = Op_Comercial::all();
        return view('Laudo/Laudo_edit', ['laudo' => $laudo, 'clientes'=> $clientes, 'comerciais'=> $comercial]);
    }

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param LaudoRequest
     * @param int $id
     * @return Redirect
     */
    public function updateLaudo(LaudoRequest $request, $id){
        $request->validated();

        $laudo = Laudo::findOrFail($id);

        $laudo->update([
            'nome' => $request->nome,
            'data_previsao' => $request->dataPrevisao,
            'data_fim_contrato' => $request->dataFimContrato,
            'numero_clientes' => $request->numFuncionarios,
            'cliente_id' => $request->cliente,
            'comercial_id' => $request->comercial
        ]);

        session()->flash('mensagem', 'Laudo Alterado com sucesso');

        return redirect()->route('readLaudo');
    }

    /**
    * recebe o id e deleta o Laudo vinculado nesse ID
    * @param int $id
    * @return view
    */
   public function deleteLaudo($id){
    $laudo = Laudo::findOrFail($id);

    $laudo->delete();

    return redirect()->route('readLaudo');
    }

    /**
     * Recebe uma solicitação GET com uma request de filtro
     * @param Request
     * @return View
     */
    public function filterDashboard(Request $request){
        $laudos = Laudo::query();

        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 

        if($request->filled('search')){
            $clientes = Cliente::where('nome', 'like', "%{$request->input('search')}%")->pluck('id');
            if($clientes->isNotEmpty()){
                $laudos = $laudos->whereIn('cliente_id', $clientes);
            }else{
                session()->flash('mensagem', 'Nenhum cliente localizado');
       
                return view("index", [
                    "laudos" => collect(), // array vazio em vez de query vazia
                    "status" => $status,
                    "tecnicos" => $tecnicos
                ]);
            }
        }

        if($request->filled('status')){
            $laudos = $laudos->where('status_id', $request->status); # esse where deve funcionar, devido a value do select ser o id do status
        }

        if($request->filled('dataConclusao')){
            $laudos = $laudos->where('data_conclusao', $request->dataConclusao);
        }

        return view("index", ["laudos"=> $laudos->get(), "status" => $status, "tecnicos"=> $tecnicos]);
    }

    /**
     * retorna a pagina index levando todos os laudos, status e tecnicos de segurança
     * @return View
     */
    public function showDashboard(){
        $laudos = Laudo::all();
        $status = Status::all();
        $tecnicos = Op_Tecnico::all();
        return view("index", ["laudos"=> $laudos, "status" => $status, "tecnicos"=> $tecnicos]);
    }

    /**
     * Recebe uma request válida os dados através do LaudoUpdateRequest e retorna um json
     * @param LaudoUpdateRequest
     * @return Json
     */
    public function updateLaudoIndex(LaudoUpdateRequest $request){
        $request->validated();

        $laudo = Laudo::findOrFail($request->laudo_id);

        $laudo->update([
            'data_conclusao' => $request->dataConclusao,
            'status_id' => $request->status,
            'tecnico_id' => $request->tecnicoResponsavel
        ]);

        return response()->json(['message' => 'Laudo Atualizado com sucesso']);
    }

}
