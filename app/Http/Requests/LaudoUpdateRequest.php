<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaudoUpdateRequest extends FormRequest
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
    public function rules()
    {
        return [
            'laudo_id' => 'required',
            'status' => 'nullable',
            'dataConclusao' => 'nullable|date',
            'tecnicoResponsavel' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'laudo_id.required' => 'ID do laudo é obrigatório.',
            'laudo_id.exists' => 'Laudo não encontrado.',
            'status.exists' => 'Status inválido.',
            'dataConclusao.date' => 'A data de conclusão precisa ser uma data válida.',
            'tecnicoResponsavel.exists' => 'Técnico selecionado inválido.',
        ];
    }

}
