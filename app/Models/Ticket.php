<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Ticket extends Model
{
    use SoftDeletes, Uuid;
    protected $table = "support";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function business(){
        return Business::wherereference($this->business_id)->first();
    } 
}
