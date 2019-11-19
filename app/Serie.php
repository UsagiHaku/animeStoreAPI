<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = ['id','name','description','image'];

    public function packages(){
        return $this->belongsToMany('App\Package')->withTimestamps();
    }
}
