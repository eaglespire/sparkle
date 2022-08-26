<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Virtual;
use App\Models\Virtualtransactions;
use App\Models\Balance;
use App\Jobs\SendEmail;
use Curl\Curl;
use Illuminate\Support\Facades\Validator;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;

class VirtualcardController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function adminCardTransactions()
    {
        $data['title'] = 'Transaction History';
        $data['log'] = Virtualtransactions::all();
        return view('admin.virtualcard.log', $data);
    }
    public function adminCard()
    {
        $data['title'] = 'Virtual Cards';
        $data['card'] = Virtual::orderby('id', 'DESC')->paginate(6);
        return view('admin.virtualcard.card', $data);
    }
    public function adminTransactionsVirtual($id)
    {
        $data['title'] = 'Transaction History';
        $data['log'] = Virtualtransactions::wherecard_hash($id)->get();
        return view('admin.virtualcard.card-log', $data);
    }
    public function userTransactions($id){
        $data['title']='Cards';
        $data['card']=Virtual::whereuser_id($id)->paginate(6);
        return view('admin.virtualcard.card', $data);
    }  
    public function adminBlockVirtual($id)
    {
        $set = Settings::first();
        $vcard = Virtual::wherecard_hash($id)->first();
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $set->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/status/block");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $vcard->status = 2;
            $vcard->save();
            return back()->with('success', $response->message);
        }
    }
    public function adminUnblockVirtual($id)
    {
        $set = Settings::first();
        $vcard = Virtual::wherecard_hash($id)->first();
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $set->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/status/unblock");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $vcard->status = 1;
            $vcard->save();
            return back()->with('success', $response->message);
        }
    }
    public function adminTerminateVirtual($id)
    {
        $set = Settings::first();
        $vcard = Virtual::wherecard_hash($id)->first();
        $balance = Balance::whereuser_id($vcard->user_id)->wherebusiness_id($vcard->business_id)->wherecountry_id($vcard->currency)->first();
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $set->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/terminate");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $balance->amount = $balance->amount + $vcard->amount;
            $balance->save();
            $vcard->delete();
            return back()->with('success', $response->message);
        }
    }
    public function cards()
    {
        $data['title'] = 'Cards';
        return view('user.virtual.index', $data);
    }
    public function buyCard(Request $request)
    {
        if (auth()->guard('user')->user()->business()->live == 0) {
            return back()->with('alert', 'Please activate live mode');
        }
        $currency = explode('*', $request->currency);
        $link = getCountry($currency[0]);
        $charge = ($request->amount * $link->virtual_percent_charge / 100) + $link->virtual_fiat_charge;
        if ($link->max_amount != null) {
            $max = $link->max_amount;
        } else {
            $max = null;
        }
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $max,
            ]
        );
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $balance = Balance::wherecountry_id($link->id)->whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->first();
        if ($balance->amount > $request->amount || $balance->amount == $request->amount) {
            $post = [
                'currency' => strtoupper($link->real->currency),
                'amount' => $request->amount,
                'billing_name' => ucwords(auth()->guard('user')->user()->first_name) . " " . ucwords(auth()->guard('user')->user()->last_name),
                'callback_url' => route('webhook.virtual')
            ];
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->post("https://api.flutterwave.com/v3/virtual-cards", $post);
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                return back()->with('alert', $response->message);
            } else {
                if ($response->status == "success") {
                    $balance->amount = $balance->amount - ($request->amount + $charge);
                    $balance->save();
                    $sav = new Virtual();
                    $exp = explode("-", $response->data->expiration);
                    $sav->user_id = auth()->guard('user')->user()->id;
                    $sav->business_id = auth()->guard('user')->user()->business_id;
                    $sav->first_name = auth()->guard('user')->user()->first_name;
                    $sav->last_name = auth()->guard('user')->user()->last_name;
                    $sav->account_id = $response->data->account_id;
                    $sav->card_hash = $response->data->id;
                    $sav->card_pan = $response->data->card_pan;
                    $sav->masked_card = $response->data->masked_pan;
                    $sav->cvv = $response->data->cvv;
                    $sav->expiration = $exp[1] . '/' . substr($exp[0], -2);
                    $sav->card_type = $response->data->card_type;
                    $sav->name_on_card = $response->data->name_on_card;
                    $sav->callback = $response->data->callback_url;
                    $sav->ref_id = randomNumber(11);
                    $sav->city = $response->data->city;
                    $sav->state = $response->data->state;
                    $sav->zip_code = $response->data->zip_code;
                    $sav->address = $response->data->address_1;
                    $sav->amount = $request->amount;
                    $sav->charge = $charge;
                    $sav->paid = $request->amount + $charge;
                    $sav->currency = $link->id;
                    $sav->save();
                }
            }
            return back()->with('success', 'Card created');
        } else {
            return back()->with('alert', 'Insufficient Funds, please fund your account');
        }
    }
    public function blockVirtual($id)
    {
        $vcard = Virtual::wherecard_hash($id)->first();
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/status/block");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $vcard->status = 2;
            $vcard->save();
            return back()->with('success', $response->message);
        }
    }
    public function unblockVirtual($id)
    {
        $vcard = Virtual::wherecard_hash($id)->first();
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/status/unblock");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $vcard->status = 1;
            $vcard->save();
            return back()->with('success', $response->message);
        }
    }
    public function terminateVirtual($id)
    {
        $vcard = Virtual::wherecard_hash($id)->first();
        $balance = Balance::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wherecountry_id($vcard->currency)->first();
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/terminate");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $balance->amount = $balance->amount + $vcard->amount;
            $balance->save();
            $vcard->delete();
            return back()->with('success', $response->message);
        }
    }
    public function withdrawVirtual(Request $request)
    {
        $vcard = Virtual::wherecard_hash($request->id)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $vcard->amount,
            ]
        );
        if ($validator->fails()) {
            return back()->with('alert', $validator->errors());
        }
        $balance = Balance::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wherecountry_id($vcard->currency)->first();
        $data = [
            'amount' => $request->amount
        ];
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/withdraw", $data);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            return back()->with('alert', $response->message);
        } else {
            $balance->amount = $balance->amount + $request->amount;
            $balance->save();
            $vcard->amount = $vcard->amount - $request->amount;
            $vcard->save();
            return redirect()->route('transactions.virtual', ['id' => $vcard->card_hash])->with('success', $response->message);
        }
    }
    public function fundVirtual(Request $request)
    {
        $vcard = Virtual::wherecard_hash($request->id)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $vcard->getCurrency->virtual_max_amount . '|min:' . $vcard->getCurrency->virtual_min_amount,
            ]
        );
        if ($validator->fails()) {
            return back()->with('alert', $validator->errors());
        }
        $balance = Balance::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wherecountry_id($vcard->currency)->first();
        if ($balance->amount > $request->amount || $balance->amount == $request->amount) {
            $data = [
                'amount' => $request->amount
            ];
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->post("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/fund", $data);
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                return back()->with('alert', $response->message);
            } else {
                if ($response->status == "success") {
                    $balance->amount = $balance->amount - $request->amount;
                    $balance->save();
                    $vcard->amount = $vcard->amount + $request->amount;
                    $vcard->save();
                    return redirect()->route('transactions.virtual', ['id' => $vcard->card_hash])->with('success', $response->message);
                }
            }
        } else {
            return back()->with('alert', 'Insufficient Funds, please fund your account');
        }
    }
    public function transactionsVirtual($id)
    {
        $data['val'] = Virtual::wherecard_hash($id)->whereuser_id(Auth::guard('user')->user()->id)->first();
        $data['title'] = 'Card details';
        $data['log'] = Virtualtransactions::wherecard_hash($id)->whereuser_id(Auth::guard('user')->user()->id)->get();
        return view('user.virtual.transactions', $data);
    }
    public function webhookVirtual(Request $request){
        $set=Settings::find(1);
        $body = @file_get_contents("php://input");
        $signature = (isset($_SERVER['HTTP_VERIF_HASH']) ? $_SERVER['HTTP_VERIF_HASH'] : '');
        if (!$signature) {
            exit();
        }
        $local_signature = $set->secret_hash;
        if( $signature !== $local_signature ){
          exit();
        }
        http_response_code(200);
        $response = json_decode($body);
        if ($response->status == 'successful') {
            $vcard=Virtual::wherecard_hash($response->cardId)->first();
            if($response->Description=="OTP"){
                dispatch(new SendEmail($vcard->user->email, $vcard->user->first_name, 'Transaction OTP', "OTP code;".$response->Otp));
            }
        }
        exit();
    }
}
