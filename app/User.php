<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['id','name','email','password'];

    public function apps()
    {
        return $this->belongsToMany('App\App', 'users_have_apps')
                    ->withPivot('date', 'event', 'latitude', 'longitude')                                    
                    ->withTimestamps();                    
    }

    public function apps_restrictions()
    {
        return $this->belongsToMany('App\App', 'users_restrict_apps')
                    ->withPivot('maximum_usage_time', 'usage_from_hour', 'usage_to_hour') 
                    ->withTimestamps();
    }
    
    public function apps_dates()
    {
        return $this->belongsToMany('App\App', 'users_have_apps')
                    ->withPivot('event')    
                    ->select(DB::raw('DATE(date) as date_group'), 'date')                                   
                    ->withTimestamps();                    
    }

    public function apps_coordinates()
    {
        return $this->belongsToMany('App\App', 'users_have_apps')
                    ->withPivot('date', 'latitude', 'longitude')    
                    ->select('name', 'date', 'latitude', 'longitude')                                   
                    ->withTimestamps();                    
    }

}
