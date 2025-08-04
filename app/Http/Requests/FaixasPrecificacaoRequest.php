<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaixasPrecificacaoRequest extends FormRequest
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
            'variavel_id' => 'required|exists:Variaveis_Precificacao,id',
            'valor_min' => 'required|numeric|min:0',
            'valor_max' => 'required|numeric|min:0|gte:valor_min',
            'percentual_reajuste' => 'nullable|numeric|min:0',
            'preco_min' => 'nullable|numeric|min:0',
            'preco_max' => 'nullable|numeric|min:0|gte:preco_min'
        ];
    }

    public function messages(): array
    {
        return [
            'valor_min.required' => 'O valor mínimo é obrigatório.',
            'valor_min.numeric' => 'O valor mínimo deve ser um número.',
            'valor_min.min' => 'O valor mínimo deve ser no mínimo 0.',

            'valor_max.required' => 'O valor máximo é obrigatório.',
            'valor_max.numeric' => 'O valor máximo deve ser um número.',
            'valor_max.min' => 'O valor máximo deve ser no mínimo 0.',
            'valor_max.gte' => 'O valor máximo deve ser maior ou igual ao valor mínimo.',

            'percentual_reajuste.numeric' => 'O percentual de reajuste deve ser um número.',
            'percentual_reajuste.min' => 'O percentual de reajuste deve ser no mínimo 0.',

            'preco_min.numeric' => 'O preço deve ser um número.',
            'preco_min.min' => 'O preço deve ser no mínimo 0.',

            'preco_max.numeric' => 'O preço máximo deve ser um número.',
            'preco_max.min' => 'O preço máximo deve ser no mínimo 0.',
            'preco_max.gte' => 'O preço máximo deve ser maior que o preço minimo.',
        ];
    }

}
