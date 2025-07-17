<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadRequest extends FormRequest
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
            'cliente_id' => 'required|exists:cliente,id',
            'status_id' => 'required|exists:status_crm,id',
            'observacoes' => 'nullable|string',
            'nome_contato' => 'nullable|string|min:4|max:255',
            'investimento' => 'nullable|numeric',
            'proximo_contato' => 'nullable|date',
        ];
    }

    public function messages(): array{
        return [
            'cliente_id.required'     => 'O campo cliente é obrigatório.',
            'cliente_id.exists'       => 'O cliente selecionado não existe no sistema.',

            'status_id.required'      => 'O campo etapa do CRM é obrigatório.',
            'status_id.exists'        => 'A etapa do CRM selecionada é inválida.',

            'observacoes.string'      => 'As observações devem ser um texto válido.',

            'nome_contato.string'     => 'O nome do contato deve ser um texto válido.',
            'nome_contato.min'        => 'O nome do contato deve ter no mínimo 4 caracteres',
            'nome_contato.max'        => 'O nome do contato deve ter no máximo 255 caracteres',

            'investimento.numeric'      => 'O numero de investimento deve ser decimal',
            
            'proximo_contato.date'    => 'A data de próximo contato deve ser uma data válida.',
        ];
    }

}
