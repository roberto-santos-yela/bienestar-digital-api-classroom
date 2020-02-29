<?php

namespace App\Helpers;
use Carbon\Carbon;

class AppTimeCalculator {

    private $app_entries;

    function __construct($app_entries)
    {
        $this->app_entries = $app_entries;

    }

    function app_total_hours()
    {
        $app_entries_lenght = count($this->app_entries);
        $total_time_in_seconds = 0;

        if($this->app_entries[0]->pivot->event == "closes")
        {                                  
            $date_format = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[0]->pivot->date)->format('Y-m-d'); 
            $date_format_at_midnight = $date_format . ' 00:00:00';
            $date_from_midnight = Carbon::parse($date_format_at_midnight);
            $date_hour = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[0]->pivot->date);
            $time_diff_from_midnight_in_seconds = $date_from_midnight->diffInSeconds($date_hour);
            $total_time_in_seconds = $total_time_in_seconds + $time_diff_from_midnight_in_seconds;            

            for ($x = 1; $x <= $app_entries_lenght - 1; $x++) {

                $have_both_hours = true;
    
                if($this->app_entries[$x]->pivot->event == "opens")
                {
                    $from_hour = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[$x]->pivot->date);                
                    $from_hour_format = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[$x]->pivot->date)->format('Y-m-d'); 
                    $from_hour_format_to_midnight = $from_hour_format . ' 23:59:59';
                    $today_to_midnight = Carbon::parse($from_hour_format_to_midnight);
                    $time_diff_till_midnight = $from_hour->diffInSeconds($today_to_midnight);
                    $total_time_in_seconds = $total_time_in_seconds + $time_diff_till_midnight;                             
                    $have_both_hours = false;                
                    
                }else if($this->app_entries[$x]->pivot->event == "closes"){
    
                    $total_time_in_seconds = $total_time_in_seconds - $time_diff_till_midnight;
                    $to_hour = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[$x]->pivot->date);                        
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
    
                if($this->app_entries[$x]->pivot->event == "opens")
                {
                    $from_hour = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[$x]->pivot->date);                
                    $from_hour_format = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[$x]->pivot->date)->format('Y-m-d'); 
                    $from_hour_format_to_midnight = $from_hour_format . ' 23:59:59';
                    $today_to_midnight = Carbon::parse($from_hour_format_to_midnight);
                    $time_diff_till_midnight = $from_hour->diffInSeconds($today_to_midnight);
                    $total_time_in_seconds = $total_time_in_seconds + $time_diff_till_midnight;                             
                    $have_both_hours = false;                
                    
                }else if($this->app_entries[$x]->pivot->event == "closes"){
    
                    $total_time_in_seconds = $total_time_in_seconds - $time_diff_till_midnight;
                    $to_hour = Carbon::createFromFormat('Y-m-d H:i:s', $this->app_entries[$x]->pivot->date);                        
                    $have_both_hours = true;
    
                }
                
                if($have_both_hours)
                {
                    $total_time_in_seconds += $from_hour->diffInSeconds($to_hour);
    
                }           
            }
        }

        return $total_usage_time = Carbon::createFromTimestampUTC($total_time_in_seconds)->secondsSinceMidnight();
    }
}
