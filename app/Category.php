<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['id','title'];

    public function series(){
        return $this->belongsToMany('App\Serie');
    }
}
