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
        return [
            'nome' => 'required|string|min:5|max:255',
            'cnpj' => 'required|string|size:14|unique:cliente,cnpj'
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
            'cnpj.size'     => 'O CNPJ deve conter exatamente :size caracteres (sem pontuação).',
            'cnpj.unique'   => 'Este CNPJ já está cadastrado.',    
        ];
    }
}
