<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\Op_TecnicoRequest;

use App\Models\User;
use App\Models\Op_Tecnico;

class Op_TecnicoController extends Controller
{
    /**
     * Retorna a pagina de cadastro do operador tecnico de segurança
     * @return View
     */
    public function cadastroTecnico(){
        return view("OpTecnico/Tecnico_new");
    }

    /**
     * Recebe uma request via POST valida os dados, se validado cadastra no banco
     * Se não retorna o erro
     * @param Request $request
     * @return Redirect
     */
    public function createTecnico(Op_TecnicoRequest  $request){
        $request->validated();

        $user = User::create([
            'name' => $request->usuario,
            'email' => $request->email,
            'password' => $request->password,
            'tipo' => 'seguranca'
        ]);

        Op_Tecnico::create([
            'usuario' => $request->usuario,
            'user_id' => $user->id
        ]);

        session()->flash('mensagem', 'Técnico registrado com sucesso');

        return redirect()->route('readTecnico');
    }

    /**
     * retorna os tecnicos cadastrados no banco
     * @return Array
     */
    public function readTecnico(){
        $tecnicos = Op_Tecnico::all();
        return view('OpTecnico/Tecnico_show', ['tecnicos'=> $tecnicos]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do do tecnico 
     * @param int $id
     * @return array
     */
    public function alteracaoTecnico($id){
        $tecnico = Op_Tecnico::findOrFail($id);
        return view('OpTecnico/Tecnico_edit', ['tecnico' => $tecnico]);
    }

    /**
     * Recebe uma request faz a validação dos dados e faz o update dado o id
     * @param Request
     * @param int $id
     * @return Redirect
     */
    public function updateTecnico(Op_TecnicoRequest $request, $id){
        $request->validated();

        $tecnico = Op_Tecnico::findOrFail($id);
        
        $user = $tecnico->User;

        $user->update([
            'name' => $request->usuario,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $tecnico->update([
            'usuario' => $request->usuario
        ]);

        session()->flash('mensagem', 'Técnico Alterado com sucesso');

        return redirect()->route('readTecnico');
    }

    /**
     * recebe o id e deleta o tecnico vinculado nesse ID
     * @param int $id
     * @return view
     */
    public function deleteTecnico($id){
        $tecnico = Op_Tecnico::findOrFail($id);

        $user = $tecnico->User;

        $user->delete();

        session()->flash('mensagem', 'Operador Tecnico Excluido com sucesso');

        return redirect()->route('readTecnico');
    }

    
}
