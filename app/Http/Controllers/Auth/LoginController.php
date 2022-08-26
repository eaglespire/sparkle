<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Settings;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guest:user');
    }

    public function showLoginForm()
    {
		$data['title']='Login';
        return view('auth.login', $data);
    } 
    
    public function submitlogin(Request $request)
    {
        $set=Settings::first();
        if($set->maintenance==1){
            return back()->with('alert', 'We are currently under maintenance, please try again later');
        }
        if($set->recaptcha==1){
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha'
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'password' => 'required'
            ]);
        }
        if ($validator->fails()) {
            return back()->with('alert', $validator->errors());
        }
        $remember_me = $request->has('remember_me') ? true : false; 
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password], $remember_me)) {
        	$user=User::whereid(Auth::guard('user')->user()->id)->first();
	        $user->last_login=Carbon::now();
	        $user->ip_address=user_ip();
            $user->save();
            App::setLocale($user->language);
            session()->put('locale', $user->language);
            if($user->fa_status==1){
                return redirect()->route('2fa');
            }else{
                return redirect()->intended();
            }
        } else {
        	return back()->with('alert', 'Oops! You have entered invalid credentials');
        }

    }

}
