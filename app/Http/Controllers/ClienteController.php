<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Telefone;
use App\Models\Endereco_Cliente;
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
            'email' => $request->email,
            'cliente_novo' => $request->cliente_novo
       ]);

        foreach($request->telefone as $telefone){
            Telefone::create([
                'telefone' =>  $telefone,
                'cliente_id' => $cliente->id
            ]);
        }

        Endereco_Cliente::create([
            'cliente_id' => $cliente->id,
            'cep' => $request->cep,
            'bairro' => $request->bairro,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'cidade' => $request->cidade,
            'uf' => $request->uf
        ]);

       session()->flash('mensagem', 'Cliente registrado com sucesso');

       return redirect()->route('readCliente');
   }

   /**
    * retorna os cleintes cadastrados no banco
    * @return Array
    */
   public function readCliente(){
        $clientes = Cliente::orderBy('nome', 'asc')->paginate(10);
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
            'email' => $request->email,
            'cliente_novo' => $request->cliente_novo
        ]);
 
        $cliente->telefone()->delete();
 
        foreach($request->telefone as $telefone){
             Telefone::create([
                 'telefone' => $telefone,
                 'cliente_id' => $cliente->id
             ]);
         }

        $cliente->endereco()->delete();

        Endereco_Cliente::create([
            'cliente_id' => $cliente->id,
            'cep' => $request->cep,
            'bairro' => $request->bairro,
            'rua' => $request->rua,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'cidade' => $request->cidade,
            'uf' => $request->uf
        ]);
        
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

    /**
     * recebe uma request e busca no banco um cliente com esse nome ou CNPJ utilizando like
     * @param Request
     * @return Array
     */
    public function filterCliente(Request $request){
        $cliente = Cliente::where('nome', 'like', '%'. $request->cliente .'%')
                        ->orWhere('cnpj', 'like', '%'. $request->cliente . '%')
                        ->paginate(10);
        return view('Cliente/Cliente_show', ['clientes'=> $cliente]);
    }
   

}
