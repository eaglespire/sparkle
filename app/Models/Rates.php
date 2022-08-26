<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    use HasFactory;
    protected $table = "rates";
    public function getCurrency()
    {
        return $this->belongsTo(Countrysupported::class, 'to_currency');
    }
}
