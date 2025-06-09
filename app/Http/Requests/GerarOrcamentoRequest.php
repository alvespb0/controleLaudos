<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GerarOrcamentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'numProposta' => 'required|integer|min:1',
            'razaoSocialCliente' => 'required|string|min:5',
            'nomeUnidade' => 'required|string|min:5',
            'cnpjCliente' => 'required|string|min:11|max:14',
            'telefoneCliente' => 'required|string|min:6',
            'emailCliente' => 'required|string|min:5',
            'nomeContato' => 'required|string|min:4',
            'numFuncionarios' => 'required|integer|min:1',
            'investimento' => 'required|numeric|min:0.01',
            'parcelasTexto' => 'required|integer|min:1'
        ];
    }

    public function messages(): array
{
    return [
        'numProposta.required' => 'O campo número da proposta é obrigatório',
        'numProposta.integer' => 'O campo número da proposta deve ser um número',
        'numProposta.min' => 'O campo número da proposta deve ser no mínimo 1',
        
        'razaoSocialCliente.required' => 'A razão social é obrigatória.',
        'razaoSocialCliente.min' => 'A razão social deve ter no mínimo 5 caracteres.',
        
        'nomeUnidade.required' => 'O nome da unidade é obrigatória.',
        'nomeUnidade.min' => 'O nome da unidade deve ter no mínimo 5 caracteres.',

        'cnpjCliente.required' => 'O CNPJ é obrigatório.',
        'cnpjCliente.min' => 'O CNPJ deve ter no mínimo 11 caracteres.',
        'cnpjCliente.max' => 'O CNPJ deve ter no máximo 14 caracteres.',

        'telefoneCliente.required' => 'O telefone é obrigatório.',
        'telefoneCliente.min' => 'O telefone deve conter no mínimo 6 dígitos.',

        'emailCliente.required' => 'O e-mail é obrigatório.',
        'emailCliente.email' => 'O e-mail informado não é válido.',

        'nomeContato.required' => 'O nome do contato é obrigatório.',
        'nomeContato.min' => 'O nome do contato deve ter no mínimo 4 caracteres.',

        'numFuncionarios.required' => 'Informe o número de funcionários.',
        'numFuncionarios.integer' => 'O número de funcionários deve ser um número inteiro.',
        'numFuncionarios.min' => 'Deve haver pelo menos 1 funcionário.',

        'investimento.required' => 'O valor do investimento é obrigatório.',
        'investimento.numeric' => 'O valor do investimento deve ser um número.',
        'investimento.min' => 'O investimento deve ser de no mínimo 0.01.',

        'parcelasTexto.required' => 'Informe o número de parcelas.',
        'parcelasTexto.integer' => 'O número de parcelas deve ser um número inteiro.',
        'parcelasTexto.min' => 'Deve haver ao menos 1 parcela.'
    ];
}

}
