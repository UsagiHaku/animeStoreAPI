<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSeriesRequest;
use App\Http\Requests\UpdateSeriesRequest;
use App\Http\Resources\PackageResource;
use App\Http\Resources\SerieResource;
use App\Serie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Info(title="API Series", version="1.0")
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Add bearer token to endpoints",
 *     name="Bearer Token",
 *     in="header",
 *     scheme="bearer",
 *     securityScheme="Bearer Auth",
 * )
 *
 * @OA\Server(url="http://localhost:8000")
 * @OA\Server(url="http://animeStore.test")
 */
class SerieController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/series",
     *      operationId="getSeries",
     *      tags={"Series"},
     *      summary="Obtener las series",
     *      description="Regresa la informacion de las series",
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
     * )
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json( SerieResource::collection(Serie::with('packages','comments')->get()));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/series",
     *     summary="Añadir una serie",
     *     description="Añade la informacion perteneciente a una serie",
     *      tags={"Series"},
     *     @OA\RequestBody(
     *
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */

    public function store(Request $request)
    {
        $serie = Serie::create($request->all());

        return response()->json(new SerieResource($serie), 201);
    }


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
     * @return Response
     */
    public function show($id)
    {
        $serie = Serie::with('packages', 'categories', 'comments', 'comments.user')->find($id);
        if (!$serie) {
            abort(404);
        }
        return response()->json(new SerieResource($serie));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/series/{series}/packages",
     *      tags={"Series"},
     *      summary="Obtener los paquetes de series",
     *      description="Regresa la informacion de los paquetes que tienen una serie en particular",
     *     @OA\RequestBody(
     *
     *     ),
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
     *          description="Serie no encontrada en los registros"
     *       ),
     * )
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getPackages($id){
        $serie = Serie::with('packages')->find($id);
        if(!$serie){
            abort(404);
        }
        return response()->json(PackageResource::collection($serie->packages()->get()),200);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/series/{series}",
     *     tags={"Series"},
     *     summary="Actualizar una serie",
     *     description="Regresa la informacion de una serie ya actualizada",
     *     @OA\RequestBody(
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Serie a actualizar no encontrada",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Operación exitosa",
     *     ),
     *  @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */
    public function update(UpdateSeriesRequest $request, $id)
    {
        $serie = Serie::find($id);
        if(!$serie){
            abort(404);
        }
        $serie->update($request->all());
        return response()->json(new SerieResource($serie), 200);
    }

    /**
     * @OA\Delete(
     *     path=" api/v1/series/{series}",
     *     summary="Elimina una serie",
     *     description="Elimina la información de una serie",
     *     tags={"Series"},
     *     @OA\Response(
     *         response=204,
     *         description="Serie destruida exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Serie no encontrada"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */
    public function destroy($id)
    {
        $serie = Serie::find($id);
        $serie->packages()->detach();
        $serie->categories()->detach();
        $serie->users()->detach();
        $serie->comments()->delete();
        Serie::destroy($id);
        return response(null, 204);
    }

    /**
     * @return Response
     */
    public function mySeries()
    {
        $mySeries = auth('api')->user()->series()->get();
        return response()->json(SerieResource::collection($mySeries), 200);
    }
}
