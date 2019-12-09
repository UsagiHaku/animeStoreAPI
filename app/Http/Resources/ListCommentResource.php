<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ListCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $user_id = $this->user_id;
        $user = User::findOrFail($user_id);
        $user_name = $user->name;

        return [
            'type' => 'Comments',
            'attributes' => [
                'description' => $this->description,
                'made by' => $user_name
            ]
        ];
    }
}
