<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
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
        $id = $this->route('id'); 

        return [
            'nome' => 'required|string|min:5|max:255',
            'cnpj' => "required|string|min:11|max:14|unique:cliente,cnpj,{$id}",
            'email' => 'nullable|string|min:5|max:255',
            'tipo_cliente' => 'required|in:novo,renovacao,resgatado',
            'telefone' => 'required|array|min:1',
            'telefone.*' => 'required|string|min:8|max:14',
            'cep' => 'required|string|min:8|max:9',
            'rua' => 'required|string|min:4|max:255',
            'numero' => 'required|numeric|min:1',
            'bairro' => 'required|string|min:4|max:255',
            'complemento' => 'nullable|string|min:3|max:255',
            'cidade' => 'required|string|min:4|max:255',
            'uf' => 'required|string|size:2',
            'cep_cobranca' => 'nullable|string|min:8|max:9',
            'rua_cobranca' => 'nullable|string|min:4|max:255',
            'numero_cobranca' => 'nullable|numeric|min:1',
            'bairro_cobranca' => 'nullable|string|min:4|max:255',
            'complemento_cobranca' => 'nullable|string|min:3|max:255',
            'cidade_cobranca' => 'nullable|string|min:4|max:255',
            'uf_cobranca' => 'nullable|string|size:2',
            'email_cobranca' => 'nullable|string|min:5|max:255',
            'telefone_cobranca' => 'nullable|string|min:6'
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string'   => 'O campo nome deve ser um texto.',
            'nome.min'      => 'O campo nome deve ter no mínimo :min caracteres.',
            'nome.max'      => 'O campo nome deve ter no máximo :max caracteres.',

            'cnpj.required' => 'O campo CNPJ é obrigatório.',
            'cnpj.string'   => 'O campo CNPJ deve conter apenas números.',
            'cnpj.min'     => 'O CNPJ/CPF deve conter no mínimo :min caracteres (CPF sem pontuação).',
            'cnpj.max'     => 'O CNPJ/CPF deve conter no máximo :max caracteres (CNPJ sem pontuação).',
            'cnpj.unique'   => 'Este CNPJ já está cadastrado.',   
            
            'email.string' => 'O campo email deve ser um texto',
            'email.min' => 'O campo email deve ter no mínimo :min caracteres',
            'email.max' => 'O campo email deve ter no máximo :max caracteres',

            'tipo_cliente.required' => 'O campo cliente novo é obrigatório',
            'tipo_cliente.in' => 'O campo cliente novo tem que novo, renovação ou resgatado',

            'telefone.required' => 'O campo telefone é um campo obrigatório.',
            'telefone.array' => 'O campo telefone está com formato inválido.',
            'telefone.min' => 'O campo telefone deve ter no mínimo :min caracteres.',
            'telefone.max' => 'O campo telefone deve ter no máximo :max caracteres.',

            'telefone.*.required' => 'Todos os telefones precisam ser preenchidos.',
            'telefone.*.string' => 'Cada telefone deve ser um texto válido.',
            'telefone.*.min' => 'Cada telefone deve ter no mínimo :min caracteres.',
            'telefone.*.max' => 'Cada telefone deve ter no máximo :max caracteres.',

            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.string' => 'O CEP deve ser um valor textual.',
            'cep.min' => 'O CEP deve ter pelo menos 8 caracteres.',
            'cep.max' => 'O CEP deve ter no máximo 9 caracteres.',
            
            'rua.required' => 'O campo Rua é obrigatório.',
            'rua.string' => 'A Rua deve ser um valor textual.',
            'rua.min' => 'O campo Rua deve ter pelo menos 4 caracteres.',
            'rua.max' => 'O campo Rua deve ter no máximo 255 caracteres.',
            
            'numero.required' => 'O campo Número é obrigatório.',
            'numero.numeric' => 'O Número deve ser um valor numérico.',
            'numero.min' => 'O Número deve ser pelo menos 1.',
            
            'bairro.required' => 'O campo Bairro é obrigatório.',
            'bairro.string' => 'O Bairro deve ser um valor textual.',
            'bairro.min' => 'O campo Bairro deve ter pelo menos 4 caracteres.',
            'bairro.max' => 'O campo Bairro deve ter no máximo 255 caracteres.',
            
            'complemento.string' => 'O Complemento deve ser um valor textual.',
            'complemento.min' => 'O Complemento deve ter pelo menos 3 caracteres.',
            'complemento.max' => 'O Complemento deve ter no máximo 255 caracteres.',
            
            'cidade.required' => 'O campo Cidade é obrigatório.',
            'cidade.string' => 'A Cidade deve ser um valor textual.',
            'cidade.min' => 'O campo Cidade deve ter pelo menos 4 caracteres.',
            'cidade.max' => 'O campo Cidade deve ter no máximo 255 caracteres.',
            
            'uf.required' => 'O campo UF é obrigatório.',
            'uf.string' => 'A UF deve ser um valor textual.',
            'uf.size' => 'A UF deve ter exatamente 2 caracteres.',

            'cep_cobranca.min' => 'O CEP de cobrança deve ter no mínimo 8 caracteres.',
            'cep_cobranca.max' => 'O CEP de cobrança deve ter no máximo 9 caracteres.',
            'cep_cobranca.string' => 'O CEP de cobrança deve ser um texto válido.',

            'rua_cobranca.min' => 'A rua de cobrança deve ter no mínimo 4 caracteres.',
            'rua_cobranca.max' => 'A rua de cobrança deve ter no máximo 255 caracteres.',
            'rua_cobranca.string' => 'A rua de cobrança deve ser um texto válido.',

            'numero_cobranca.numeric' => 'O número de cobrança deve conter apenas números.',
            'numero_cobranca.min' => 'O número de cobrança deve ser no mínimo 1.',

            'bairro_cobranca.min' => 'O bairro de cobrança deve ter no mínimo 4 caracteres.',
            'bairro_cobranca.max' => 'O bairro de cobrança deve ter no máximo 255 caracteres.',
            'bairro_cobranca.string' => 'O bairro de cobrança deve ser um texto válido.',

            'complemento_cobranca.min' => 'O complemento de cobrança deve ter no mínimo 3 caracteres.',
            'complemento_cobranca.max' => 'O complemento de cobrança deve ter no máximo 255 caracteres.',
            'complemento_cobranca.string' => 'O complemento de cobrança deve ser um texto válido.',

            'cidade_cobranca.min' => 'A cidade de cobrança deve ter no mínimo 4 caracteres.',
            'cidade_cobranca.max' => 'A cidade de cobrança deve ter no máximo 255 caracteres.',
            'cidade_cobranca.string' => 'A cidade de cobrança deve ser um texto válido.',

            'uf_cobranca.size' => 'O UF de cobrança deve conter exatamente 2 letras.',
            'uf_cobranca.string' => 'O UF de cobrança deve ser um texto válido.',

            'email_cobranca.min' => 'O e-mail de cobrança deve ter no mínimo 5 caracteres.',
            'email_cobranca.max' => 'O e-mail de cobrança deve ter no máximo 255 caracteres.',
            'email_cobranca.string' => 'O e-mail de cobrança deve ser um texto válido.',

            'telefone_cobranca.min' => 'O telefone de cobrança deve ter no mínimo 6 caracteres.',
            'telefone_cobranca.string' => 'O telefone de cobrança deve ser um texto válido.',

        ];
    }
}
