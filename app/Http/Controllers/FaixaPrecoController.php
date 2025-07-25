<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variaveis_Precificacao;
use App\Models\Faixa_Precificacao;

use App\Http\Requests\VariavelRequest;

class FaixaPrecoController extends Controller
{
    /**
     * Retorna a view de cadastro de variavel de preço
     */
    public function cadastroVariavelPrecificacao(){
        return view('FaixaPreco/Variavel_new');
    }

    /**
     * Salva no banco a variável de precificação, passando por uma válidação de request
     * @param  \App\Http\Requests\VariavelRequest  $request  Requisição validada contendo os dados da variavel.
     */
    public function createVariavelPrecificacao(VariavelRequest $request){
        $request->validated();

        Variaveis_Precificacao::create([
            'nome' => $request->nome_variavel,
            'campo_alvo' => $request->campo_alvo,
            'tipo' => $request->tipo,
        ]);

        session()->flash('mensagem','Variável criada com sucesso');

        return redirect()->route('read.variavel');
    }

    /**
     * Retorna a view de show variavel precificacao
     */
    public function readVariavelPrecificacao(){
        $variaveis = Variaveis_Precificacao::all();
        return view('FaixaPreco/Variavel_show', ['variaveis' => $variaveis]);
    }

    /**
     * retorna a view de edição da variavel de precificação junto da variável desejada junto a um findorfail com o id
     * @param $id
     */
    public function alterarVariavelPrecificacao($id){
        $variavel = Variaveis_Precificacao::findOrFail($id);
        return view('FaixaPreco/Variavel_edit', ['variavel' => $variavel]);
    }

    /**
     * altera no banco a variável de precificação, passando por uma válidação de request
     * @param  \App\Http\Requests\VariavelRequest  $request  Requisição validada contendo os dados da variavel.
     */
    public function editVariavelPrecificacao(VariavelRequest $request, $id){
        $request->validated();

        $variavel = Variaveis_Precificacao::findOrFail($id);

        $variavel->update([
            'nome' => $request->nome_variavel,
            'campo_alvo' => $request->campo_alvo,
            'tipo' => $request->tipo,
            'ativo' => $request->status
        ]);

        session()->flash('mensagem','Variável editada com sucesso');

        return redirect()->route('read.variavel');
    }

    /**
     * deleta a variável dado o id
     * @param $id
     */
    public function deleteVariavelPrecificacao($id){
        $variavel = Variaveis_Precificacao::findOrFail($id);

        $variavel->delete();

        session()->flash('mensagem', 'Variável excluída com sucesso');

        return redirect()->route('read.variavel');
    }
}
