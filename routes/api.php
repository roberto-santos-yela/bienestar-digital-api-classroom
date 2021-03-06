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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
    //return $request->user();
//});

Route::post('/create_user', 'UserController@store');
Route::post('/user_login', 'UserController@user_login');
Route::post('/recover_user_password', 'UserController@recover_user_password');

Route::group(['middleware' => ['auth']], function () {
    
    Route::get('/get_user_data', 'UserController@get_user_data');
    Route::post('/change_user_password', 'UserController@change_user_password');
    Route::post('/create_restriction/{id}', 'UserController@create_restriction');
    Route::post('/send_notification_email', 'UserController@send_notification_email');
            
    Route::apiResource('/app', 'AppController'); 
    Route::post('/store_apps_list', 'AppController@store_apps_list');
    Route::post('/store_apps_data', 'AppController@store_apps_data');      
    Route::get('/get_app_total_usage_time/{id}', 'AppController@get_app_total_usage_time');
    Route::get('/get_apps_statistics', 'AppController@get_apps_statistics');
    Route::get('/get_apps_restrictions', 'AppController@get_apps_restrictions');
    Route::get('/total_usage_time_per_day/{id}', 'AppController@total_usage_time_per_day');  
    Route::get('/get_apps_coordinates', 'AppController@get_apps_coordinates');
});


