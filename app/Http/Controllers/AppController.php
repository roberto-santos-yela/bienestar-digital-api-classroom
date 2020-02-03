<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\App;
use App\UserHasApp;
use App\Helpers\AppTimeCalculator;
use App\Helpers\AppTimeStorage;
use Illuminate\Support\Facades\DB;

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
    
    public function store_apps_list(Request $request)
    {        
        //$array_csv = array_map('str_getcsv', file('/Applications/MAMP/htdocs/bienestar-digital-api-classroom/csv_files/apps_list.csv')); 
        //$array_csv = array_map('str_getcsv', file('D:\Programas\xampp\htdocs\bienestar-digital-api-classroom\csv_files\apps_list.csv')); 
        //MAC//$lines = explode(PHP_EOL, $request->csv);
        //PC//$lines = explode("\n", $request->csv);
        
        $lines = explode("\n", $request->csv);
        
        $array_csv = [];
        
        foreach ($lines as $line) {
            
            $array_csv[] = str_getcsv($line);
        
        }        

        foreach ($array_csv as $key => $line) {           

            if($key != 0)
            {
                $is_app_repeated = App::where('name', '=', $line[1])->first();
                
                if($is_app_repeated == null){

                    $app = new App();
                    $app->logo = $line[0];               
                    $app->name = $line[1];                
                    $app->save();

                }

            }     
                       
        }

        $apps = App::select('id','name','logo')->get();        

        return response()->json(

            $apps                 

        , 200);

    }
   
    public function store_apps_data(Request $request)
    {
        //$array_csv = array_map('str_getcsv', file('D:\Programas\xampp\htdocs\bienestar-digital-api-classroom\csv_files\usage_dummy.csv'));
        //MAC//$lines = explode(PHP_EOL, $request->csv);
        //PC//$lines = explode("\n", $request->csv);
        $request_user = $request->user; 
        
        $lines = explode("\n", $request->csv);
                
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
    
    public function get_app_total_usage_time(Request $request, $id){

        $request_user = $request->user;          
        
        $app_entries = $request_user->apps()->wherePivot('app_id', $id)->get();
        $app_entry = $request_user->apps()->wherePivot('app_id', $id)->select('name')->first();
        
        $app_time_calculator = new AppTimeCalculator($app_entries);
        $total_usage_time_in_seconds = $app_time_calculator->app_total_hours();
        $total_usage_time = Carbon::createFromTimestampUTC($total_usage_time_in_seconds)->toTimeString();

        return response()->json([

            "app_name" => $app_entry->name,
            "total_usage_time" => $total_usage_time,  

        ], 200);

    }

    public function get_apps_statistics(Request $request)
    {
        $request_user = $request->user;        
        $apps_names = App::select('name')->get();
        $apps_time_averages = [];      
      
        foreach ($apps_names as $app_name)
        {           
            $app_entries = $request_user->apps()->where('name', '=', $app_name["name"])->get();
            $app_time_calculator = new AppTimeCalculator($app_entries);
            $total_usage_time_in_seconds = $app_time_calculator->app_total_hours();
            $total_usage_time = Carbon::createFromTimestampUTC($total_usage_time_in_seconds)->toTimeString();                    
            
            $total_usage_time_in_milliseconds  = $total_usage_time_in_seconds * 1000;
            
            $day_average = Carbon::createFromTimestampMs($total_usage_time_in_milliseconds / 365)->format('H:i:s.u');            
            $week_average = Carbon::createFromTimestampMs($total_usage_time_in_milliseconds / 52)->format('H:i:s.u');
            $month_average = Carbon::createFromTimestampMs($total_usage_time_in_milliseconds / 12)->format('H:i:s.u');
          
            $apps_time_averages[] = new AppTimeStorage($app_name["name"], $total_usage_time, $day_average, $week_average, $month_average);

        }

        return response()->json(
            
            $apps_time_averages           
                       
        , 200);

    }

    //TIEMPO TOTAL DE DIAS ANTERIORES// // CASI TERMINADO //
    public function total_usage_time_per_day(Request $request, $id)
    {
        $request_user = $request->user;        
        $app_entries = $request_user->apps;
        $app_entries_by_date = $app_entries->where("id", "=", $id)->groupBy(function($item) {
            $new_date = Carbon::parse($item->pivot->date);
            return $new_date->format('Y-m-d');
        });
        $app_dates_per_day = [];

        foreach ($app_entries_by_date as $key => $entry) {

            $app_time_calculator = new AppTimeCalculator($entry);        
            $total_usage_time_in_seconds = $app_time_calculator->app_total_hours();
            $total_usage_time = Carbon::createFromTimestampUTC($total_usage_time_in_seconds)->toTimeString();
            $app_dates_per_day[$key] = $total_usage_time;

        }

        return response()->json(

            $app_dates_per_day 

        , 200);
    
    }

    public function get_apps_coordinates(Request $request)
    {
        $request_user = $request->user;        
        $apps_names = App::select('name')->get();
        $apps_coordinates = [];
        $apps_coordinates_groups = [];
 
        foreach ($apps_names as $app_name)
        {
            $app_entry = $request_user->apps()->where('name', '=', $app_name["name"])->latest('date')->first();
            $app_time_storage = new AppTimeStorage();
            $apps_coordinates[] = $app_time_storage->create()->set_coordinates($app_entry->name, $app_entry->pivot->latitude, $app_entry->pivot->longitude);

        }

        foreach ($apps_coordinates as $app_coordinates_entry)
        {
            $is_found = false;

            foreach ($apps_coordinates_groups as $new_array_line)
            {        
                if($app_coordinates_entry->latitude == $new_array_line->latitude && $app_coordinates_entry->longitude == $new_array_line->longitude){
              
                    $new_array_line->name .= " " . $app_coordinates_entry->name;
                    $is_found = true;
                    break;

                }

            }

            if($is_found == false){

                $apps_coordinates_groups[] = $app_coordinates_entry;

            }

        }

        return response()->json(

            $apps_coordinates_groups

        , 200);
       
    }

    ///////////////////////////////////////////FINAL/////////////////////////////////////////////

    //PRUEBA////GUARDARLO COMO ORO EN PANO//
    public function SAVE_total_usage_time(Request $request, $id)
    {
        $request_user = $request->user;
        //$app_entries = $request_user->apps()->wherePivot('app_id', $id)->whereDate('date', "=", '2019-11-19')->get();//ATENTO A LA DATE//    
        $app_entries = $request_user->apps()->wherePivot('app_id', $id)->get();//ATENTO A LA DATE//
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

}
