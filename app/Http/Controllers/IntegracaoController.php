<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

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
     * @param IntegracaoRequest $request
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
        $integracoes = Integracao::withTrashed()->get();
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

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param IntegracaoRequest
     * @param int $id
     * @return Redirect
     */
    public function updateIntegracao(IntegracaoRequest $request, $id){
        $request->validated();

        $integracao = Integracao::findOrFail($id);

        $integracao->update([
            'sistema' => $request->sistema,
            'descricao' => $request->descricao,
            'slug' => $request->slug,
            'endpoint' => $request->endpoint,
            'auth' => $request->auth, 
            'tipo' => $request->tipo, 
        ]);

        session()->flash('mensagem', 'Integração Alterada com sucesso');

        return redirect()->route('read.integracao');
    }

    /**
     * Excluí (sofdelete)
     */
    public function deleteIntegracao($id){
        $integracao = Integracao::findOrFail($id);

        $integracao->update([
            'username' => null,
            'password_enc' => null
        ]);

        $integracao->delete();

        session()->flash('mensagem', 'Integração Inativada com sucesso');

        return redirect()->route('read.integracao');
    }

    /**
     * Excluí (sofdelete)
     */
    public function restoreIntegracao($id){
        $integracao = Integracao::withTrashed()->findOrFail($id);

        $integracao->restore();

        session()->flash('mensagem', 'Integração reativada com sucesso');

        return redirect()->route('read.integracao');
    }

    public function authIntegracao($id){
        $integracao = Integracao::findOrFail($id);
        return view('Integracao/Integracao_auth', ['integracao' => $integracao]);
    }

    public function setAuthIntegracao(Request $request, $id){
        $integracao = Integracao::findOrFail($id);

        $data = [
            'username' => $request->username ?? null,
        ];

        // Só atualiza a senha se foi fornecida
        if (!empty($request->password)) {
            $data['password_enc'] = Crypt::encryptString($request->password);
        }

        $integracao->update($data);

        session()->flash('mensagem', 'Autenticação da integração atualizado com sucesso');

        return redirect()->route('read.integracao');
    }
}
