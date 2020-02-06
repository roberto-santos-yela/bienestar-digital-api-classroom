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

///PPRUEBA
Route::get('/generate_password', 'UserController@generate_password');

//PRUEBAS


Route::group(['middleware' => ['auth']], function () {

    
    Route::get('/get_user_data', 'UserController@get_user_data');
    Route::post('/create_restriction/{id}', 'UserController@create_restriction');
    Route::post('/change_user_password', 'UserController@change_user_password');
    Route::get('/get_time_diff/{id}', 'UserController@get_time_diff');
    Route::get('/daily_usage_time/{id}', 'UserController@daily_usage_time');
   

    Route::post('/send_notification_email', 'UserController@send_notification_email');
        
    Route::apiResource('/app', 'AppController'); 
    
    
    Route::get('/get_apps_data', 'AppController@get_apps_data');  
   
    Route::get('/total_usage_time/{id}', 'AppController@total_usage_time');
    Route::get('/total_usage_time_beta/{id}', 'AppController@total_usage_time_beta');  

    Route::get('/get_app_total_usage_time/{id}', 'AppController@get_app_total_usage_time');    
    Route::get('/total_usage_time_per_day/{id}', 'AppController@total_usage_time_per_day');        

    Route::post('/store_apps_list', 'AppController@store_apps_list');
    Route::post('/store_apps_data', 'AppController@store_apps_data');  

    Route::get('/get_app_details', 'AppController@get_app_details');
    Route::get('/get_app_statistics', 'AppController@get_app_statistics');
    Route::get('/get_apps_coordinates', 'AppController@get_apps_coordinates');

    Route::get('/get_apps_restrictions', 'AppController@get_apps_restrictions');
    
    Route::get('/get_apps_usage_range/{date}', 'AppController@get_apps_usage_range');

    
     
    Route::get('/get_apps_statistics', 'AppController@get_apps_statistics');


    Route::get('/prueba', 'AppController@prueba');
    
     



});


