<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\Op_ComercialRequest;

use App\Models\Op_Comercial;
use App\Models\User;

class Op_ComercialController extends Controller
{
    /**
     * Retorna a pagina de cadastro do operador comercial
     * @return View
     */
    public function cadastroComercial(){
        return view("OpComercial/Comercial_new");
    }

    /**
     * Recebe uma request via POST valida os dados, se validado cadastra no banco
     * Se não retorna o erro
     * @param Request $request
     * @return Redirect
     */
    public function createComercial(Op_ComercialRequest $request){
        $request->validated();

        $user = User::create([
            'name' => $request->usuario,
            'email' => $request->email,
            'password' => $request->password,
            'tipo' => 'comercial'
        ]);

        Op_Comercial::create([
            'usuario' => $request->usuario,
            'user_id' => $user->id
        ]);

        session()->flash('mensagem', 'Operador Comercial registrado com sucesso');

        return redirect()->route('readComercial');
    }

    /**
     * retorna os funcionarios do comercial cadastrados no banco
     * @return Array
     */
    public function readComercial(){
        $comercial = Op_Comercial::all();
        return view('OpComercial/Comercial_show', ['comercial'=> $comercial]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do funcionario do comercial 
     * @param int $id
     * @return array
     */
    public function alteracaoComercial($id){
        $comercial = Op_Comercial::findOrFail($id);
        return view('OpComercial/Comercial_edit', ['comercial' => $comercial]);
    }

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param Request
     * @param int $id
     * @return Redirect
     */
    public function updateComercial(Op_ComercialRequest $request, $id){
        $request->validated();

        $Op_Comercial = Op_Comercial::findOrFail($id);

        $user = $Op_Comercial->User;

        $user->update([
            'name' => $request->usuario,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $Op_Comercial->update([
            'usuario' => $request->usuario
        ]);

        session()->flash('mensagem', 'Operador Comercial Alterado com sucesso');

        return redirect()->route('readComercial');
    }

    /**
     * recebe o id e deleta o comercial vinculado nesse ID
     * @param int $id
     * @return view
     */
    public function deleteComercial($id){
        $Op_Comercial = Op_Comercial::findOrFail($id);

        $user = $Op_Comercial->User;

        $user->delete();

        session()->flash('mensagem', 'Operador Comercial Excluido com sucesso');
        
        return redirect()->route('readComercial');
    }

}
