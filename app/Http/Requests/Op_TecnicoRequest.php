<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Op_TecnicoRequest extends FormRequest
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
            'usuario' => 'required|string|min:5|max:255',
            'email' => 'required|string|min:5|max:255',
            'password'=> 'required|string|min:8|max:32',
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.required' => 'O campo usuário é obrigatório.',
            'usuario.string'   => 'O campo usuário deve ser um texto.',
            'usuario.min'      => 'O campo usuário deve ter no mínimo :min caracteres.',
            'usuario.max'      => 'O campo usuário deve ter no máximo :max caracteres.',

            'email.required' => 'O campo e-mail é obrigatório.',
            'email.string'   => 'O campo e-mail deve ser um texto.',
            'email.email'    => 'Informe um e-mail válido.',
            'email.min'      => 'O campo e-mail deve ter no mínimo :min caracteres.',
            'email.max'      => 'O campo e-mail deve ter no máximo :max caracteres.',

            'password.required'   => 'O campo senha é obrigatório.',
            'password.string'     => 'O campo senha deve ser um texto.',
            'password.min'        => 'A senha deve ter no mínimo :min caracteres.',
            'password.max'        => 'A senha deve ter no máximo :max caracteres.',
            'password.confirmed'  => 'A confirmação da senha não confere.',
        ];
    }

}
