<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ListCommentResource;
use App\Serie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * @OA\Get(
     *      path="/series/{serie}/comments",
     *      tags={"Comments"},
     *      summary="Obtener los comentarios pertenecientes a una serie ",
     *      description="Regresa la informacion de todos los comentarios pertenecientes a una serie en particular",
     *     @OA\Parameter(
     *          name="serie",
     *          description="Serie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Operacion exitosa"
     *       ),
     *     @OA\Response(
     *         response=404,
     *         description="Una o más series pertenecientes al paquete
     *          no se encuentran o el paquete mismo no se encuentra"
     *     ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {
        $serie = Serie::with('comments')->find($id);
        if(!$serie){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }
        return response()->json($serie->comments()->get(),200);
    }

    /**
     * @OA\Post(
     *      path="/series/{serie}/comments",
     *      summary="Añadir un comentario",
     *      description="Añade la informacion perteneciente a un comentario en una serie en particular",
     *      tags={"Comments"},
     *      @OA\Parameter(
     *          name="serie",
     *          description="Serie id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="La serie no se encuentra"
     *     ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     * @param $id
     * @param CreateCommentRequest $request
     * @return CommentResource
     */
    public function store($id, CreateCommentRequest $request)
    {
        $serie = Serie::with('comments')->find($id);
        if(!$serie){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        $comment = new Comment($request->all());
        $comment->user()->associate(auth('api')->user());
        $serie->comments()->save($comment);
        return new CommentResource($comment);
        //return response()->json($comment, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

}
