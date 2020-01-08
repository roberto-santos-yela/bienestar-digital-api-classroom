<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    protected $table = 'apps';
    protected $fillable = ['id','logo','name'];

    public function users()
    {
        return $this->belongsToMany('App\User', 'users_have_apps')
                    ->withPivot('date', 'event', 'latitude', 'longitude') 
                    ->withTimestamps();                    
    }
    
    public function users_restrictions()
    {
        return $this->belongsToMany('App\App', 'users_restrict_apps')
                    ->withPivot('maximum_usage_time', 'usage_from_hour', 'usage_to_hour') 
                    ->withTimestamps();
    }

    
}
