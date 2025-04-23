<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaudoRequest extends FormRequest
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
            'dataPrevisao' => 'nullable|date',
            'dataFimContrato' => 'required|date',
            'numFuncionarios' => 'required|integer|min:1',
            'cliente' => 'required|exists:cliente,id',
            'comercial' => 'required|exists:op_comercial,id'
        ];
    }
    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser uma string.',
            'nome.min' => 'O nome deve ter no mínimo :min caracteres.',
            'nome.max' => 'O nome deve ter no máximo :max caracteres.',

            'dataPrevisao.date' => 'A data de previsão deve ser uma data válida.',

            'dataFimContrato.required' => 'A data de fim do contrato é obrigatória.',
            'dataFimContrato.date' => 'A data de fim do contrato deve ser uma data válida.',

            'cliente.required' => 'O cliente é obrigatório.',
            'cliente.exists' => 'O cliente selecionado é inválido.',

            'numFuncionarios.required'=> 'O campo numero de funcionários é obrigatório',
            'numFuncionarios.integer' => 'O campo é númerico',
            'numFuncionarios.min'=> 'O valor mínimo de numero de funcionários é 1',

            'comercial.required' => 'O cliente é obrigatório.',
            'comercial.exists' => 'O cliente selecionado é inválido.',
        ];
    }

}
