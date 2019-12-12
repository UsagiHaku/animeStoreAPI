<?php

namespace App\Http\Controllers;

use App\Http\Resources\PackageResource;
use App\Http\Resources\SerieResource;
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
        return response()->json(PackageResource::collection(Package:: with('series')->get()));
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

        return response()->json(new PackageResource($package), 201);
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
        return response()->json(new PackageResource($package),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id){
        $package = Package::find($id);
        if(!$package){
            abort(404);
        }
        $package->update($request->all());
        return response()->json(new PackageResource($package), 200);
    }

    public function addSeries(Request $request, $id){
        $package = Package::with('series')->find($id);
        if(!$package){
            abort(404);
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
            abort(404);

        }

        foreach($series as $serie){
            $newSerie = Serie::find($serie["id"]);
            $newSerie->save();
            $package->series()->attach($newSerie);
        }

        return response()->json(new PackageResource(Package::with('series')->find($id)), 200);
    }

    public function removeSeries(Request $request, $id){
        $package = Package::with('series')->find($id);
        if(!$package){
            abort(404);
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
            abort(404);
        }
        return response(null, 204);
    }

    public function getPackageSeries($id){
        $package = Package::with('series')->find($id);
        if(!$package){
            abort(404);
        }
        return response()->json(SerieResource::collection($package->series()->get()),200);
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
