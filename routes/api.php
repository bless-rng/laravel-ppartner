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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix("/user")->group(function() {
    Route::post("/", 'UserController@create');
});

Route::prefix("/transaction")->group(function() {
    Route::get("/user/{user}", 'TransactionController@getByUser')->where('user', '\d+');
    Route::get("/",'TransactionController@getAll');
    Route::get("/date/{date}", 'TransactionController@getByDate')->where('date', '\d{4}\-\d{2}\-\d{2}');
    Route::post("/", 'TransactionController@create');
});
