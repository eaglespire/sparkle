<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Traits\Uuid;

class Paymentlink extends Model
{
    use Sortable, Uuid, SoftDeletes;
    protected $table = "payment_link";
    protected $guarded = [];
    public $sortable = [
        'name',
        'active',
        'business_id',
        'status',
        'amount',
        'created_at',
        'updated_at',
    ];

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
    public function business(){
        return Business::wherereference($this->business_id)->first();
    } 

}
