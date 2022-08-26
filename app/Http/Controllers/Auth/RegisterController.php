<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Settings;
use App\Models\Balance;
use App\Models\Business;
use App\Jobs\welcomeEmail;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';

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

    public function showRegistrationForm()
    {
        $data['title'] = 'Register';
        return view('auth.register', $data);
    }

    public function submitregister(Request $request)
    {
        $set = Settings::first();
        if($set->maintenance==1){
            return back()->with('alert', 'We are currently under maintenance, please try again later');
        }
        if($set->email_verification==0){
            $email_verify = 1;
        }else{
            $email_verify = 0;
        }
        $verification_code = strtoupper(Str::random(32));
        $customMessages = [
            'email.unique' => 'This email is already in use, do you want to <a href=' . route("login") . '>login?</a>',
        ];
        if ($set->recaptcha == 1) {
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'business_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'g-recaptcha-response' => 'required|captcha',
                'terms' => 'required',
                'country' => 'required',
            ];
        } else {
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'business_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'terms' => 'required',
                'country' => 'required',
            ];
        }
        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return back()->with('alert', $validator->errors());
        }
        $pieces=explode('*', $request->country);
        $country=getPricing($pieces[1])->id;
        $user = new User();
        $user->first_name = ucwords(strtolower($request->first_name));
        $user->last_name = ucwords(strtolower($request->last_name));
        $user->country_id = $country;
        $user->email = $request->email;
        $user->email_verify = $email_verify;
        $user->verification_code = $verification_code;
        $user->email_time = Carbon::parse()->addMinutes(5);
        $user->ip_address = user_ip();
        $user->password = Hash::make($request->password);
        $user->last_login = Carbon::now();
        $user->save();
        $business = new Business();
        $business->user_id=$user->id;
        $business->name=$request->business_name;
        $business->reference=randomNumber(7);
        $business->public_key = 'pub-live-' . Str::random(32);
        $business->secret_key = 'sec-live-' . Str::random(32);
        $business->test_public_key = 'pub-test-' . Str::random(32);
        $business->test_secret_key = 'sec-test-' . Str::random(32);
        if(count(getAcceptedCountryCard())>0){
            $business->card = 1;
        }
        if(count(getAcceptedCountryBank())>0){
            $business->bank_account = 1;
        }
        if(count(getAcceptedCountryMobileMoney())>0){
            $business->mobile_money = 1;
        }
        $business->save();
        $user->business_id = $business->reference;
        $user->save();
        foreach (getAcceptedCountry() as $val) {
            $data = new Balance();
            $data->user_id = $user->id;
            $data->country_id = $val->id;
            $data->ref_id = Str::random(32);
            $data->business_id = $business->reference;
            $data->save();
        }
        if ($set->email_verification == 1) {
            dispatch(new welcomeEmail($user->id));
            dispatch(new SendEmail($user->email, $user->first_name . ' ' . $user->last_name, 'We need to verify your email address', 'Thanks you for signing up to '.$set->site_name.'.<br> As part of our securtiy checks we need to verify your email address. Simply click on the link below and job done.<br><a href=' . route('user.confirm-email', ['id' => $verification_code]) . '>' . route('user.confirm-email', ['id' => $verification_code]) . '</a>'));
        }
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password,])) {
            return redirect()->route('user.dashboard');
        }
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
