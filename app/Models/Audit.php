<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Audit extends Model {
    use Uuid;
    protected $table = "audit_logs";
    protected $guarded = [];
}
