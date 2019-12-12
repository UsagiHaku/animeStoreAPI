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
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateSeriesRequest $request)
    {
        $serie = Serie::create($request->all());

        return response()->json(new SerieResource($serie), 201);
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
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

    public function getPackages($id){
        $serie = Serie::with('packages')->find($id);
        if(!$serie){
            abort(404);
        }
        return response()->json(PackageResource::collection($serie->packages()->get()),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UpdateSeriesRequest $request, $id)
    {
        $serie = Serie::find($id);
        $serie->update($request->all());
        return response()->json(new SerieResource($serie), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
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
