<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if (Auth::check()) {
            return [
                'data' => [
                    'type' => 'Users',
                    'attributes' => [
                        'name' => $this->name,
                        'email' => $this->email,
                    ],
                    'links' => [
                        'self' => route('users.show')
                    ]
                ]
            ];
        } else {
            return [
                'data' => [
                    'type' => 'Users',
                    'attributes' => [
                        'name' => $this->name,
                        'email' => $this->email,
                    ]
                ]
            ];
        }
    }

}
