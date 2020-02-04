<?php

namespace App\Helpers;

class AppRestrictionManager {

    public $name;
    public $initial_time;
    public $finish_time;
    public $total_time;

    function __construct($name, $initial_time, $finish_time, $total_time)
    {
        $this->name = $name;
        $this->initial_time = $initial_time;
        $this->finish_time = $finish_time;
        $this->total_time = $total_time;
    }
}