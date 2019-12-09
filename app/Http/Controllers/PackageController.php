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
        $series = $request->get('series');

        if(!$package || $series == null || empty($series)){
            response()->json(null,404);
        }

        foreach ($series as $serie){
            if(!(array_key_exists("id", $serie) &&
                Serie::find($serie["id"]))
            ){
                response()->json(null, 404);
            }
        }

        $package->fill($request->all())->save();
        $packageSeries = $package->series()->get();
        //checo en busca de series que no estén en el paquete y las añado
        $addSerie = true;
        foreach($series as $serie){

            foreach ($packageSeries as $packageSerie){
                $serieTemp = new Serie($serie);
                if($serieTemp->id ==
                    $packageSerie->id) {
                    $addSerie = false;
                }
           }

            print($serie["name"]);
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
        $package->save();

        return response()->json($package, 200);
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

        $package->series()->detach();
        Package::destroy($id);

        return response(null, 204);
    }

}
