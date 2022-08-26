<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Virtual;
use App\Models\Virtualtransactions;
use App\Models\Balance;
use App\Models\User;
use App\Jobs\SendEmail;
use Curl\Curl;
use Illuminate\Support\Facades\Validator;
use App\Models\Settings;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class VirtualController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    //Verify secret key
    public function verifyToken($token)
    {
        if (Business::wheresecret_key($token)->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
    //Get user
    public function getUser($token)
    {
        $business = Business::wheresecret_key($token)->first();
        return User::find($business->user_id);
    }
    //Get all cards
    public function all(Request $request)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            return response()->json([
                'message' => "All Cards", 'status' => 'success', 'data' =>
                Virtual::whereuserId($this->getUser($request->bearerToken())->id)->wherebusinessId($this->getUser($request->bearerToken())->business_id)->get()
            ], 201);
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Get a card
    public function card(Request $request, $id)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            return response()->json([
                'message' => "Card Details", 'status' => 'success', 'data' =>
                Virtual::whereId($id)->whereuserId($this->getUser($request->bearerToken())->id)->first()
            ], 201);
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Get transaction
    public function transactions(Request $request, $card_hash)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            return response()->json([
                'message' => "Card Transactions", 'status' => 'success', 'data' =>
                Virtualtransactions::wherecard_hash($card_hash)->whereuserId($this->getUser($request->bearerToken())->id)->get()
            ], 201);
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Terminate Card
    public function terminate(Request $request, $card_hash)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            $vcard = Virtual::wherecard_hash($card_hash)->first();
            $balance = Balance::whereuser_id($this->getUser($request->bearerToken())->id)->wherebusiness_id($this->getUser($request->bearerToken())->business_id)->wherecountry_id($vcard->currency)->first();
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/terminate");
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                $balance->amount = $balance->amount + $vcard->amount;
                $balance->save();
                $vcard->delete();
                return response()->json(['message' => $response->message, 'status' => 'success', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Block Card
    public function block(Request $request, $card_hash)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            $vcard = Virtual::wherecard_hash($card_hash)->first();
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/status/block");
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                $vcard->status = 2;
                $vcard->save();
                return response()->json(['message' => $response->message, 'status' => 'success', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Unblock Card
    public function unblock(Request $request, $card_hash)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            $vcard = Virtual::wherecard_hash($card_hash)->first();
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->put("https://api.flutterwave.com/v3/virtual-cards/" . $vcard->card_hash . "/status/unblock");
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                $vcard->status = 1;
                $vcard->save();
                return response()->json(['message' => $response->message, 'status' => 'success', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Withdraw from card to balance
    public function withdrawVirtual(Request $request)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            $vcard = Virtual::wherecard_hash($request->card_hash)->first();
            $validator = Validator::make(
                $request->all(),
                [
                    'amount' => 'max:' . $vcard->amount,
                    'card_hash' => 'required'
                ]
            );
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            $balance = Balance::whereuser_id($this->getUser($request->bearerToken())->id)->wherebusiness_id($this->getUser($request->bearerToken())->business_id)->wherecountry_id($vcard->currency)->first();
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
                return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                $balance->amount = $balance->amount + $request->amount;
                $balance->save();
                $vcard->amount = $vcard->amount - $request->amount;
                $vcard->save();
                return response()->json(['message' => $response->message, 'status' => 'suucess', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Fund Card
    public function fund(Request $request)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            $vcard = Virtual::wherecard_hash($request->card_hash)->first();
            $validator = Validator::make(
                $request->all(),
                [
                    'amount' => 'max:' . $vcard->getCurrency->virtual_max_amount . '|min:' . $vcard->getCurrency->virtual_min_amount,
                    'card_hash' => 'required'
                ]
            );
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            $balance = Balance::whereuser_id($this->getUser($request->bearerToken())->id)->wherebusiness_id($this->getUser($request->bearerToken())->business_id)->wherecountry_id($vcard->currency)->first();
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
                    return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
                } else {
                    if ($response->status == "success") {
                        $balance->amount = $balance->amount - $request->amount;
                        $balance->save();
                        $vcard->amount = $vcard->amount + $request->amount;
                        $vcard->save();
                        return response()->json(['message' => $response->message, 'status' => 'success', 'data' => null], 201);
                    }
                }
            } else {
                return response()->json(['message' => 'Insufficient Funds, please fund your account', 'status' => 'success', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
    //Create Card
    public function buyCard(Request $request)
    {
        if ($this->verifyToken($request->bearerToken()) == true) {
            foreach (getAcceptedCountry() as $val) {
                $currency[] = $val->real->currency;
                $country[] = $val->id;
            }
            if (in_array($request->currency, $currency)) {
                $array_key = array_keys($currency, $request->currency);
                $country_id = $country[$array_key[0]];
            } else {
                return response()->json(['message' => 'Invalid currency, ' . $request->currency . ' is not supported', 'status' => 'failed', 'data' => null], 400);
            }
            $link = getCountry($country_id);
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
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            $balance = Balance::wherecountry_id($link->id)->whereuser_id($this->getUser($request->bearerToken())->id)->wherebusiness_id($this->getUser($request->bearerToken())->business_id)->first();
            if ($balance->amount > $request->amount || $balance->amount == $request->amount) {
                $post = [
                    'currency' => strtoupper($link->real->currency),
                    'amount' => $request->amount,
                    'billing_name' => ucwords(auth()->user()->first_name) . " " . ucwords(auth()->user()->last_name),
                    'callback_url' => $request->callback_url
                ];
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post("https://api.flutterwave.com/v3/virtual-cards", $post);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
                } else {
                    if ($response->status == "success") {
                        $balance->amount = $balance->amount - ($request->amount + $charge);
                        $balance->save();
                        $sav = new Virtual();
                        $exp = explode("-", $response->data->expiration);
                        $sav->user_id = $this->getUser($request->bearerToken())->id;
                        $sav->business_id = $this->getUser($request->bearerToken())->business_id;
                        $sav->first_name = auth()->user()->first_name;
                        $sav->last_name = auth()->user()->last_name;
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
                return response()->json(['message' => 'Card created', 'status' => 'success', 'data' => $sav], 201);
            } else {
                return response()->json(['message' => 'Insufficient Funds, please fund your account', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }
}
