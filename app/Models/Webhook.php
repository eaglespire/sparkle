<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model {
    protected $table = "webhook_logs";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
