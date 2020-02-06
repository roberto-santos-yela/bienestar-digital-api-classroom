<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\App;
use Mail;
use Carbon\Carbon;
use App\Helpers\Token;
use App\Helpers\PasswordGenerator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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
        $created_user = User::where('email', '=', $request->email)->first();

        if($created_user == null)
        {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = encrypt($request->password);
            $user->save();
    
            $token = new Token(["email" => $user->email]);
            $coded_token = $token->encode();
    
            return response()->json([
                    
                "token" => $coded_token,
    
            ],200);

        }else{

            return response()->json([

                "message" => "this email address is not available",

            ],401);

        }
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

    ///USER LOGIN/// ///TERMINADO///
    public function user_login(Request $request)
    {
        
        $user = User::where('email', '=', $request->email)->first();

        if($user != null)
        {
            $decrypted_user_password = decrypt($user->password);

        }else{

            return response()->json([

                "message" => "incorrect email or password",

            ], 401);

        }

        if($decrypted_user_password == $request->password)
        {
            $token = new Token(["email" => $user->email]);
            $coded_token = $token->encode();

            return response()->json([
                
                "token" => $coded_token,

            ], 200);

        }else{

            return response()->json([

                "message" => "incorrect email or password",

            ], 401);

        }

    }

    ///RECUPERAR CONTRASEÑA// //NO TERMINADO//
    public function recover_user_password(Request $request){

        $user = User::where('email', '=', $request->email)->first();

        if($user->email == $request->confirm_email)
        {
            $password_generator = new PasswordGenerator();
            $new_password = $password_generator->generate_password();        
            $user->password = encrypt($new_password);
            $user->save();
    
            $to_name = $user->name;
            $to_email = $user->email;

            $data = array('name'=> $to_name, "body" => "Su nueva contraseña es:", "new_password" => $new_password);
        
            Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
                
                $message->to($to_email, $to_name)
                        ->subject('Bienestar Digital Mail Recovery');
        
                $message->from('roberto_santos_apps1ma1819@cev.com','Bienestar Digital');
            });
    
            return response()->json([
    
                "message" => "a new password has been sent to your e-mail address",
                "new_password" => $new_password,
    
            ]);

        }else{

            return response()->json([
    
                "message" => "¡Passwords should match!",                
    
            ]);
        }
    }
    
    public function send_notification_email(Request $request){

        $request_user = $request->user;
        $to_name = $request_user->name;
        $to_email = $request_user->email;
        $data = array('name' => $to_name, 'app_name' => $request->app_name);
 
        Mail::send('emails.notification_mail', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)->subject('Bienestar Digital - App Restriccion Violation');
            $message->from('roberto_santos_apps1ma1819@cev.com','Bienestar Digital');
        });
        
    }

    //USO TOTAL//
    ///PRUEBA GET TIME DIFERENCE/// ////TERMINADO CON PINZAS///
    public function get_time_diff(Request $request, $id)
    {
        $request_user = $request->user;
        $app_entries = $request_user->apps()->wherePivot('app_id', $id)->get(); 
        $app_entry = $request_user->apps()->wherePivot('app_id', $id)->first();     
        $app_entries_lenght = count($app_entries);
        $total_time_in_seconds = 0;
                
        for ($x = 0; $x <= $app_entries_lenght - 1; $x++) {

            $have_both_hours = false;

            if($app_entries[$x]->pivot->event == "opens")
            {
                $from_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date);
                
            }else{

                $to_hour = Carbon::createFromFormat('Y-m-d H:i:s', $app_entries[$x]->pivot->date);                              
                $have_both_hours = true;

            }

            if($have_both_hours)
            {
                $total_time_in_seconds += $from_hour->diffInSeconds($to_hour);

            }
        }

        $total_usage_time = Carbon::createFromTimestampUTC($total_time_in_seconds)->toTimeString();

        return response()->json([

            "app_name" => $app_entry->name,
            "total_usage_time" => $total_usage_time,  

        ]);

    }

    //PRUEBA////GUARDARLO COMO ORO EN PANO//
    public function daily_usage_time(Request $request, $id)
    {
        $request_user = $request->user;
        $app_entries = $request_user->apps()->wherePivot('app_id', $id)->whereDate('date', "=", '2019-11-18')->get();     
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

    //CREAR RESTRICCIONES// //TERMINADO//
    public function create_restriction(Request $request, $id)
    {
        $request_user = $request->user;
        $app = $request_user->apps_restrictions->where('id', '=', $id)->first();
        
        if($app != null)
        {   
            $app->pivot->maximum_usage_time = $request->maximum_usage_time;
            $app->pivot->usage_from_hour = $request->usage_from_hour;
            $app->pivot->usage_to_hour = $request->usage_to_hour;
            $app->pivot->save();

        }else{

            $request_user->apps_restrictions()->attach($id, [

                'maximum_usage_time' => $request->maximum_usage_time,
                'usage_from_hour' => $request->usage_from_hour,
                'usage_to_hour' => $request->usage_to_hour,
    
            ]);   
        }  
                  
    }

    ///GENERAR NUEVA CONTASEÑA///
    public function generate_password()
    {
        echo("hola");

        $password_generator = new PasswordGenerator(8);
        $pass = $password_generator->generate_password(); 

        return response()->json([

            "new password" => $pass,

        ], 200);

    }

    ///BORRAR RESTRICCIONES/// ///NO TERMINADO///
    


    //OBTENER DATOS DEL USUARIO// //TERMINADO//
    public function get_user_data(Request $request)
    {
        $request_user = $request->user;     
        $decrypted_password = decrypt($request_user->password);
    
        return response()->json([

            "name" => $request_user->name,
            "email" => $request_user->email, 
            "password" => $decrypted_password,

        ], 200);

    }

    //CAMBIAR PASSWORD DEL USUARIO - V1// //TERMINADO//
    public function change_user_password(Request $request)
    {
        $request_user = $request->user;
        $current_password = decrypt($request_user->password);

        if($current_password == $request->old_password)
        {
            $request_user->password = encrypt($request->new_password);
            $request_user->save();

            return response()->json([

                "new password" => $request->new_password,
    
            ], 200);

        }else{
            
            return response()->json([

                "message" => "old password field is incorrect", 
    
            ], 401);

        }

    }

}
