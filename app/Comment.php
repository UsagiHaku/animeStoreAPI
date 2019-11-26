<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['id','description'];

    function user(){
        return $this->belongsTo('App\User');
    }
}
