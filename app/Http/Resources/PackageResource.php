<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{


    public function toArray($request)
    {
        $series_id = [];
        foreach ($this->series as $serie){
            array_push($series_id,$serie->id);
        }
        $data = [
            'data' => [
                'type' => 'Packages',
                'id' => $this->id,
                'attributes' => [
                    'title' => $this->title,
                    'description' => $this->description,
                    'image' => $this->image,
                    'price' => $this->price,
                ],
                'series'=> $series_id,
                'links' => [
                    'self' => url("/packages/{$this->id}")
                ]
            ]
        ];

        return $data;
    }
}