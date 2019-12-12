<?php


namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SerieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $packages_id = [];
        foreach ($this->packages as $package){
            array_push($packages_id,$package->id);
        }
        $data = [
            'data' => [
                'type' => 'Series',
                'id' => $this->id,
                'attributes' => [
                    'name' => $this->name,
                    'description' => $this->description,
                    'image' => $this->image
                ],
                'packages'=> $packages_id,
                //'comments'=> CommentResource::collection($this->comments),
                'links' => [
                    'self' => url("/series/{$this->id}")
                ]
            ]
        ];

        return $data;
    }

}