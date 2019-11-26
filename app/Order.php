<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['id','total','card_id','delivery_date'];

    public function orderItems(){
        return $this->hasMany('App\OrderItem');
    }
}
