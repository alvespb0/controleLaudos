<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Variaveis_Precificacao;
use App\Models\Faixa_Precificacao;

use App\Http\Requests\VariavelRequest;
use App\Http\Requests\FaixasPrecificacaoRequest;

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
            'valor' => $request->valor
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

        if($variavel->id == 1 || $variavel->id == 2){
            $variavel->update([
                'campo_alvo' => $request->campo_alvo,
                'valor' => $request->valor,
                'ativo' => $request->status
            ]);
        }else{
            $variavel->update([
                'nome' => $request->nome_variavel,
                'campo_alvo' => $request->campo_alvo,
                'tipo' => $request->tipo,
                'valor' => $request->valor,
                'ativo' => $request->status
            ]);            
        }

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

    /**
     * Exibe todas as faixas de precificação relacionadas a uma variável específica.
     *
     * @param $id ID da variável associada às faixas de precificação.
     */
    public function faixasPrecos($id){
        $faixas = Faixa_Precificacao::where('variavel_id', $id)
                    ->orderBy('valor_min')->get();

        $variavel = Variaveis_Precificacao::findOrFail($id);

        return view('FaixaPreco/Faixas_show', ['faixas' => $faixas, 'variavel' => $variavel]);
    }

    /**
     * Armazena uma nova faixa de precificação, após validar e verificar sobreposição com faixas existentes.
     *
     * @param \App\Http\Requests\FaixasPrecificacaoRequest $request Request validado contendo os dados da nova faixa.
     * @return \Illuminate\Http\RedirectResponse Redireciona de volta para a tela de faixas com mensagem de sucesso ou erro.
     *
     * @throws \Illuminate\Validation\ValidationException Se os dados do request forem inválidos.
     */
    public function createFaixaPreco(FaixasPrecificacaoRequest $request){
        $request->validated();

        $faixas = Faixa_Precificacao::where('variavel_id', $request->variavel_id)->get();

        foreach ($faixas as $faixa) {
            if ($request->valor_min <= $faixa->valor_max && $request->valor_max >= $faixa->valor_min) {
                session()->flash('error', 'Faixa sobrepõe a existente de ' . $faixa->valor_min . ' até ' . $faixa->valor_max);
                return redirect()->route('faixa.preco', $request->variavel_id);
            }
        }

        Faixa_Precificacao::create([
            'variavel_id' => $request->variavel_id,
            'valor_min' => $request->valor_min,
            'valor_max' => $request->valor_max,
            'percentual_reajuste' => $request->percentual_reajuste,
            'preco' => $request->preco
        ]);

        session()->flash('mensagem', 'Faixa criada com sucesso');

        return redirect()->route('faixa.preco', $request->variavel_id);
    }

    /**
     * Altera faixa de precificação, após validar e verificar sobreposição com faixas existentes.
     *
     * @param \App\Http\Requests\FaixasPrecificacaoRequest $request Request validado contendo os dados da nova faixa.
     * @param $id Id da faixa
     * @return \Illuminate\Http\RedirectResponse Redireciona de volta para a tela de faixas com mensagem de sucesso ou erro.
     *
     * @throws \Illuminate\Validation\ValidationException Se os dados do request forem inválidos.
     */
    public function editFaixaPreco(FaixasPrecificacaoRequest $request, $id){
        $request->validated();

        $faixas = Faixa_Precificacao::where('variavel_id', $request->variavel_id)->get();

        foreach ($faixas as $faixa) {
            if ($faixa->id != $id && $request->valor_min <= $faixa->valor_max && $request->valor_max >= $faixa->valor_min) {
                session()->flash('error', 'Faixa sobrepõe a existente de ' . $faixa->valor_min . ' até ' . $faixa->valor_max);
                return redirect()->route('faixa.preco', $request->variavel_id);
            }
        }

        $faixa = Faixa_Precificacao::findOrFail($id);

        $faixa->update([
            'valor_min' => $request->valor_min,
            'valor_max' => $request->valor_max,
            'percentual_reajuste' => $request->percentual_reajuste,
            'preco' => $request->preco
        ]);

        session()->flash('mensagem', 'Faixa alterada com sucesso');

        return redirect()->route('faixa.preco', $request->variavel_id);
    }

    public function deleteFaixa($id){
        $faixa = Faixa_Precificacao::findOrFail($id);

        $variavel_id = $faixa->variavel->id;

        #dd($variavel_id);
        $faixa->delete();

        session()->flash('mensagem', 'Faixa excluída com sucesso');

        return redirect()->route('faixa.preco', $variavel_id);
    }
}
