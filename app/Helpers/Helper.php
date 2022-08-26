<?php

use Illuminate\Support\ServiceProvider;
use App\Models\Settings;
use App\Models\Logo;
use App\Models\Banksupported;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Language;
use App\Models\Countrysupported;
use App\Models\Country;
use App\Models\Carderrors;
use App\Models\Webhook;
use App\Models\Plugins;
use App\Models\Faqcategory;
use App\Models\CountryRegistered;
use App\Models\Faq;
use App\Models\Rates;
use App\Models\Business;
use Carbon\Carbon;
use Curl\Curl;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

function encrypt3Des($data, $key)
{
    $encData = openssl_encrypt($data, 'DES-EDE3', $key, OPENSSL_RAW_DATA);
    return base64_encode($encData);
}
function randomNumber($length)
{
    $result = '';
    for ($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}
function nextPayoutDate($duration)
{
    $dt = Carbon::now()->add($duration . ' Day');
    if (date("D", strtotime($dt)) == "Sat") {
        $dt = Carbon::now()->add('2 Day');
        return $dt;
    } elseif (date("D", strtotime($dt)) == "Sun") {
        $dt = Carbon::now()->add('1 Day');
        return $dt;
    } else {
        return $dt;
    }
}
function getSettings()
{
    return Settings::find(1);
}
function getBusiness($id, $user)
{
    return Business::where('reference', '!=', $id)->whereuser_id($user)->get();
}
function getBusinessUser($user)
{
    return Business::whereuser_id($user)->get();
}
function getFaqCategory()
{
    return Faqcategory::all();
}
function getFaq($id)
{
    return Faq::wherecat_id($id)->limit(5)->get();
}
function getCardErrors($reference)
{
    return Carderrors::whereref_id($reference)->wheretype('error')->get();
}
function getCardLogs($reference)
{
    return Carderrors::whereref_id($reference)->orderby('created_at', 'asc')->get();
}
function getWebhook($reference)
{
    return Webhook::wherereference($reference)->get();
}
function getTransaction($id, $user)
{
    return Transactions::wherepayment_link($id)->wherereceiver_id($user)->first();
}
function getFirstCardLog($reference)
{
    $date = Carderrors::whereref_id($reference)->orderby('id', 'asc')->first();
    return new Carbon($date->created_at);
}
function getLastCardLog($reference)
{
    $date = Carderrors::whereref_id($reference)->orderby('id', 'desc')->first();
    return new Carbon($date->created_at);
}
function cardError($reference, $message, $type)
{
    $error = new Carderrors();
    $error->ref_id = $reference;
    $error->message = $message;
    $error->type = $type;
    $error->save();
    return response()->json([$error]);
}
function getAllCountry()
{
    return Country::all();
}
function getRegisteredCountry()
{
    return CountryRegistered::all();
}
function getRegisteredCountryActive()
{
    return CountryRegistered::wherestatus(1)->orderby('country_id', 'asc')->get();
}
function getLanguage()
{
    return Language::wherestatus(1)->get();
}
function getPlugins()
{
    return Plugins::wherestatus(1)->get();
}
function getLang()
{
    $locale = session()->get('locale');
    if ($locale == null) {
        $locale = "en";
    }
    return Language::wherecode($locale)->first();
}
function getAcceptedCountry()
{
    return Countrysupported::wherestatus(1)->orderby('country_id', 'asc')->get();
}
function getAcceptedCountryBank()
{
    return Countrysupported::wherestatus(1)->wherebank_account(1)->orderby('country_id', 'asc')->get();
}
function getAcceptedCountryCard()
{
    return Countrysupported::wherestatus(1)->wherecard(1)->orderby('country_id', 'asc')->get();
}
function getAcceptedCountryMobileMoney()
{
    return Countrysupported::wherestatus(1)->wheremobile_money(1)->orderby('country_id', 'asc')->get();
}
function getAcceptedCountryVirtual()
{
    return Countrysupported::wherestatus(1)->wherevirtual_card(1)->orderby('country_id', 'asc')->get();
}
function getCountry($id)
{
    return Countrysupported::whereid($id)->first();
}
function getAllCountryExcept($id)
{
    return Countrysupported::where('id', '!=', $id)->wherestatus(1)->orderby('country_id', 'asc')->get();
}
function getCountryRates($id)
{
    return Rates::wherefrom_currency($id)->get();
}
function getCountryRatesUnique($from, $to)
{
    return Rates::wherefrom_currency($from)->whereto_currency($to)->first();
}
function getPricing($id)
{
    foreach(Countrysupported::wherestatus(1)->orderby('country_id', 'asc')->get() as $val){
        if($val->real->currency==$id){
            return $val;
        }
    }
}
function getBank($id)
{
    return Banksupported::wherecountry_id($id)->orderby('name', 'asc')->get();
}
function getBankFirst($id)
{
    return Banksupported::find($id);
}
function getAllAcceptedCountry()
{
    return Countrysupported::orderby('country_id', 'asc')->get();
}
function send_email($to, $name, $subject, $body)
{
    $set = Settings::first();
    $mlogo = Logo::first();
    $from = env('MAIL_FROM_ADDRESS');
    $logo = url('/') . '/asset/' . $mlogo->dark;
    $text = str_replace("{{logo}}", $logo, (str_replace("{{site_name}}", $set->site_name, str_replace("{{message}}", $body, $set->email_template))));
    Mail::send([], [], function ($message) use ($subject, $from, $set, $to, $text, $name) {
        $message->to($to, $name)->subject($subject)->from($from, $set->site_name)->setBody($text, 'text/html');
    });
}
function sub_check() {
    $set=Settings::first();
    if(env('PURCHASECODE')==null){
        session_start();
        $_SESSION["error"] = "no purchase code found";
        $url = route('ipn.flutter');
    	header("Location: ".$url);
        exit();
    }else{
        if($set->xperiod<Carbon::now()){
            $purchase_code=trim(env('PURCHASECODE'));
            $domain=trim(env('DOMAIN'));
            $curl = new Curl();
            $curl->setHeader('Content-Type', 'application/json');
            $curl->setHeader('Accept', 'application/json');
            $curl->get('https://boomchart.net/api/verify-purchase/'.$purchase_code.'/'.$domain);
            $curl->close();
            $response=$curl->response;
            //dd($response);
            if($response->status=="success"){
            }else{
                session_start();
                $_SESSION["error"] = $response->message;
                $url = route('ipn.flutter');
                header("Location: ".$url);
                exit();
            }
            $set->xperiod=Carbon::now()->add('10 minutes');
            $set->save();  
        }
    }
}

function send_webhook($id)
{
    $link = Transactions::whereref_id($id)->first();
    $user = User::whereid($link->receiver_id)->first();
    if ($link->mode == 1) {
        $mode = "live";
    } else {
        $mode = "test";
    }
    if ($link->status == 0) {
        $status = "pending";
    } elseif ($link->status == 1) {
        $status = "success";
    } elseif ($link->status == 3) {
        $status = "refunded";
    } elseif ($link->status == 4) {
        $status = "reversed";
    } else {
        $status = "failed/cancelled";
    }
    if ($link->client == 1) {
        $amount = $link->amount - $link->charge;
    } else {
        $amount = $link->amount;
    }
    if ($link->type == 1) {
        $data = [
            'first_name' => $link->first_name,
            'last_name' => $link->last_name,
            'email' => $link->email,
            'currency' => $link->receiver->getCountry()->currency,
            'amount' => number_format($amount, 2),
            'charge' => number_format($link->charge, 2),
            'mode' => $mode,
            'type' => "Payment",
            'status' => $status,
            'reference' => $link->ref_id,
            'created_at' => $link->created_at,
            'updated_at' => $link->updated_at
        ];
    } elseif ($link->type == 2) {
        $data = [
            'first_name' => $link->first_name,
            'last_name' => $link->last_name,
            'email' => $link->email,
            'currency' => $link->receiver->getCountry()->currency,
            'amount' => number_format($amount, 2),
            'charge' => number_format($link->charge, 2),
            'mode' => $mode,
            'type' => "API",
            'status' => $status,
            'reference' => $link->ref_id,
            'tx_ref' => $link->api->tx_ref,
            'customization' => [
                'title' => $link->api->title,
                'description' => $link->api->description,
                'logo' => $link->api->logo
            ],
            'meta' => $link->api->meta,
            'created_at' => $link->created_at,
            'updated_at' => $link->updated_at
        ];
    } elseif ($link->type == 3) {
        $data = [
            'first_name' => auth()->guard('user')->user()->first_name,
            'last_name' => auth()->guard('user')->user()->last_name,
            'email' => auth()->guard('user')->user()->email,
            'currency' => $link->receiver->getCountry()->currency,
            'amount' => number_format($amount, 2),
            'charge' => number_format($link->charge, 2),
            'mode' => $mode,
            'type' => "Payout",
            'status' => $status,
            'reference' => $link->ref_id,
            'created_at' => $link->created_at,
            'updated_at' => $link->updated_at
        ];
    }elseif ($link->type == 4) {
        $data = [
            'first_name' => auth()->guard('user')->user()->first_name,
            'last_name' => auth()->guard('user')->user()->last_name,
            'email' => auth()->guard('user')->user()->email,
            'currency' => $link->receiver->getCountry()->currency,
            'amount' => number_format($amount, 2),
            'charge' => number_format($link->charge, 2),
            'mode' => $mode,
            'type' => "Funding",
            'status' => $status,
            'reference' => $link->ref_id,
            'created_at' => $link->created_at,
            'updated_at' => $link->updated_at
        ];
    }
    WebhookCall::create()->maximumTries(1)->url($user->business()->webhook)->payload($data)->useSecret($user->business()->webhook_secret)->dispatch();
}
function user_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function UR_exists($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($code == 200) {
        $status = true;
    } else {
        $status = false;
    }
    curl_close($ch);
    return $status;
}
