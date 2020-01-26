<?php

namespace App\Helpers;

class AppTimeStorage {

    public $name;
    public $total_time;
    public $day_average;
    public $week_average;
    public $month_average;
    public $latitude;
    public $longitude;
        
    function __construct($name = null, $total_time = null, $day_average = null, $week_average = null, $month_average = null)
    {
        $this->name = $name;
        $this->total_time = $total_time;
        $this->day_average = $day_average;
        $this->week_average = $week_average;
        $this->month_average = $month_average;
      
    }

    public static function create() {
        
        $instance = new self();
        return $instance;
        
    }

    public function set_coordinates($name, $latitude, $longitude){
        
        $this->name = $name;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        return $this;

    }

}
