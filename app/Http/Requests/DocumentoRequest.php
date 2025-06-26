<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoRequest extends FormRequest
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
            'tipo_documento' => 'required|in:CAT,PPP,ADENDO',
            'descricao' => 'required|string|max:255',
            'data_elaboracao' => 'required|date',
            'cliente_id' => 'required|exists:cliente,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required' => 'O campo Tipo de Documento é obrigatório.',
            'tipo_documento.in' => 'O Tipo de Documento selecionado é inválido. Escolha entre CAT, PPP ou ADENDO.',

            'descricao.required' => 'O campo Descrição é obrigatório.',
            'descricao.string' => 'A Descrição deve ser um texto válido.',
            'descricao.max' => 'A Descrição pode ter no máximo :max caracteres.',

            'data_elaboracao.required' => 'O campo Data de Elaboração é obrigatório.',
            'data_elaboracao.date' => 'A Data de Elaboração deve ser uma data válida.',

            'cliente_id.required' => 'O campo Cliente é obrigatório.',
            'cliente_id.exists' => 'O Cliente selecionado é inválido.',
        ];
    }


}
