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
            'cliente_novo' => 'required|boolean',
            'telefone' => 'required|array|min:1',
            'telefone.*' => 'required|string|min:8|max:14',
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

            'cliente_novo.required' => 'O campo cliente novo é obrigatório',
            'cliente.boolean' => 'O campo cliente novo tem que ser um bool',

            'telefone.required' => 'O campo telefone é um campo obrigatório.',
            'telefone.array' => 'O campo telefone está com formato inválido.',
            'telefone.min' => 'O campo telefone deve ter no mínimo :min caracteres.',
            'telefone.max' => 'O campo telefone deve ter no máximo :max caracteres.',

            'telefone.*.required' => 'Todos os telefones precisam ser preenchidos.',
            'telefone.*.string' => 'Cada telefone deve ser um texto válido.',
            'telefone.*.min' => 'Cada telefone deve ter no mínimo :min caracteres.',
            'telefone.*.max' => 'Cada telefone deve ter no máximo :max caracteres.',
        ];
    }
}
