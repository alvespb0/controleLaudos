<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Op_Tecnico;
use App\Http\Requests\Op_TecnicoRequest;

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
    public function createTecnico(Request $request){
        $request->validated();

        Op_Tecnico::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'usuario' => $request->usuario
        ]);

        session()->flash('mensagem', 'Técnico registrado com sucesso');

        return redirect('')->route('readTecnico');
    }

    /**
     * retorna os tecnicos cadastrados no banco
     * @return Array
     */
    public function readTecnico(){
        $tecnicos = Op_Tecnico::all();
        return view('OpTecnico/Tecnico_show', ['tecnicos'=> $tecnicos]);
    }
}
