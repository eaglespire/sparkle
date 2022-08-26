<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Reply extends Model {
    use Uuid;
    protected $table = "reply_support";
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function staff()
    {
        return $this->belongsTo('App\Models\Admin','staff_id');
    }   
}
