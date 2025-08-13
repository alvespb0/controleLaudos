<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecomendadorRequest extends FormRequest
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
            'cpf' => "required|string|min:11|max:14|unique:recomendadores,cpf,{$id}",
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto válido.',
            'nome.min' => 'O nome deve ter no mínimo 5 caracteres.',
            'nome.max' => 'O nome pode ter no máximo 255 caracteres.',

            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.string' => 'O CPF deve ser um texto válido.',
            'cpf.min' => 'O CPF deve ter no mínimo 11 caracteres.',
            'cpf.max' => 'O CPF pode ter no máximo 14 caracteres.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ];
    }

}
