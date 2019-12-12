<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{

    public function show()
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        return new UserResource($user);
    }
}
