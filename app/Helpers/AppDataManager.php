<?php

namespace App\Helpers;

class AppDataManager {

    public $id;
    public $name;
    public $logo;
    public $today_total_time;

    function __construct($id, $name, $logo, $today_total_time)
    {
        $this->id = $id;
        $this->name = $name;
        $this->logo = $logo;
        $this->today_total_time = $today_total_time;
    }
}