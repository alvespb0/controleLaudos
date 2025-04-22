<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|min:5|max:255',
            'cor'    => 'nullable', 'regex:/^#([a-fA-F0-9]{6})$/',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string'   => 'O campo nome deve ser um texto.',
            'nome.min'      => 'O campo nome deve ter no mínimo :min caracteres.',
            'nome.max'      => 'O campo nome deve ter no máximo :max caracteres.',

            'cor.regex' => 'A cor deve estar no formato hexadecimal válido (ex: #FF0000).',
        ];
    }
}