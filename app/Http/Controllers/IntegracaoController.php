<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\IntegracaoRequest;
use App\Models\Integracao;

class IntegracaoController extends Controller
{
    /**
     * Retorna a pagina de cadastro da integração
     * @return View
     */
    public function cadastroIntegracao(){
        return view("Integracao/Integracao_new");
    }

    /**
     * Recebe uma request via POST valida os dados, se validado cadastra no banco
     * Se não retorna o erro
     * @param Request $request
     * @return Redirect
     */
    public function createIntegracao(IntegracaoRequest $request){
        $request->validated();

        Integracao::create([
            'sistema' => $request->sistema,
            'descricao' => $request->descricao,
            'slug' => $request->slug,
            'endpoint' => $request->endpoint,
            'auth' => $request->auth, 
            'tipo' => $request->tipo, 
        ]);

        session()->flash('mensagem', 'Integração registrado com sucesso');

        return redirect()->route('read.integracao');
    }

    /**
     * retorna as integrações cadastrados no banco
     * @return Array
     */
    public function readIntegracao(){
        $integracoes = Integracao::all();
        return view('Integracao/Integracao_show', ['integracoes'=> $integracoes]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição da integracao 
     * @param int $id
     * @return array
     */
    public function alteracaoIntegracao($id){
        $integracao = Integracao::findOrFail($id);
        return view('Integracao/Integracao_edit', ['integracao' => $integracao]);
    }

    
}
