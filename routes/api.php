<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::apiResource('series', 'SerieController');
        Route::apiResource('packages','PackageController');
        Route::put('packages/{id}/series','PackageController@addSeries');
        Route::delete('packages/{id}/series','PackageController@removeSeries');
        Route::apiResource('orders','OrderController');
        Route::get('packages/{id}/series','PackageController@getPackageSeries');
        Route::get('packages/{id_package}/series/{id_serie}','PackageController@getSerieOfPackage');
        Route::apiResource('series/{id}/comments', 'CommentController');
        Route::get('series/{id}/packages', 'SerieController@getPackages');
        Route::get('/user/series', 'SerieController@mySeries');
        Route::apiResource('series/comments','CommentController');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::get('me', 'AuthController@me');
    });

    Route::post('signup', 'AuthController@signup');
    Route::post('login', 'AuthController@login');

});
