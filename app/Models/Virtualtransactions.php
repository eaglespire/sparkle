<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Virtualtransactions extends Model {
    use Uuid;
    protected $table = "virtual_transactions";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }    
    public function card()
    {
        return $this->belongsTo('App\Models\Virtual','virtual_id');
    }   

}
