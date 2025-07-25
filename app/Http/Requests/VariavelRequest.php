<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariavelRequest extends FormRequest
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
            'nome_variavel' => 'required|string|min:2|max:255',
            'campo_alvo' => 'required|string|min:5|max:255',
            'tipo' => 'required|in:numerico,booleano,string'
        ];
    }

    public function messages()
    {
        return [
            'nome_variavel.required' => 'O campo "nome da variável" é obrigatório.',
            'nome_variavel.string' => 'O campo "nome da variável" deve ser um texto.',
            'nome_variavel.min' => 'O campo "nome da variável" deve ter no mínimo :min caracteres.',
            'nome_variavel.max' => 'O campo "nome da variável" deve ter no máximo :max caracteres.',

            'campo_alvo.required' => 'O campo "campo alvo" é obrigatório.',
            'campo_alvo.string' => 'O campo "campo alvo" deve ser um texto.',
            'campo_alvo.min' => 'O campo "campo alvo" deve ter no mínimo :min caracteres.',
            'campo_alvo.max' => 'O campo "campo alvo" deve ter no máximo :max caracteres.',

            'tipo.required' => 'O campo "tipo" é obrigatório.',
            'tipo.in' => 'O campo "tipo" deve ser um dos seguintes valores: numérico, booleano ou string.',
        ];
    }
}
