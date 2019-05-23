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

Route::post('login', 'API\PassportController@login');
Route::delete('logout', 'API\PassportController@logout');
Route::post('registration', 'API\PassportController@registration');

Route::group(['middleware' => 'auth:api'], function(){
    Route::post('user-info', 'API\PassportController@userInfo');
});
