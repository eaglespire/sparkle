<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Settings;
use App\Models\Admin;
use App\Models\Etemplate;
use Carbon\Carbon;


class SettingController extends Controller
{

    public function Settings()
    {
        $data['title']='General settings';
        $data['val']=Admin::first();
        return view('admin.settings.index', $data);
    }    
    
    public function Email()
    {
        $data['title']='Email settings';
        return view('admin.settings.email', $data);
    }    
    
    public function Template()
    {
        $data['title']='Email template';
        return view('admin.settings.template', $data);
    } 

    public function EmailUpdate(Request $request)
    {
        $data = Etemplate::findOrFail(1);
        $data->esender=$request->sender;
        $data->emessage=Purifier::clean($request->message);
        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }      

    public function Account()
    {
        $data['title']='Change account details';
        $data['val']=Admin::first();
        return view('admin.settings.account', $data);
    } 

    public function AccountUpdate(Request $request)
    {
        $data = Admin::whereid(1)->first();
        $data->username=$request->username;
        $data->password=Hash::make($request->password);
        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }  
        
    public function SettingsUpdate(Request $request)
    {
        $data = Settings::findOrFail(1);
        $data->fill($request->all())->save();
        return back()->with('success', 'Update was Successful!');
    }    
    public function Features(Request $request)
    {
        $data = Settings::findOrFail(1);  
        if(empty($request->email_verification)){
            $data->email_verification=0;	
        }else{
            $data->email_verification=$request->email_verification;
        }             
        if(empty($request->email_notify)){
            $data->email_notify=0;	
        }else{
            $data->email_notify=$request->email_notify;
        }      
        if(empty($request->registration)){
            $data->registration=0;	
        }else{
            $data->registration=$request->registration;
        }                   
        if(empty($request->recaptcha)){
            $data->recaptcha=0;	
        }else{
            $data->recaptcha=$request->recaptcha;
        }        
        if(empty($request->maintenance)){
            $data->maintenance=0;	
        }else{
            $data->maintenance=$request->maintenance;
        }        
        if(empty($request->language)){
            $data->language=0;	
        }else{
            $data->language=$request->language;
        }        
        if(empty($request->preloader)){
            $data->preloader=0;	
        }else{
            $data->preloader=$request->preloader;
        }             
        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }      
    
    public function charges(Request $request)
    {
        $data = Settings::findOrFail(1);
        $data->transfer_charge=$request->transfer_charge;
        $data->transfer_chargep=$request->transfer_chargep;
        $data->balance_reg=$request->bal;
        $data->withdraw_duration=$request->withdraw_duration;
        $data->merchant_charge=$request->merchant_charge;
        $data->merchant_chargep=$request->merchant_chargep;
        $data->invoice_charge=$request->invoice_charge;
        $data->invoice_chargep=$request->invoice_chargep;
        $data->product_charge=$request->product_charge; 
        $data->product_chargep=$request->product_chargep; 
        $data->subscription_charge=$request->subscription_charge; 
        $data->subscription_chargep=$request->subscription_chargep; 
        $data->donation_charge=$request->donation_charge; 
        $data->donation_chargep=$request->donation_chargep; 
        $data->single_charge=$request->single_charge; 
        $data->single_chargep=$request->single_chargep;         
        $data->qr_charge=$request->qr_charge; 
        $data->qr_chargep=$request->qr_chargep; 
        $data->min_transfer=$request->min_transfer; 
        $data->bill_charge=$request->bill_charge;
        $data->bill_chargep=$request->bill_chargep;
        $data->virtual_createcharge=$request->virtual_createcharge;
        $data->virtual_createchargep=$request->virtual_createchargep;
        $data->virtual_charge=$request->virtual_charge;
        $data->virtual_chargep=$request->virtual_chargep;
        $data->vc_min=$request->vc_min;
        $data->vc_max=$request->vc_max;
        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }    
    
    public function crypto(Request $request)
    {
        $data = Settings::findOrFail(1);
        $data->btc_sell=$request->btc_sell;
        $data->btc_buy=$request->btc_buy;        
        $data->eth_sell=$request->eth_sell;
        $data->eth_buy=$request->eth_buy;
        $data->min_btcbuy=$request->min_btcbuy;
        $data->min_btcsell=$request->min_btcsell;        
        $data->min_ethbuy=$request->min_ethbuy;
        $data->min_ethsell=$request->min_sell;
        $data->btc_wallet=$request->btc_wallet;
        $data->eth_wallet=$request->eth_wallet;
        $res=$data->save();
        if ($res) {
            return back()->with('success', 'Update was Successful!');
        } else {
            return back()->with('alert', 'An error occured');
        }
    }  
}
