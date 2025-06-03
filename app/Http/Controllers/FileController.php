<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\TemplateProcessor;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Cliente;
use App\Models\File;

class FileController extends Controller
{
    /**
     * Exibe a tela inicial para geração de orçamento.
     * O usuário poderá escolher entre:
     *  - Gerar orçamento para cliente já cadastrado (pré-carrega dados do cliente)
     *  - Gerar orçamento avulso (formulário em branco)
     * 
     * Esta função apenas redireciona para a tela de seleção/entrada de dados do orçamento.
     * 
     * @return \Illuminate\View\View
     */
    public function entradaOrcamento(){
        return view('Orcamento/Orcamento_new0');
    }

    /**
     * Recebe os dados da seleção inicial para geração de orçamento.
     * Pode conter dados do cliente selecionado ou estar vazio para orçamento avulso.
     * Encaminha os dados para a view de preenchimento do orçamento, 
     * onde o formulário será exibido com os dados já carregados (se houver).
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function formularioOrcamento(Request $request){
        $request->validate([
            'tipo_orcamento' => 'required|in:1,2', // 1 = avulso, 2 = cliente cadastrado (exemplo)
            'cliente' => 'nullable|required_if:tipo_orcamento,2|exists:clientes,id',
        ], [
            'tipo_orcamento.required' => 'o campo tipo de orçamento é obrigatório',
            'tipo_orcamento.in' => 'o campo tipo de orçamento deve ser ou avulso ou de cliente cadastrado',

            'cliente.required_if' => 'Por favor, selecione um cliente para este tipo de orçamento.',
            'cliente.exists' => 'Cliente selecionado não é válido.',
        ]);

        
        if($request->tipo_orcamento == '1'){ # 1 representa um orçamento avulso
            return view('Orcamento/Orcamento_new', ['cliente' => null]);
        }
        
        $cliente = Cliente::findOrFail($request->input('cliente'));
        return view('Orcamento/Orcamento_new', ['cliente' => $cliente]);
    }

}
