<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutentiqueRequest extends FormRequest
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
            'documento' => 'required|file|mimes:pdf|max:10240', // max 10MB
            'nome_documento' => 'required|string|max:255',
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email'
        ];
    }

    public function messages()
    {
        return [
            'documento.required' => 'O envio do documento em PDF é obrigatório.',
            'documento.file' => 'O documento deve ser um arquivo válido.',
            'documento.mimes' => 'O documento deve estar no formato PDF.',
            'documento.max' => 'O documento não pode ultrapassar 10MB.',

            'nome_documento.required' => 'O nome do documento é obrigatório.',
            'nome_documento.string' => 'O nome do documento deve ser um texto.',
            'nome_documento.max' => 'O nome do documento não pode ultrapassar 255 caracteres.',

            'emails.required' => 'É necessário informar pelo menos um e-mail para assinatura.',
            'emails.array' => 'Os e-mails devem ser enviados em formato de lista.',
            'emails.min' => 'Informe ao menos um e-mail de signatário.',

            'emails.*.required' => 'Todos os campos de e-mail são obrigatórios.',
            'emails.*.email' => 'Um ou mais endereços de e-mail são inválidos.',
        ];
    }

}
