<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'data' => [
                'type' => 'Comments',
                'id' => $this->id,
                'attributes' => [
                    'description' => $this->description,
                    'user'=>$this->user_id,
                    'serie'=>$this->serie_id
                ],
                'links' => [
                    'self' => url("/comments/{$this->id}")
                ]
            ]
        ];

        return $data;
    }

    // public function toArray($request)
    //    {
    //        $serie_id = $this->serie_id;
    //        $serie = Serie::findOrFail($serie_id);
    //        $serie_name = $serie->name;
    //
    //        $user_id = $this->user_id;
    //        $user = User::findOrFail($user_id);
    //        $user_name = $user->name;
    //
    //        return [
    //            'data' => [
    //                'type' => 'Comments',
    //                'attributes' => [
    //                    'description' => $this->description,
    //                    'serie' => $serie_name,
    //                    'made by' => $user_name
    //                ]
    //            ]
    //        ];
    //    }

}
