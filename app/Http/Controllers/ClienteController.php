<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Telefone;
use App\Http\Requests\ClienteRequest;

class ClienteController extends Controller
{
    /**
    * Retorna a pagina de cadastro do Cliente
    * @return View
    */
   public function cadastroCliente(){
       return view("Cliente/Cliente_new");
   }

   /**
    * Recebe uma request via POST valida os dados, se validado cadastra no banco
    * Se não retorna o erro
    * @param ClienteRequest $request
    * @return Redirect
    */
   public function createCliente(ClienteRequest $request){
       $request->validated();

       $cliente = Cliente::create([
            'nome'=> $request->nome,
            'cnpj' => $request->cnpj,
            'email' => $request->email
       ]);

        foreach($request->telefone as $telefone){
            Telefone::create([
                'telefone' =>  $telefone,
                'cliente_id' => $cliente->id
            ]);
        }
       session()->flash('mensagem', 'Cliente registrado com sucesso');

       return redirect()->route('readCliente');
   }

   /**
    * retorna os cleintes cadastrados no banco
    * @return Array
    */
   public function readCliente(){
       $clientes = Cliente::all();
       return view('Cliente/Cliente_show', ['clientes'=> $clientes]);
   }

   /**
    * recebe um ID valida se o ID é válido via find or fail
    * se for válido retorna o formulario de edição do cliente 
    * @param int $id
    * @return array
    */
   public function alteracaoCliente($id){
       $cliente = Cliente::findOrFail($id);
       return view('Cliente/Cliente_edit', ['cliente' => $cliente]);
   }

  /**
    * Recebe uma request faz a validação dos dados e faz o update dado o id
    * @param Request
    * @param int $id
    * @return Redirect
    */
    public function updateCliente(ClienteRequest $request, $id){
        $request->validated();
 
        $cliente = Cliente::findOrFail($id);
 
        $cliente->update([
             'nome'=> $request->nome,
             'cnpj' => $request->cnpj,
             'email' => $request->email
        ]);
 
        $cliente->telefone()->delete();
 
        foreach($request->telefone as $telefone){
             Telefone::create([
                 'telefone' => $telefone,
                 'cliente_id' => $cliente->id
             ]);
         }
        session()->flash('mensagem', 'Cliente Alterado com sucesso');
 
        return redirect()->route('readCliente');
    }
 
 
   /**
    * recebe o id e deleta o cliente vinculado nesse ID
    * @param int $id
    * @return view
    */
   public function deleteCliente($id){
       $cliente = Cliente::findOrFail($id);

       $cliente->delete();

       session()->flash('mensagem', 'Cliente Excluido com sucesso');

       return redirect()->route('readCliente');
   }

}
