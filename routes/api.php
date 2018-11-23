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



Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::group(['middleware' => 'auth:api'], function (){
    Route::post('createhotel', 'API\UserController@createHotel');
    Route::get('gethotel', 'API\UserController@getHotel');
    Route::get('gethotelbyid', 'API\UserController@getHotelById');
    Route::post('createroom', 'API\UserController@createRoom');
    Route::get('getroomsbyhotelid', 'API\UserController@getRoomsByHotelId');
    Route::get('reserveroom', 'API\UserController@reserveRoom');
    Route::get('releaseroom', 'API\UserController@releaseRoom');
});

