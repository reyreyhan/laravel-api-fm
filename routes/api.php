<?php

use Illuminate\Http\Request;

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

Route::post('register', 'API\UserController@register');
Route::post('login', 'API\UserController@login');
Route::get('user', 'API\UserController@getAuthenticatedUser')->middleware('jwt.verify');
Route::post('user', 'API\UserController@update')->middleware('jwt.verify');
Route::post('user/absent', 'API\UserController@absent')->middleware('jwt.verify');
Route::get('user/absent', 'API\UserController@getAbsent')->middleware('jwt.verify');
