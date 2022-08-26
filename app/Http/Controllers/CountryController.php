<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Countrysupported;
use App\Models\Paymentlink;
use App\Models\Exttransfer;
use App\Models\Balance;
use App\Models\Transactions;
use App\Models\Ticket;
use App\Models\CountryRegistered;
use App\Models\Country;
use App\Models\Virtual;
use App\Models\Virtualtransactions;
use Illuminate\Support\Str;



class CountryController extends Controller
{
    
//Country
    public function create(Request $request)
    {
        $country=Country::whereid($request->id)->first();
        if(CountryRegistered::wherecountry_id($request->id)->count()==0){
            $gg=new CountryRegistered();
            $gg->country_id=$request->id;
            $gg->save();
            foreach(getAcceptedCountry() as $val){
                if($val->real->currency==$country->currency){
                    return back()->with('success', 'Country added'); 
                }
            }
            if(Countrysupported::wherecountry_id($request->id)->count()==0){
                $datac=new Countrysupported();
                $datac->country_id = $request->id;
                $datac->save();
                $users = User::all();
                foreach ($users as $user) {
                    foreach (getAcceptedCountry() as $val) {
                        $check = Balance::whereuser_id($user->id)->wherecountry_id($val->id)->count();
                        if ($check == 0) {
                            $data = new Balance();
                            $data->user_id = $user->id;
                            $data->country_id = $val->id;
                            $data->business_id = $user->business_id;
                            $data->ref_id = Str::random(32);
                            $data->save();
                        } else {
                            $data = Balance::whereuser_id($user->id)->wherebusiness_id($user->business_id)->wherecountry_id($val->id)->first();
                            if ($data->ref_id == null) {
                                $data->ref_id = Str::random(32);
                                $data->save();
                            }
                        }
                    }
                }
                return redirect()->route('admin.edit.currency', ['id'=>$datac->id])->with('success', 'Country added');
            }
        }else{
            return back()->with('alert', 'This country has already been added'); 
        }
    }
    public function index()
    {
        $data['title']='Country Supported';
        return view('admin.country.country', $data);
    }    
    public function delete($id)
    {
        $country = CountryRegistered::findOrFail($id);
        if(Countrysupported::wherecountry_id($country->country_id)->count()>1){
            $country->delete();
        }else{
            $data = Countrysupported::wherecountry_id($country->country_id);
            $again=Countrysupported::count();
            if($again>1){
                $users=User::wherecountry_id($country->country_id)->get();
                foreach($users as $val){
                    Ticket::whereuser_id($val->id)->delete();
                    Balance::whereuser_id($val->id)->wherecountry_id($country->country_id)->delete();
                }
                Transactions::wherecurrency($country->country_id)->delete();
                Paymentlink::wherecurrency($country->country_id)->delete();
                Exttransfer::wherecurrency($country->country_id)->delete();
                Virtual::wherecurrency($country->country_id)->delete();
                $data->delete();
                $country->delete();
                return back()->with('success', 'Country was Successfully deleted!');
            }else{
                return back()->with('alert', 'There must be at least 1 currency on the system!');
            }
        }
    }  
    public function disable($id)
    {
        $data=CountryRegistered::find($id);
        $data->status=0;
        $data->save();
        return back()->with('success', 'country has been suspended.');
    } 
    public function enable($id)
    {
        $data=CountryRegistered::find($id);
        $data->status=1;
        $data->save();
        return back()->with('success', 'country was successfully published.');
    }          
//
// 
}
