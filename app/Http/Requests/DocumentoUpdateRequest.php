<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoUpdateRequest extends FormRequest
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
            'documento_id' => 'required',
            'status' => 'nullable',
            'dataConclusao' => 'nullable|date',
            'tecnicoResponsavel' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'documento_id.required' => 'ID do laudo é obrigatório.',
            #'documento_id.exists' => 'Laudo não encontrado.',

            'status.exists' => 'Status inválido.',

            'dataConclusao.date' => 'A data de conclusão precisa ser uma data válida.',
            
            #'tecnicoResponsavel.exists' => 'Técnico selecionado inválido.',
        ];
    }

}
