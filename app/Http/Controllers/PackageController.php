<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddSeriesRequest;
use App\Http\Requests\CreatePackageRequest;
use App\Http\Requests\RemoveSeriesRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Http\Resources\SerieResource;
use App\Package;
use App\Serie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class PackageController extends Controller
{
    /**
     * @OA\Get(
     *      path="/packages",
     *      tags={"Packages"},
     *      summary="Obtener los paquetes",
     *      description="Regresa la informacion de los paquetes",
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
        return response()->json(PackageResource::collection(Package:: with('series')->get()));
    }

    /**
     * @OA\Post(
     *     path="/packages",
     *     summary="Añadir un paquete",
     *     description="Añade la informacion perteneciente a un paquete",
     *      tags={"Packages"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Una o más series pertenecientes al paquete
     *          no se encuentran o el paquete mismo no se encuentra"
     *     ),
     *       @OA\Response(
     *         response=400,
     *         description="No hay series en el paquete"
     *     ),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */
    public function store(CreatePackageRequest $request)
    {
        $package = new Package($request->all());
        $package->save();

        $series = $request->get('series');

        if($series == null || empty($series)){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        $allSeriesExist= true;

        foreach($series as $serie){
            if(!(array_key_exists("id", $serie) && Serie::find($serie["id"]))){
                $allSeriesExist = false;
                break;
            }
        }

        if($allSeriesExist){
            foreach($series as $serie){
                $newSerie = Serie::find($serie["id"]);
                $newSerie->save();
                $package->series()->attach($newSerie);
            }
        }else{
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        return response()->json(new PackageResource($package), 201);
    }

    /**
     * @OA\Get(
     *      path="/packages/{packages}",
     *      tags={"Packages"},
     *      summary="Obtener un paquete",
     *      description="Regresa la informacion de un paquete",
     *      @OA\Parameter(
     *          name="packages",
     *          description="Paquete id",
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
        $package = Package::with('series')->find($id);
        if(!$package){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }
        return response()->json(new PackageResource($package),200);
    }

    /**
     * @OA\Put(
     *     path="/packages/{packages}",
     *     tags={"Packages"},
     *     summary="Actualizar un paquete",
     *     description="Regresa la informacion de un paquete ya actualizada",
     *      @OA\Parameter(
     *          name="packages",
     *          description="Paquete id",
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
     *         response=404,
     *         description="Paquete a actualizar no encontrada",
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
    public function update(UpdatePackageRequest $request, $id){
        $package = Package::find($id);
        if(!$package){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }
        $package->update($request->all());
        return response()->json(new PackageResource($package), 200);
    }

    /**
     * @OA\Put(
     *     path="/packages/{packages}/series",
     *     tags={"Packages"},
     *     summary="Actualizar las series que tiene un paquete",
     *     description="Regresa la información de un paquete,
     *      con sus series ya actualizadas, de manera que se añaden series al paquete",
     *      @OA\Parameter(
     *          name="packages",
     *          description="Paquete id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Paquete a actualizar no encontrado o una o más series a añadir no encontradas",
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
    public function addSeries(Request $request, $id){
        $package = Package::with('series')->find($id);
        if(!$package){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        $series = $request->get('series');
        $allSeriesExist = true;
        foreach($series as $serie){
            if(!(array_key_exists("id", $serie) && Serie::find($serie["id"]))){
                $allSeriesExist = false;
                break;
            }
        }

        if(!$allSeriesExist){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        foreach($series as $serie){
            $newSerie = Serie::find($serie["id"]);
            $newSerie->save();
            $package->series()->attach($newSerie);
        }

        return response()->json(new PackageResource(Package::with('series')->find($id)), 200);
    }

    /**
     * @OA\Delete(
     *     path="/packages/{packages}/series",
     *     summary="Actualizar las series que tiene un paquete",
     *     description="Regresa la información de un paquete,
     *      con sus series ya actualizadas, de manera que se eliminan series al paquete",
     *     tags={"Packages"},
     *      @OA\Parameter(
     *          name="packages",
     *          description="Paquete id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(
     *         response=204,
     *         description="Serie destruida exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Una o más series no encontradas o paquete no encontrado"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */
    public function removeSeries(Request $request, $id){
        $package = Package::with('series')->find($id);
        if(!$package){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        $series = $request->get('series');
        $allSeriesExist = true;
        foreach($series as $serie){
            if(!(array_key_exists("id", $serie) && Serie::find($serie["id"]))){
                $allSeriesExist = false;
                break;
            }
        }

        if($allSeriesExist){
            foreach($series as $serie){
                $newSerie = Serie::find($serie["id"]);
                $newSerie->save();
                $package->series()->detach($newSerie);
            }
        }else{
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }
        return response(null, 204);
    }

    /**
     * @OA\Get(
     *      path="/packages/{packages}/series",
     *      tags={"Packages"},
     *      summary="Obtener las series pertenecientes a un paquete",
     *      description="Regresa la informacion de las series pertenecientes a un paquete en particular",
     *      @OA\Parameter(
     *          name="packages",
     *          description="Paquete id",
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
     *          description="Paquete no encontrada en los registros"
     *       ),
     * )
     *
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getPackageSeries($id){
        $package = Package::with('series')->find($id);
        if(!$package){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }
        return response()->json(SerieResource::collection($package->series()->get()),200);
    }

    /**
     * @OA\Delete(
     *     path="/packages/{packages}",
     *     summary="Elimina un paquete",
     *     description="Elimina la información de un paquete",
     *     tags={"Packages"},
     *      @OA\Parameter(
     *          name="packages",
     *          description="Paquete id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Response(
     *         response=204,
     *         description="Paquete destruido exitosamente"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Paquete no encontrado"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *      security={
     *         {
     *             "Bearer Auth": {"write:projects", "read:projects"}
     *         }
     *     },
     * )
     */
    public function destroy($id){
        $package = Package::find($id);
        if(!$package){
            return response()->json([
                "errors" => [
                    "code" => "ERROR-2",
                    "title" => "Resource not found",
                    "message" => "No se encontro un recurso"
                ]
            ], 404);
        }

        $package->series()->detach();
        Package::destroy($id);

        return response(null, 204);
    }

}
