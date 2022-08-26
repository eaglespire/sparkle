<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Countrysupported;
use App\Models\Banksupported;
use App\Models\Rates;



class CurrencyController extends Controller
{

    //Country
    public function update(Request $request, $id)
    {
        if ($request->card != null || $request->bank_acount != null || $request->mobile_money != null) {
            $data = Countrysupported::whereid($id)->first();
            $data->bank_format = $request->bank_format;
            $data->min_amount = $request->min_amount;
            $data->pending_balance_duration = $request->pending_balance_duration;
            $data->virtual_min_amount = $request->virtual_min_amount;
            $data->swap_min_amount = $request->swap_min_amount;
            $data->max_amount = $request->max_amount;
            $data->virtual_max_amount = $request->virtual_max_amount;
            $data->swap_max_amount = $request->swap_max_amount;
            $data->fiat_charge = $request->fiat_charge;
            $data->percent_charge = $request->percent_charge;
            $data->withdraw_fiat_charge = $request->withdraw_fiat_charge;
            $data->withdraw_percent_charge = $request->withdraw_percent_charge;
            $data->virtual_fiat_charge = $request->virtual_fiat_charge;
            $data->virtual_percent_charge = $request->virtual_percent_charge;
            $data->duration = $request->duration;
            $data->auth_key = $request->auth_key;
            $data->auth_secret = $request->auth_secret;
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->iban = $request->iban;
            $data->acct_no = $request->acct_no;
            $data->routing_no = $request->routing_no;
            $data->sort_code = $request->sort_code;
            if (empty($request->card)) {
                $data->card = 0;
            } else {
                $data->card = $request->card;
            }
            if (empty($request->bank_account)) {
                $data->bank_account = 0;
            } else {
                $data->bank_account = $request->bank_account;
            }
            if (empty($request->mobile_money)) {
                $data->mobile_money = 0;
            } else {
                $data->mobile_money = $request->mobile_money;
            }
            if (empty($request->funding)) {
                $data->funding = 0;
            } else {
                $data->funding = $request->funding;
            }
            if (empty($request->virtual_card)) {
                $data->virtual_card = 0;
            } else {
                $data->virtual_card = $request->virtual_card;
            }
            $data->save();
            return back()->with('success', 'Country was Successfully updated!');
        } else {
            return back()->with('alert', 'Select a payment method');
        }
    }
    public function index()
    {
        $data['title'] = 'Currency Settings';
        return view('admin.country.index', $data);
    }
    public function edit($id)
    {
        $data['val'] = Countrysupported::find($id);
        $data['title'] = 'Edit Currency';
        return view('admin.country.edit', $data);
    }
    public function disable($id)
    {
        $data = Countrysupported::find($id);
        $data->status = 0;
        $data->save();
        return back()->with('success', 'country has been suspended.');
    }
    public function enable($id)
    {
        $data = Countrysupported::find($id);
        $data->status = 1;
        $data->save();
        return back()->with('success', 'country was successfully published.');
    }
    public function users($id)
    {
        $location = Countrysupported::whereid($id)->first();
        $data['title'] = 'Customers from ' . $location->real->name;
        $data['users'] = User::wherecountry_id($id)->orderby('id', 'desc')->paginate(10);
        return view('admin.user.index', $data);
    }
    public function createRate(Request $request, $id)
    {
        if (Rates::wherefrom_currency($id)->whereto_currency($request->to_currency)->count() > 0) {
            return back()->with('alert', 'currency rate already added');
        } else {
            $data = new Rates();
            $data->from_currency = $id;
            $data->to_currency = $request->to_currency;
            $data->rate = $request->rate;
            $data->charge = $request->charge;
            $data->save();
            return back()->with('success', 'Saved!');
        }
    }
    public function updateRate(Request $request, $id)
    {
        $data = Rates::find($id);
        $data->rate = $request->rate;
        $data->charge = $request->charge;
        $data->save();
        return back()->with('success', 'Saved!');
    }
    public function deleteRate($id)
    {
        $data = Rates::find($id);
        $data->delete();
        return back()->with('success', 'Deleted!');
    }
    //

    //Bank
    public function Updatebank(Request $request)
    {
        $mac = Banksupported::whereid($request->id)->first();
        $mac['name'] = $request->name;
        $res = $mac->save();
        if ($res) {
            return back()->with('success', 'Bank successfully updated');
        } else {
            return back()->with('alert', 'Problem With Updating Bank');
        }
    }
    public function Createbank(Request $request)
    {
        $data['country_id'] = $request->country;
        $data['name'] = $request->name;
        $res = Banksupported::create($data);
        if ($res) {
            return back()->with('success', 'Bank successfully created');
        } else {
            return back()->with('alert', 'Problem With Creating New Bank');
        }
    }
    public function bank($id)
    {
        $data['title'] = 'Bank Supported';
        $data['bank'] = Banksupported::wherecountry_id($id)->orderby('name', 'asc')->get();
        $data['country'] = $id;
        return view('admin.country.bank', $data);
    }
    public function Destroybank($id)
    {
        $data = Banksupported::findOrFail($id);
        $data->delete();
        return back()->with('toast_success', 'Bank was Successfully deleted!');
    }
    // 
}
