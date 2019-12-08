<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
/*
    public function messages()
    {
        return [
            'name.required' => 'El :attribute es obligatorio',
            'name.string'=> 'El :attribute debe ser una cadena de texto',
            'email.required' => 'El :attribute es obligatorio',
            'email.string' => 'El :attribute debe ser una cadena de texto',
            'email.email' => 'El :attribute debe tener el formato correcto',
            'email.unique' => 'Ya existe un usuario registrado con ese :attribute',
            'password.required' => 'La :attribute es obligatoria',
            'password.string' => 'La :attribute debe ser una cadena de texto',
            'password.min' => 'La :attribute debe tener por lo menos 8 caracteres'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña'
        ];
    }
*/

    /**
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "errors" => [
                "code" => "ERROR-1",
                "title" => "Unprocessable Entity",
                "message" => "Un atributo enviado no es correcto"
            ]
        ], 422));
    }
}
