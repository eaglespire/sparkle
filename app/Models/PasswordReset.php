<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class PasswordReset extends Model
{
    use Uuid;
    protected $table = "password_resets";
    protected $guard = [];
}
