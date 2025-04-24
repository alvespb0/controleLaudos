<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Retorna a view da página de login
     */
    public function login(){
        return view('/auth/login');
    }

    /**
     * recebe via post um email e senha, e verifica se as credenciais são válidas para login
     * @param Request
     * @return Redirect
     */
    public function tryLogin(Request $request){
        $credenciais = [
            'email' => $request->email,
            'password'=> $request->password
        ];

        if(Auth::attempt($credenciais)){
            return redirect(route('dashboard.show'))->with('success','');
        }

        session->flash('mensagem', 'Credenciais Inválidas');
        return redirect(route('login'));
    }

    /**
     * Faz o logut
     */
    public function logout(){
        Auth::logout();

        return redirect(route('login'));
    }
}

