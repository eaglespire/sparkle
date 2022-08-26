<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Business extends Model
{
    use HasFactory, Uuid;
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function getState()
    {
        return Shipstate::wherecountry_code($this->getCountry()->iso2)->orderby('name', 'asc')->get();
    }  
    public function myState()
    {
        return Shipstate::whereid($this->state)->first();
    }    
    public function myCity()
    {
        return Shipcity::whereid($this->city)->first();
    }    
    public function myBusinessState()
    {
        return Shipstate::whereid($this->business_state)->first();
    }    
    public function myBusinessCity()
    {
        return Shipcity::whereid($this->business_city)->first();
    }
}
