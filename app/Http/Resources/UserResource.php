<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'Users',
                'attributes' => [
                    'id' => $this->id,
                    'name' => $this->name,
                    'email' => $this->email,
                    'api_token' => $this->api_token
                ]
            ]
        ];
    }

}
