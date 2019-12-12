<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    public function toArray($request)
    {
        switch($request) {
            case "ERROR-1": {
                return [
                    "errors" => [
                        "code" => "ERROR-1",
                        "title" => "Unprocessable Entity",
                        "message" => "Un atributo enviado no es correcto"
                    ]
                ];
            }
        }
    }
}
