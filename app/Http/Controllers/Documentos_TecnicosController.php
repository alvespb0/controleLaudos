<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DocumentoRequest; 
use App\Http\Requests\DocumentoUpdateRequest; 

use App\Models\Cliente;
use App\Models\Op_Tecnico;
use App\Models\Status;
use App\Models\Documentos_Tecnicos;

class Documentos_TecnicosController extends Controller
{
    /**
     * Retorna a view da página de cadastro de documentos complementares
     * @return view
     */
    public function cadastroDocTecnico(){
        $clientes = Cliente::orderBy('nome','asc')->get();
        return view('Documentos/Documento_new', ['clientes' => $clientes]);
    }

    /**
     * Recebe um DocumentoRequest, valida os dados, se validado, salva no banco
     * Salva apenas os dados que vieram no formulário, o restante é NULL
     * @param DocumentoRequest $request
     * @return Redirect
     */
    public function createDocTecnico(DocumentoRequest $request){
        $request->validated();

        Documentos_Tecnicos::create([
            'tipo_documento' => $request->tipo_documento,
            'descricao' => $request->descricao,
            'data_elaboracao' => $request->data_elaboracao,
            'cliente_id' => $request->cliente_id
        ]);

        session()->flash('mensagem', $request->tipo_documento.'registrado com sucesso');

        return redirect()->route('readDocs');
    }

    /**
     * Retorna todos os documentos em forma de listagem
     * @return Array
     */
    public function readDocsTecnicos(){
        $docs = Documentos_Tecnicos::orderBy('data_elaboracao', 'desc')->paginate(10);
        return view('Documentos/Documento_show', ['documentos' => $docs]);
    }

    /**
     * Recebe um ID via get valida o ID via find or FAIL e retorna a página de edição
     * 
     */
    public function alteracaoDocTecnico($id){
        $documento = Documentos_Tecnicos::findOrFail($id);
        $clientes = Cliente::all();
        return view ('Documentos/Documento_edit', ['documento' => $documento, 'clientes' => $clientes]);
    }

    /**
     * Recebe uma request faz a validação dos dados e da update dado o id
     * @param DocumentoRequest $request
     * @param int $id
     * @return Redirect
     */
    public function updateDocTecnico(DocumentoRequest $request, $id){
        $request->validated();

        $documento = Documentos_Tecnicos::findOrFail($id);

        $documento->update([
            'tipo_documento' => $request->tipo_documento,
            'descricao' => $request->descricao,
            'data_elaboracao' => $request->data_elaboracao,
            'cliente_id' => $request->cliente_id
        ]);

        session()->flash('mensagem', $request->tipo_documento.' alterado com sucesso');

        return redirect()->route('readDocs');
    }

    /**
     * recebe o id e deleta o doc vinculado nesse ID
     * @param int $id
     * @return view 
     */
    public function deleteDocTecnico($id){
        $documento = Documentos_Tecnicos::findOrFail($id);

        $documento->delete();

        session()->flash('mensagem','Documento excluido com sucesso');

        return redirect()->route('readDocs');
    }

    /**
     * Retorna a view da index de controle de documento técnico
     */
    public function indexDocTecnico(){
        $documentos = Documentos_Tecnicos::orderBy('data_elaboracao', 'desc')->paginate(6);
        $status = Status::all();
        $tecnicos = Op_Tecnico::all();

        $contagemPorStatus = [];
        foreach ($status as $s) {
            $contagemPorStatus[$s->id] = Documentos_Tecnicos::where('status_id', $s->id)->count();
        }
        $semStatusCount = Documentos_Tecnicos::whereNull('status_id')->count();
        $status->push((object)[
            'id' => 'sem_status',
            'nome' => 'Sem status',
            'cor' => '#6c757d'
        ]);
        $contagemPorStatus['sem_status'] = $semStatusCount;

        return view("/Documentos/Documento_index", ["documentos"=> $documentos, "status" => $status, "tecnicos"=> $tecnicos, "contagemPorStatus" => $contagemPorStatus]);
    }

    /**
     * Recebe uma request vinda da index através de fetch, valida, e dá update no banco
     * @param DocumentoUpdateRequest $request
     * @return json
     */
    public function updateDocIndex(DocumentoUpdateRequest $request){
        $request->validated();

        $documento = Documentos_Tecnicos::findOrFail($request->documento_id);

        $documento->update([
            'status_id' => $request->status,
            'data_conclusao' => $request->dataConclusao,
            'tecnico_id' => $request->tecnicoResponsavel
        ]);

        return response()->json(['message' => $documento->tipo_documento.' Atualizado com sucesso']);
    }

    /**
     * Recebe os filtros vindo da index, aplica os filtros ao query builder e retorna os documentos dado os filtros
     * @param Request $request
     * @return view
     */
    public function filterDocIndex(Request $request){
        $documentos = Documentos_Tecnicos::query();
        $status = Status::all();
        $tecnicos = Op_Tecnico::all(); 

        /* Filtro de Search Cliente */
        if($request->filled('search')){
            $clientes = Cliente::where('nome', 'like', "%{$request->input('search')}%")->pluck('id');
            if($clientes->isNotEmpty()){
                $documentos = $documentos->whereIn('cliente_id', $clientes);
            }else{
                session()->flash('Error', 'Nenhum cliente localizado');
                return view("index", [
                    "documentos_tecnicos" => collect(),
                    "status" => $status,
                    "tecnicos" => $tecnicos
                ]);
            }
        }
        /* Filtro de Search Mes de competencia (pela data de elaboração) */
        if($request->filled('mesCompetencia')){
            [$ano, $mes] = explode('-', $request->mesCompetencia);

            $documentos = $documentos->whereYear('data_elaboracao', $ano)
                             ->whereMonth('data_elaboracao', $mes);
        }

        /* Filtro de Search pelo status do documentos */
        if($request->filled('status')){
            if($request->status == "sem_status"){
                $documentos = $documentos->where('status_id', null);
            }else{
                $documentos = $documentos->where('status_id', $request->status);
            }
        }

        /* Filtro de Search pela data de conclusão (específica) */
        if($request->filled('dataConclusao')){
            $documentos = $documentos->where('data_conclusao', $request->dataConclusao);
        }

        // Calcula indicadores com base na query filtrada
        $contagemPorStatus = [];

        foreach ($status as $s) {
            $contagemPorStatus[$s->id] = (clone $documentos)->where('status_id', $s->id)->count();
        }

        $semStatusCount = (clone $documentos)->whereNull('status_id')->count();

        $status->push((object)[
            'id' => 'sem_status',
            'nome' => 'Sem status',
            'cor' => '#6c757d'
        ]);

        $contagemPorStatus['sem_status'] = $semStatusCount;

        /* Filtro pela ordenação */
        $ordem = $request->input('ordenarPor', 'mais_novos'); 

        if ($ordem === 'mais_antigos') {
            $documentos = $documentos->orderBy('created_at', 'asc');
        } else {
            $documentos = $documentos->orderBy('created_at', 'desc');
        }

        $documentos = $documentos->paginate(6)->appends($request->query());


        return view("/Documentos/Documento_index",[
            "documentos"=> $documentos, 
            "status" => $status, 
            "tecnicos"=> $tecnicos,
            "contagemPorStatus" => $contagemPorStatus
        ]);
    }

}
