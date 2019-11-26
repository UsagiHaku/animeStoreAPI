<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['id','title','description','image','price'];

    public function series(){
        return $this->belongsToMany('App\Serie');
    }

    public function orderItems(){
        return $this->hasMany('App\OrderItem');
    }
}
