<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['id'];

    public function orders(){
        return $this->belongsTo('App\Order', 'order_id');
    }

    public function packages(){
        return $this->belongsTo('App\Package', 'package_id');
    }
}
