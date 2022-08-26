<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Banksupported extends Model {
    use Uuid;
    protected $table = "bank_supported";
    protected $guarded = [];

    public function creal()
    {
        return $this->belongsTo('App\Models\Country','country_id');
    }
}
