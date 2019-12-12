<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/v1/series/{series}",
     *      operationId="getSerie",
     *      tags={"Series"},
     *      summary="Obtener una serie",
     *      description="Regresa la informacion de una serie",
     *      @OA\Response(
     *          response=200,
     *          description="Operacion exitosa"
     *       ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     *     @OA\Response(
     *          response=404,
     *          description="Serie no encontrada"
     *       )
     * )
     *
     * Display a listing of the resource.
     *
     */

    public function show()
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        return new UserResource($user);
    }
}
