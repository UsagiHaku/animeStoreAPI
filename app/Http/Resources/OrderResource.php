<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        //$packages_id = [];
        //foreach ($this->orderItems as $orderItem){
          //  array_push($packages_id,$orderItem->packages->package);
        //}
        $data = [
            'data' => [
                'type' => 'Order',
                'id' => $this->id,
                'attributes' => [
                    'total' => $this->total,
                    'user card' => $this->card_id,
                    'user' => $this->user_id,
                    'delivery date' => $this->delivery_date,
                    'create date' => $this->created_at
                ],
                //'packages'=> $packages_id,
                'links' => [
                    'self' => url("me/orders/{$this->id}")
                ]
            ]
        ];

        return $data;
    }

}