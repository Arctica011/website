<?php

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

Route::get('/auth/csfr', "Api\AuthController@csfr");
Route::post('/auth/token', "Api\AuthController@token");
Route::post('/auth/login', "Api\AuthController@login");
Route::post('/auth/logout', "Api\AuthController@Logout");

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user', '\App\Http\Controllers\Api\UserController');
});

