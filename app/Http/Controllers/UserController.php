<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{
    public function show()
    {
        if(Auth::check())
        {
            $id = Auth::user()->id;
            $user = User::findOrFail($id);
            return new UserResource($user);
        }
        else{
            return [
              'Can not find user'
            ];
        }

    }
}
