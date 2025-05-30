<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;

use Illuminate\Support\Facades\Mail;
use App\Mail\TokenRecuperacaoMail;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TokenRecuperacao;
use App\Models\Op_Comercial;
use App\Models\Op_Tecnico;

class AuthController extends Controller
{
    /**
     * Retorna a view da página de login
     */
    public function login(){
        return view('/Auth/Login');
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

        session()->flash('mensagem', 'Credenciais Inválidas');
        return redirect(route('login.show'));
    }

    /**
     * Faz o logut
     */
    public function logout(){
        Auth::logout();

        return redirect(route('login.show'));
    }

    /**
     * retorna a página de registro
     */
    public function register(){
        return view('Auth/User_New');
    }

    /**
     * recebe uma request do tipo RegisterRequest valida qual o tipo de operador que está sendo cadastrado e salva no banco
     * @param RegisterRequest $request
     * @return Redirect 
     */
    public function createUser(RegisterRequest $request){
        $request->validated();

        if($request->tipo == 'comercial'){
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

            return redirect()->route('readUsers');
        
        }else if($request->tipo == 'seguranca'){
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
    
            return redirect()->route('readUsers');

        }else if($request->tipo == 'admin'){
            $user = User::create([
                'name' => $request->usuario,
                'email' => $request->email,
                'password' => $request->password,
                'tipo' => 'admin'
            ]);    

            session()->flash('mensagem', 'Admin registrado com sucesso');
    
            return redirect()->route('readUsers');

        }else{
            session()->flash('error', 'tipo selecionado inválido');
    
            return redirect()->route('cadastro.user');
        }
    }

    /**
     * retorna a página de visualização de usuários
     * @return Array <users>
     */
    public function readUsers(){
        $users = User::all();
        return view('Auth/User_show', ['users' => $users]);
    }

    /**
     * recebe um ID valida se o ID é válido via find or fail
     * se for válido retorna o formulario de edição do User
     * @param int $id
     * @return array
     */
    public function alteracaoUser($id){
        $user = User::findOrFail($id);
        return view('Auth/User_Edit', ['user' => $user]);
    }


    /**
     * Recebe uma Request faz a a validação de dados e faz o update dado o Id
     * @param RegisterRequest $request
     * @param int $id
     * @return Redirect
     */
    public function updateUser(RegisterRequest $request, $id){
        $request->validated();
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->usuario,
            'email' => $request->email,
            'password' => $request->password,
            'tipo' => $request->tipo
        ]);

        if($user->tipo == 'comercial'){
            $opComercial = Op_Comercial::where('user_id', $user->id)->first();
            $opComercial->update([
                'usuario' => $request->usuario,
            ]);
        }else if($user->tipo == 'seguranca'){
            $opTecnico = Op_Tecnico::where('user_id', $user->id)->first();
            $opTecnico->update([
                'usuario' => $request->usuario,
            ]);
        }

        session()->flash('mensagem', 'Operador alterado com sucesso');
    
        return redirect()->route('readUsers');
    }

    /**
     * deleta o user dado o ID
     */
    public function deleteUser($id){
        $user = User::findOrFail($id);

        $user->delete();

        session()->flash('mensagem', 'Operador Excluido com sucesso');

        return redirect()->route('readUsers');
    }

    /**
     * retorna apenas a view de forgot pass
     */
    public function emailUserForgotPass(){
        return view('Auth/Forgot_Pass');
    }

    /**
     * Recebe um email via POST, verifica se alguma usuário existe com esse email se existir envia um token de recuperação
     * @param Request $request
     * @return redirect
     */
    public function tokenUserForgotPass(Request $request){
        $user = User::where('email', $request->email)->first();
        /* dd($user); */
        if($user){
            $token = substr(bin2hex(random_bytes(3)), 0, 6);

            TokenRecuperacao::create([
                'email' => $user->email,
                'token' => $token,
                'expiracao' => now()->addMinutes(15)
            ]);

            $this->enviarEmailRecuperacao($user->email, $user->name, $token);

            return view('Auth/Token_Pass');
        }else{
            session()->flash('mensagem', 'teste');

            return view('/Auth/Login');    
        }
    }

    /**
     * faz o envio do email de recuperação de senha
     */
    private function enviarEmailRecuperacao($email, $nome, $token){
        Mail::mailer('default')->to($email)->send(new TokenRecuperacaoMail($token, $nome));
    }

    /**
     * recebe o roken valida se existe na tabla e se não está expirado
     * @param string
     */
    public function validateTokenPass(Request $request){
        $token = implode('', [
            $request->digit1, $request->digit2, $request->digit3,
            $request->digit4, $request->digit5, $request->digit6,
        ]);
        
        $token = TokenRecuperacao::where('token', $token)->first();

        if (!$token) {
            session()->flash('mensagem', 'Token inválido.');
            return view('Auth/Token_Pass');
        }
    
        if (strtotime($token->expiracao) < now()->timestamp) {
            session()->flash('mensagem', 'Token expirado. Solicite um novo.');
            return view('Auth/Token_Pass');
        }

        $user = User::where('email', $token->email)->first();

        if (!$user) {
            session()->flash('mensagem', 'Usuário não encontrado.');
            return view('Auth/Token_Pass');
        }
        
        return view('Auth/Alter_Pass', ['userId' => $user->id, 'tokenId' => $token->id]);
    }

    /**
     * recebe um id de usuário via input hidden e altera a senha desse usuário
     * @param int $id
     * @param string $password
     * @return redirect
     */
    public function alterPassUser(Request $request){
        $user = User::findOrFail($request->id);

        $user->update([
            'password'=> $request->password,
        ]);

        $token = TokenRecuperacao::findOrFail($request->tokenId);
        $token->delete();

        session()->flash('success','Senha alterado com sucesso!');
        return redirect('login');
    }

}

