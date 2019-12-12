<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePackageRequest extends FormRequest
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
            "title" => ['string', 'max:255'],
            "description" => ['string', 'max:255'],
            "image" => ['string','URL', 'max:255'],
            "price" => ['numeric','gt:0']
        ];
    }

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
