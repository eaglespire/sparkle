<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Carderrors extends Model
{    
    use Uuid;
    protected $table = "card_errors";
    use HasFactory;
}
