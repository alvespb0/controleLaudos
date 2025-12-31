<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntegracaoRequest extends FormRequest
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
            'sistema'   => 'required|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'slug' => 'required|string|max:150|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
                    . '|unique:integracoes,slug,' . $id,
            'endpoint' => 'required|string|max:255|url',
            'auth' => 'required|string|in:basic,bearer,wss',
            'tipo' => 'required|string|in:soap,rest',

        ];
    }

    public function messages(): array
    {
        return [
            // sistema
            'sistema.required' => 'O campo sistema é obrigatório.',
            'sistema.string'   => 'O campo sistema deve ser um texto.',
            'sistema.max'      => 'O campo sistema deve ter no máximo 100 caracteres.',

            // descricao
            'descricao.string' => 'A descrição deve ser um texto.',
            'descricao.max'    => 'A descrição deve ter no máximo 255 caracteres.',

            // slug
            'slug.required' => 'O slug é obrigatório.',
            'slug.string'   => 'O slug deve ser um texto.',
            'slug.max'      => 'O slug deve ter no máximo 150 caracteres.',
            'slug.regex'   => 'O slug deve conter apenas letras minúsculas, números e hífen.',
            'slug.unique'  => 'Já existe uma integração cadastrada com esse slug.',

            // endpoint
            'endpoint.required' => 'O endpoint é obrigatório.',
            'endpoint.string'   => 'O endpoint deve ser um texto.',
            'endpoint.max'      => 'O endpoint deve ter no máximo 255 caracteres.',
            'endpoint.url'      => 'O endpoint deve ser uma URL válida.',

            // auth
            'auth.required' => 'O tipo de autenticação é obrigatório.',
            'auth.string'   => 'O tipo de autenticação deve ser um texto válido.',
            'auth.in'       => 'O tipo de autenticação deve ser basic, bearer ou wss.',

            // tipo
            'tipo.required' => 'O tipo da integração é obrigatório.',
            'tipo.string'   => 'O tipo da integração deve ser um texto válido.',
            'tipo.in'       => 'O tipo da integração deve ser soap ou rest.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug')) {
            $this->merge([
                'slug' => strtolower($this->slug),
            ]);
        }
    }

}
