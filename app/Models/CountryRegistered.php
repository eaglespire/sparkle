<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryRegistered extends Model
{
    use HasFactory;
    protected $table = "country_registered";
    public function real()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }
}
