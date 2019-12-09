<?php

namespace App\Http\Controllers;

use App\Package;
use App\Serie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return response()->json(Package::with('series')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $package = new Package($request->all());
        $package->save();

        $series = $request->get('series');

        if($series == null || empty($series)){
            abort(400);
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
            abort(404);
        }

        return response()->json($package, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $package = Package::with('series')->find($id);
        if(!$package){
            abort(404);
        }
        return response()->json($package);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $package = Package::with('series')->find($id);
        if(!$package){
            abort(404);
        }
        $package->fill($request->all())->save();

        $series = $request->get('series');

        if($series == null || empty($series)){
            abort(400);
        }

        $packageSeries = $package->series()->get();

        //checo en busca de series que no estén en el paquete y las añado
        $addSerie = true;
        foreach($series as $serie){
            if(array_key_exists("id", $serie) && Serie::find($serie["id"])){
                foreach ($packageSeries as $packageSerie){
                   if($serie["id"] == $packageSerie["id"]){
                       $addSerie = false;
                       break;
                   }

               }
            }
            if($addSerie == true){
                $newSerie = Serie::find($serie["id"]);
                $newSerie->save();
                $package->series()->attach($newSerie);
            }
            $addSerie = true;
        }

        $removeSerie = true;
        $packageSeries= $package->series()->get();

        //checo en busca de series que estén en el paquete y pero no en las series recibidas y las elimino
        foreach($packageSeries as $packageSerie){
            foreach ($series as $serie){
                if($packageSerie["id"] == $serie["id"]){
                    $removeSerie = false;
                    break;
                }

            }
            if($removeSerie == true){
                $removedSerie = Serie::find($packageSerie["id"]);
                $removedSerie->save();
                $package->series()->detach($removedSerie);
            }
            $removeSerie = true;
        }

        return response()->json($package, 201);
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return Response
     */
    public function destroy($id){
        $package = Package::find($id);
        if(!$package){
            abort(404);
        }

        $packageDestroyed = Package::destroy($id);
        if(!$packageDestroyed) {
            abort(404);
        }
        return response(null, 204);
    }

}
