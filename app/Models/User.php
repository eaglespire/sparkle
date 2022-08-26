<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Traits\Uuid;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, Sortable, Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $sortable = [
        'first_name',
        'last_name',
        'email',
        'status',
        'created_at',
    ];
    protected $fillable = [
        'facebook', 
        'twitter', 
        'instagram', 
        'linkedin', 
        'youtube'
    ];
    protected $guard = 'user';

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function business()
    {
        return Business::wherereference($this->business_id)->first();
    }  
    public function getCountrySupported()
    {
        return Countrysupported::find($this->country_id);
    }    
    public function getCountry()
    {
        return Country::find($this->getCountrySupported()->country_id);
    }   
    public function getVcard()
    {
        return Virtual::whereUser_id($this->id)->wherebusiness_id($this->business_id)->orderby('id', 'DESC')->paginate(6);
    }   
    public function getState()
    {
        return Shipstate::wherecountry_code($this->getCountry()->iso2)->orderby('name', 'asc')->get();
    }  
    public function myState()
    {
        return Shipstate::whereid($this->state)->first();
    }    
    public function myCity()
    {
        return Shipcity::whereid($this->city)->first();
    }
    public function getPayment($limit)
    {
        return Paymentlink::whereuser_id($this->id)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->orderby('id', 'desc')->paginate($limit);
    }    
    public function getTransactions()
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->latest()->get();
    }    
    public function getUniqueTransactions($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->wheremode($this->business()->live)->latest()->get();
    }
    public function getPendingTransactions($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->wherepending(1)->wheremode($this->business()->live)->latest()->sum('pending_amount');
    }    
    public function getTransactionsExceptPayout($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->where('type', '!=', 3)->wherestatus(1)->wheremode($this->business()->live)->latest()->get();
    }    
    public function getBalance($id)
    {
        return Balance::where('user_id', $this->id)->wherebusiness_id($this->business_id)->where('country_id', $id)->first();
    }    
    public function getFirstBalance()
    {
        return Balance::where('user_id', $this->id)->wherebusiness_id($this->business_id)->where('country_id', $this->country_id)->first();
    }    
    public function getLastTransaction($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->wheremode($this->business()->live)->orderby('id', 'desc')->first();
    }    
    public function getChargeBacks()
    {
        return Transactions::wherereceiver_id($this->id)->wherebusiness_id($this->business_id)->wheremode(1)->wherechargebacks(1)->latest()->get();
    }    
    public function getBeneficiary($country=null)
    {
        if($country==null){
            return beneficiary::whereuser_id($this->id)->wherebusiness_id($this->business_id)->latest()->get();
        }else{
            return beneficiary::whereuser_id($this->id)->wherebusiness_id($this->business_id)->wherecountry($country)->latest()->get();
        }
    }
}
