<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\App;
use App\UserHasApp;

class AppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $app = new App();
        $app->logo = $request->logo;
        $app->name = $request->name;
        $app->save();

        return response()->json([
                
            "message" => "new app stored"

        ], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //INTRODUCIR LISTA DE APLICACIONES// //TERMINADO//
    public function store_apps_list()
    {        
        $array_csv = array_map('str_getcsv', file('/Users/alumnos/Desktop/CSV_BIENESTAR_DIGITAL/app_info.csv')); 
        
        foreach ($array_csv as $key => $line) {

            if($key != 0)
            {
                $app = new App();
                $app->logo = $line[0];
                $app->name = $line[1];
                $app->save();

            }     
                       
        }
  
        return response()->json([
                
            "message" => "all apps stored"

        ], 200);

    }

    ///BETA///
    public function get_app_details(Request $request, $id)
    {
        $request_user = $request->user;
        $user_apps = $request_user->apps;
        $app = $user_apps->where('id', $id)->first(); 


        var_dump($id); exit;              


        return response()->json([

            //"total_usage_time" => $user_apps,
            "jeilo" => $app,

        ], 200);

    }

    ///FALTA POR TERMINAR//
    public function get_app_statistics(Request $request)
    {
        $request_user = $request->user;
        $apps = $request_user->apps;
        $primera_app = $apps[1]->pivot->total_usage_time;
        $primera_app_name = $apps[1]->name;

        return response()->json([

            "name" => $primera_app_name,
            "total_usage_time" => $primera_app,
            "array_pruebea" => $array_prueba,

        ]);

    }

    ///NECESITA CORRECCIÓN//
    public function get_app_coordinates(Request $request, $app_id, $app_date)
    {
        $request_user = $request->user;
        $app = $request_user->apps->where('id', '=', $app_id)->first();
        $pivot = $app->pivot->where('date', '<=', $app_date)->first();
        
        return response()->json([
           
            "latitude" => $pivot->latitude,
            "longitude" => $pivot->longitude,                         

        ], 200);

    }

}
