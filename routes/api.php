<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->post('v1/series/{id}/comments','CommentController@store');
Route::middleware('auth:api')->get('v1/series/{id}/comments','CommentController@list');

Route::post('v1/signup','Auth\RegisterController@store');
Route::get('v1/users','UserController@show')->name('users.show');

Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('series', 'SerieController');
    Route::apiResource('packages','PackageController');
    Route::put('packages/{id}/series','PackageController@addSeries');
    Route::delete('packages/{id}/series','PackageController@removeSeries');
    Route::apiResource('series/comments','CommentController');
    Route::apiResource('comments','CommentController');
    Route::apiResource('users/:id/orders','OrderController');
});



