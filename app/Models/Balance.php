<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Balance extends Model
{
    use SoftDeletes, Uuid, HasFactory;
    protected $table = "balance";
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    } 
    public function getCurrency()
    {
        return $this->belongsTo(Countrysupported::class,'country_id');
    } 
    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
