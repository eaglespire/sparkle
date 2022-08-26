<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Exttransfer extends Model
{
    use SoftDeletes, Uuid;
    protected $table = "ext_transfer";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function getCurrency()
    {
        return $this->belongsTo(Countrysupported::class, 'currency');
    }
    public function getTransaction()
    {
        return Transactions::wherepayment_link($this->id)->first();
    }
}
