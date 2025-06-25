<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DocumentoRequest; 

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
        $clientes = Cliente::all();
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
            'cliente_id' => $request->cliente->id
        ]);

        session()->flash('mensagem', $request->tipo_documento.'registrado com sucesso');

        return redirect()->route('readDocs');
    }

    /**
     * Retorna todos os documentos em forma de listagem
     * @return Array
     */
    public function readDocsTecnicos(){
        $docs = Documentos_Tecnicos::orderBy('descricao', 'asc')->paginate(10);
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

        $documento = Documento_Tecnicos::findOrFail($id);

        $documento->update([
            'tipo_documento' => $request->tipo_documento,
            'descricao' => $request->descricao,
            'data_elaboracao' => $request->data_elaboracao,
            'cliente_id' => $request->cliente->id
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
        $documento = Documento_Tecnicos::findOrFail($id);

        $documento->delete();

        session()->flash('mensagem', $request->tipo_documento.' excluido com sucesso');

        return redirect()->route('readLaudo');
    }
}
