<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserCollection;
use App\Models\User;
use App\Http\Resources\UserResource;

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
Route::post('users','App\Http\Controllers\UserController@register');
Route::post('users/login','App\Http\Controllers\UserController@login');

Route::post('password/email','App\Http\Controllers\ResetPasswordController@forgot');
Route::post('password/reset','App\Http\Controllers\ResetPasswordController@reset');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->group(function (){
    Route::put('users/{user}','App\Http\Controllers\UserController@update');
    Route::get('users/{user}','App\Http\Controllers\UserController@getUserdata');
});
Route::get('users','App\Http\Controllers\UserController@getAllUsers');
