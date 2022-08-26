<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class beneficiary extends Model
{
    use HasFactory, Uuid;

    public function getCurrency()
    {
        return Countrysupported::find($this->country);
    }  
}
