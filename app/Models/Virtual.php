<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Virtual extends Model {
    use Uuid;
    protected $table = "virtual_cards";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }    
    public function getCurrency()
    {
        return $this->belongsTo(Countrysupported::class, 'currency');
    } 
    protected static function boot()
    {
        parent::boot();
        if (auth()->guard('user')->check()) {
            self::creating(function($model) {
                $model->business_id = auth()->guard('user')->user()->business_id;
            });
        }
    }
}
