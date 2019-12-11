<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['id'];

    public function order(){
        return $this->belongsTo('App\Order');
    }

    public function package(){
        return $this->belongsTo('App\Package');
    }
}
