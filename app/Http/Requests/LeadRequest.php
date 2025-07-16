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
            'cliente_id' => 'required|exists: cliente,id',
            'status_id' => 'required|exists: status_crm,id',
            'observacoes' => 'nullable|string',
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

            'proximo_contato.date'    => 'A data de próximo contato deve ser uma data válida.',
        ];
    }

}
