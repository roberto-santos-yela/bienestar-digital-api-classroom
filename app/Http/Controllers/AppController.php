<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
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
        $apps = App::select('id','name','logo')->get();  
        
        return response()->json(

            $apps

        , 200);
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

    //INTRODUCIR LISTA DE APLICACIONES// 
    //POR TERMINAR// //FALTA HACER QUE NO SE REPITA Y QUE SE PASE POR CSV A LA APLICACIÓN//
    public function store_apps_list(Request $request)
    {        
        //$array_csv = array_map('str_getcsv', file('/Applications/MAMP/htdocs/bienestar-digital-api-classroom/csv_files/apps_list.csv')); 
        //$array_csv = array_map('str_getcsv', file('D:\Programas\xampp\htdocs\bienestar-digital-api-classroom\csv_files\apps_list.csv')); 
        
        $lines = explode(PHP_EOL, $request->csv);
        
        $array_csv = [];
        
        foreach ($lines as $line) {
            
            $array_csv[] = str_getcsv($line);
        
        }

        foreach ($array_csv as $key => $line) {

            if($key != 0)
            {
                $app = new App();
                $app->logo = $line[0];
                $app->name = $line[1];
                $app->save();

            }     
                       
        }

        $apps = App::select('id','name','logo')->get();        

        return response()->json(

            $apps                      

        , 200);

    }

    //PETICIÓN QUE INTRODUCE LOS DATOS DE USO DE LAS APLICACIONES MEDIANTE UN CSV Y LOS VINCULA CON EL USUARIO// 
    //POR TERMINAR// //FALTA INTRODUCIR MEDIANTE UN CSV DE LA APLICACIÓN//   
    public function store_apps_data(Request $request)
    {
        $request_user = $request->user; 
        //$array_csv = array_map('str_getcsv', file('D:\Programas\xampp\htdocs\bienestar-digital-api-classroom\csv_files\usage_dummy.csv'));

        $lines = explode(PHP_EOL, $request->csv);
        
        $array_csv = [];
        
        foreach ($lines as $line) {
            
            $array_csv[] = str_getcsv($line);
        
        }        

        foreach ($array_csv as $key => $line) {
                              
            if($key != 0)
            {
                $name = $line[1];             
                $app = App::where('name', '=', $name)->first();
                
                $request_user->apps()->attach($app->id, [

                    'date' => $line[0], 
                    'event' => $line[2],                      
                    'latitude' => $line[3],
                    'longitude' => $line[4],
        
                ]); 

            }
    
        }

    }

    //PRUEBA////GUARDARLO COMO ORO EN PANO//
    public function total_usage_time(Request $request, $id)
    {
        $request_user = $request->user;
        $app_entries = $request_user->apps()->wherePivot('app_id', $id)->whereDate('date', "=", '2019-11-19')->get();//ATENTO A LA DATE//    
        $app_entry = $request_user->apps()->wherePivot('app_id', $id)->first();   
        $app_entries_lenght = count($app_entries);
        $total_time_in_seconds = 0;

        if($app_entries[0]->pivot->event == "closes")
        {                                  
            $date_format = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[0]->pivot->date)->format('Y-m-d'); 
            $date_format_at_midnight = $date_format . ' 00:00:00';
            $date_from_midnight = Carbon::parse($date_format_at_midnight);
            $date_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[0]->pivot->date);
            $time_diff_from_midnight_in_seconds = $date_from_midnight->diffInSeconds($date_hour);
            $total_time_in_seconds = $total_time_in_seconds + $time_diff_from_midnight_in_seconds;            

            for ($x = 1; $x <= $app_entries_lenght - 1; $x++) {

                $have_both_hours = true;
    
                if($app_entries[$x]->pivot->event == "opens")
                {
                    $from_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date);                
                    $from_hour_format = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date)->format('Y-m-d'); 
                    $from_hour_format_to_midnight = $from_hour_format . ' 23:59:59';
                    $today_to_midnight = Carbon::parse($from_hour_format_to_midnight);
                    $time_diff_till_midnight = $from_hour->diffInSeconds($today_to_midnight);
                    $total_time_in_seconds = $total_time_in_seconds + $time_diff_till_midnight;                             
                    $have_both_hours = false;                
                    
                }else if($app_entries[$x]->pivot->event == "closes"){
    
                    $total_time_in_seconds = $total_time_in_seconds - $time_diff_till_midnight;
                    $to_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date);                        
                    $have_both_hours = true;
    
                }
                
                if($have_both_hours)
                {
                    $total_time_in_seconds += $from_hour->diffInSeconds($to_hour);
    
                }           
    
            }

        }else{

            for ($x = 0; $x <= $app_entries_lenght - 1; $x++) {

                $have_both_hours = true;
    
                if($app_entries[$x]->pivot->event == "opens")
                {
                    $from_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date);                
                    $from_hour_format = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date)->format('Y-m-d'); 
                    $from_hour_format_to_midnight = $from_hour_format . ' 23:59:59';
                    $today_to_midnight = Carbon::parse($from_hour_format_to_midnight);
                    $time_diff_till_midnight = $from_hour->diffInSeconds($today_to_midnight);
                    $total_time_in_seconds = $total_time_in_seconds + $time_diff_till_midnight;                             
                    $have_both_hours = false;                
                    
                }else if($app_entries[$x]->pivot->event == "closes"){
    
                    $total_time_in_seconds = $total_time_in_seconds - $time_diff_till_midnight;
                    $to_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date);                        
                    $have_both_hours = true;
    
                }
                
                if($have_both_hours)
                {
                    $total_time_in_seconds += $from_hour->diffInSeconds($to_hour);
    
                }           
    
            }

        }

        $total_usage_time = Carbon::createFromTimestampUTC($total_time_in_seconds)->toTimeString();

        return response()->json([

            "app_name" => $app_entry->name,
            "total_usage_time" => $total_usage_time,  

        ]);

    }

    //TIEMPO DE USO TOTAL// //HACE FALTA REFACTORIZAR//
    public function total_usage_time_beta(Request $request, $id)
    {
        $request_user = $request->user;       
        $app_entries = $request_user->apps_dates()->get()->groupBy('date_group');     
        $date = "2019-11-18";

                 
        $app_entry = $request_user->apps()->wherePivot('app_id', $id)->first();   
        $app_entries_lenght = count($app_entries[$date]);


        $dates = [];

        foreach ($app_entries as $key => $value) {
            

            array_push($dates, $value);
            echo $dates;
            
        }

        return response()->json(

            $app_entries


        , 200);
        
    }

    //TIEMPO TOTAL DE DIAS ANTERIORES// // CASI TERMINADO //
    public function total_usage_time_per_day(Request $request)
    {
        $request_user = $request->user;
        $app_entries = $request_user->apps_dates()->get()->groupBy('date_group'); 
        
        $collection = collect($app_entries);
        $keys = $collection->keys();

        $dates = [];
        $total_usage_times = [];
        $dates = $keys->all();
     
        foreach ($dates as $date)
        {
            $app_entries_lenght = count($app_entries[$date]); 
            $total_time_in_seconds = 0;

            for ($x = 0; $x <= $app_entries_lenght - 1; $x++) {

                $have_both_hours = false;
    
                if($app_entries[$date][$x]->pivot->event == "opens")
                {
                    $from_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$date][$x]->date);
                    
                }else{
    
                    $to_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$date][$x]->date);                              
                    
                    $have_both_hours = true;
    
                }
    
                if($have_both_hours)
                {
                    $total_time_in_seconds += $from_hour->diffInSeconds($to_hour); 
                    $total_usage_time = Carbon::createFromTimestampUTC($total_time_in_seconds)->toTimeString();                   
                    array_push($total_usage_times, $total_usage_time);
                }
                
            }

        }

        $dates_and_total_usage_times = array_combine($dates, $total_usage_times);

        return response()->json(
            
            $dates_and_total_usage_times
 
        , 200);

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
        //$app = $request_user->apps->where('id', '=', $app_id)->get();
        $apps = ['Reloj', 'Instagram'];
        $keys = ['name', 'latitude', 'longitude'];
        $data = []; 

        foreach ($apps as $app_name)
        {

            $app = $request_user->apps_coordinates()->where('name', '=', $app_name)
                                                    ->where("date", "<=", "2019-11-28 23:40:10")
                                                    ->latest('date')
                                                    ->first();
            

            array_push($data, $app->name, $app->latitude, $app->longitude);
            $latitude_longitude = array_combine($keys, $data);

        };
        
        $app = $request_user->apps_coordinates()->where("date", "<=", "2019-11-28 23:40:10")->latest('date')->first();
        
        //$apps_prueba = $apps->pivot->select("event")->get();       
        //$pivot = $app->pivot->where('date', '<=', $app_date)->first();

        return response()->json([

            $latitude_longitude
            
            //"latitude" => $app->latitude,
            //"longitude" => $app->longitude,                         

        ], 200);

    }

}
