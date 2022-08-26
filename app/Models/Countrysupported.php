<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Countrysupported extends Model
{
    protected $table = "country_supported";
    protected $guarded = [];

    public function real()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    //Payout
    public function payoutYear($type)
    {
        if ($type == 1)
            return number_format(Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%y-%m-%d")')->sum('amount'), 2);
        else
            return Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%y-%m-%d")')->count();
    }
    public function payoutMonth($type)
    {
        if ($type == 1)
            return number_format(Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%m-%d")')->sum('amount'), 2);
        else
            return Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%m-%d")')->count();
    }
    public function payoutToday($type)
    {
        if ($type == 1)
            return number_format(Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->wheredate('created_at', '=', Carbon::today())->sum('amount'), 2);
        else
            return Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->wheredate('created_at', '=', Carbon::today())->count();
    }
    public function payoutTotal($type)
    {
        if ($type == 1)
            return number_format(Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->sum('amount'), 2);
        else
            return Transactions::wheretype(3)->wherestatus(1)->wherecurrency($this->id)->count();
    }
    //End of Payout

    //Charge
    public function chargeYear($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%y-%m-%d")')->sum('charge'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%y-%m-%d")')->count();
    }
    public function chargeMonth($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%m-%d")')->sum('charge'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%m-%d")')->count();
    }
    public function chargeToday($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->wheredate('created_at', '=', Carbon::today())->sum('charge'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->wheredate('created_at', '=', Carbon::today())->count();
    }
    public function chargeTotal($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->sum('charge'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->count();
    }
    //End of Charge
    //Transaction
    public function transactionYear($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%y-%m-%d")')->sum('amount'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%y-%m-%d")')->count();
    }
    public function transactionMonth($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%m-%d")')->sum('amount'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->orderByRaw('DATE_FORMAT(created_at, "%m-%d")')->count();
    }
    public function transactionToday($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->wheredate('created_at', '=', Carbon::today())->sum('amount'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->wheredate('created_at', '=', Carbon::today())->count();
    }
    public function transactionTotal($type)
    {
        if ($type == 1)
            return number_format(Transactions::wherestatus(1)->wherecurrency($this->id)->sum('amount'), 2);
        else
            return Transactions::wherestatus(1)->wherecurrency($this->id)->count();
    }
    //End of Transaction
}
