<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Exttransfer;
use App\Models\Transactions;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Settings;
use App\Models\Shipstate;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPaymentEmail;
use Curl\curl;
use Jenssegers\Agent\Agent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Propaganistas\LaravelPhone\PhoneNumber;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function supportedCountries()
    {
        foreach (getAcceptedCountry() as $val) {
            $code[] = $val->country_id;
            $name[] = $val->real->currency;
        }
        return response()->json(
            ['currency_code' => $code, 'currency_name' => $name],
            200
        );
    } 
    public function getCountry()
    {
        return response()->json(
            ['message' => 'success', 'data' => getAllCountry()],
            201
        );
    }
    public function getState($id)
    {
        return response()->json(
            ['message' => 'success', 'data' => Shipstate::wherecountry_id($id)->orderby('name', 'asc')->get()],
            201
        );
    }
    public function generate_token(Request $request)
    {
        if (auth()->guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::whereid(auth()->guard('user')->user()->id)->first();
            $token = $user->createToken('my-app-token')->plainTextToken;
            $user->api_token = $token;
            $user->save();
            return response([
                'token' => $token,
            ], 201);
        } else {
            return response()->json(['message' => 'Invalid credentials', 'status' => 'failed', 'data' => null], 404);
        }
    }
    public function paymentCancel($id)
    {
        $link = Exttransfer::whereref_id($id)->first();
        if ($link->return_url == null) {
            $link->status == 2;
            $link->save();
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('Payment Cancelled');
        } else {
            return redirect()->away($link->return_url);
        }
    }
    public function htmlPay(Request $request)
    {
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->currency;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'email' => ['required', 'max:255'],
                'first_name' => ['required', 'max:100'],
                'last_name' => ['required', 'max:100'],
                'callback_url' => ['url'],
                'return_url' => ['url'],
                'logo' => ['url'],
                'tx_ref' => ['required', 'string'],
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'secret_key' => ['required', 'max:50', 'string'],
                'meta' => ['array'],
            ]);
            if ($validator->fails()) {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors($validator->errors());
            }
            $cc = User::wheresecret_key($request->secret_key)->count();
            if ($cc == 1) {
                $mode = 1;
                $user = User::wheresecret_key($request->secret_key)->first();
            } else {
                $test = User::wheretest_secret_key($request->secret_key)->count();
                if ($test == 1) {
                    $mode = 0;
                    $user = User::wheretest_secret_key($request->secret_key)->first();
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Invalid secret key');
                }
            }
            if ($user->status == 0) {
                $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->id)->count();
                if ($used == 0) {
                    $sav = new Exttransfer();
                    $sav->ref_id = randomNumber(11);
                    $sav->user_id = $user->id;
                    $sav->amount = $request->amount;
                    $sav->callback_url = $request->callback_url;
                    $sav->return_url = $request->return_url;
                    $sav->tx_ref = $request->tx_ref;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->currency = $country_id;
                    $sav->title = $request->title;
                    $sav->description = $request->description;
                    $sav->logo = $request->logo;
                    $sav->meta = json_encode($request->meta);
                    $sav->mode = $mode;
                    $sav->save();
                    return redirect()->route('checkout.url', ['id' => $sav->ref_id]);
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Transaction reference has been used before');
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors('User can\'t receive payments');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.payment', $data)->withErrors('Invalid currency, ' . $request->currency . ' is not supported');
        }
    }
    public function wordpressPay(Request $request)
    {
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->id;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'email' => ['required', 'max:255'],
                'first_name' => ['required', 'max:100'],
                'last_name' => ['required', 'max:100'],
                'callback_url' => ['url'],
                'return_url' => ['url'],
                'logo' => ['url'],
                'tx_ref' => ['required', 'string'],
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'secret_key' => ['required', 'max:50', 'string'],
                'meta' => ['array'],
            ]);
            if ($validator->fails()) {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors($validator->errors());
            }
            $cc = User::wheresecret_key($request->secret_key)->count();
            if ($cc == 1) {
                $mode = 1;
                $user = User::wheresecret_key($request->secret_key)->first();
            } else {
                $test = User::wheretest_secret_key($request->secret_key)->count();
                if ($test == 1) {
                    $mode = 0;
                    $user = User::wheretest_secret_key($request->secret_key)->first();
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Invalid secret key');
                }
            }
            if ($user->status == 0) {
                $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->id)->count();
                if ($used == 0) {
                    $sav = new Exttransfer();
                    $sav->ref_id = randomNumber(11);
                    $sav->user_id = $user->id;
                    $sav->amount = $request->amount;
                    $sav->callback_url = $request->callback_url;
                    $sav->return_url = $request->return_url;
                    $sav->tx_ref = $request->tx_ref;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->currency = $country_id;
                    $sav->title = $request->title;
                    $sav->description = $request->description;
                    $sav->logo = $request->logo;
                    $sav->meta = json_encode($request->meta);
                    $sav->mode = $mode;
                    $sav->save();
                    return redirect()->route('checkout.url', ['id' => $sav->ref_id]);
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Transaction reference has been used before');
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors('User can\'t receive payments');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.payment', $data)->withErrors('Invalid currency, ' . $request->currency . ' is not supported');
        }
    }
    public function jsPay(Request $request)
    {
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->currency;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'customer.email' => ['required', 'max:255'],
                'customer.first_name' => ['required', 'max:100'],
                'customer.last_name' => ['required', 'max:100'],
                'callback_url' => ['url'],
                'return_url' => ['url'],
                'customization.logo' => ['url'],
                'tx_ref' => ['required', 'string'],
                'customization.title' => ['required', 'string', 'max:100'],
                'customization.description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'secret_key' => ['required', 'max:50', 'string'],
                'meta' => ['array'],
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            $cc = User::wheresecret_key($request->secret_key)->count();
            if ($cc == 1) {
                $mode = 1;
                $user = User::wheresecret_key($request->secret_key)->first();
            } else {
                $test = User::wheretest_secret_key($request->secret_key)->count();
                if ($test == 1) {
                    $mode = 0;
                    $user = User::wheretest_secret_key($request->secret_key)->first();
                } else {
                    return response()->json(['message' => 'Invalid secret key', 'status' => 'failed', 'data' => null], 400);
                }
            }
            if ($user->status == 0) {
                $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->id)->count();
                if ($used == 0) {
                    $sav = new Exttransfer();
                    $sav->ref_id = randomNumber(11);
                    $sav->user_id = $user->id;
                    $sav->amount = $request->amount;
                    $sav->callback_url = $request->callback_url;
                    $sav->return_url = $request->return_url;
                    $sav->tx_ref = $request->tx_ref;
                    $sav->email = $request->customer['email'];
                    $sav->first_name = $request->customer['first_name'];
                    $sav->last_name = $request->customer['last_name'];
                    $sav->currency = $country_id;
                    $sav->title = $request->customization['title'];
                    $sav->description = $request->customization['description'];
                    if (array_key_exists('logo', $request->customization)) {
                        $sav->logo = $request->customization['logo'];
                    }
                    $sav->meta = json_encode($request->meta);
                    $sav->mode = $mode;
                    $sav->save();
                    $response = [
                        'checkout_url' => route('checkout.url', ['id' => $sav->ref_id]),
                    ];
                    return response()->json(['message' => 'Payment link created', 'status' => 'success', 'data' => $response], 201);
                } else {
                    return response()->json(['message' => 'Transaction reference has been used before', 'status' => 'failed', 'data' => null], 201);
                }
            } else {
                return response()->json(['message' => 'User can\'t receive payments', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid currency, ' . $request->currency . ' is not supported', 'status' => 'failed', 'data' => null], 400);
        }
    }
    public function payment(Request $request)
    {
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->currency;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $customMessages = [
                'customization.logo.url' => 'Logo must be a url',
            ];
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'email' => ['required', 'max:255'],
                'first_name' => ['required', 'max:100'],
                'last_name' => ['required', 'max:100'],
                'callback_url' => ['url'],
                'return_url' => ['url'],
                'tx_ref' => ['required', 'string'],
                'customization.title' => ['required', 'string', 'max:100'],
                'customization.description' => ['required', 'string', 'max:255'],
                'customization.logo' => ['url'],
                'currency' => ['required', 'max:3', 'string'],
                'secret_key' => ['required', 'max:50', 'string'],
                'meta' => ['array'],
            ], $customMessages);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            if (auth()->user()->status == 0) {
                $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id(auth()->user()->id)->count();
                if ($request->secret_key != auth()->user()->test_secret_key && $request->secret_key != auth()->user()->secret_key) {
                    return response()->json(['message' => 'Invalid secret key', 'status' => 'failed', 'data' => null], 400);
                } else {
                    if ($request->secret_key == auth()->user()->test_secret_key) {
                        $mode = 0;
                    } else {
                        $mode = 1;
                    }
                }
                if ($used == 0) {
                    $sav = new Exttransfer();
                    $sav->ref_id = randomNumber(11);
                    $sav->user_id = auth()->user()->id;
                    $sav->amount = $request->amount;
                    $sav->callback_url = $request->callback_url;
                    $sav->return_url = $request->return_url;
                    $sav->tx_ref = $request->tx_ref;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->currency = $country_id;
                    $sav->title = $request->customization['title'];
                    $sav->description = $request->customization['description'];
                    if (array_key_exists('logo', $request->customization)) {
                        $sav->logo = $request->customization['logo'];
                    }
                    $sav->meta = json_encode($request->meta);
                    $sav->mode = $mode;
                    $sav->save();
                    $response = [
                        'checkout_url' => route('checkout.url', ['id' => $sav->ref_id]),
                    ];
                    return response()->json(['message' => 'Payment link created', 'status' => 'success', 'data' => $response], 201);
                } else {
                    return response()->json(['message' => 'Transaction reference has been used before', 'status' => 'failed', 'data' => null], 400);
                }
            } else {
                return response()->json(['message' => 'User can\'t receive payments', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid currency, ' . $request->currency . ' is not supported', 'status' => 'failed', 'data' => null], 400);
        }
    }
    public function paymentLink($id, $type = null)
    {
        $data['link'] = $link = Exttransfer::whereref_id($id)->first();
        $data['title'] = 'Payment';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->status == 0) {
                if ($link->mode == 1) {
                    if ($link->user->kyc_status != "DECLINED") {
                        return view('user.merchant.live', $data);
                    }
                } else {
                    return view('user.merchant.test', $data);
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.error', $data)->withErrors('This payment has been cancelled');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }
    public function paymentSubmit(Request $request, $id)
    {
        $link = Exttransfer::whereref_id($id)->first();
        $receiver = User::whereid($link->user->id)->first();
        if (getTransaction($link->id, $link->user_id) == null) {
            $m_charge = ($link->amount * $link->getCurrency->percent_charge / 100 + ($link->getCurrency->fiat_charge));
            if ($request->type == 'card') {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                        'expiry' => 'required',
                        'card_number' => 'required',
                        'cvv' => 'required|string|max:4',
                    ]
                );
                if ($validator->fails()) {
                    return back()->with('errors', $validator->errors());
                }
                $expiry = explode('/', $request->expiry);
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 1;
                    $sav->mode = 1;
                    if ($link->user->charges == 0) {
                        $sav->amount = $request->amount - $m_charge;
                    } else {
                        $sav->amount = $request->amount + $m_charge;
                    }
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    if ($receiver->charges == 1) {
                        $sav->client = 1;
                    }
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    if ($link->user->charges == 0) {
                        $sav->amount = $request->amount - $m_charge;
                    } else {
                        $sav->amount = $request->amount + $m_charge;
                    }
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                cardError($sav->trace_id, "Attempted to pay with card", "log");
                //Store session
                session::put('card_number', $request->card_number);
                session::put('expiry', $request->expiry);
                session::put('expiry_month', $expiry[0]);
                session::put('expiry_year', $expiry[1]);
                session::put('cvv', $request->cvv);
                session::put('first_name', $request->first_name);
                session::put('last_name', $request->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $request->email);
                $data = [
                    'amount' => $request->amount,
                    'card_number' => str_replace(' ', '', $request->card_number),
                    'cvv' => $request->cvv,
                    'expiry_month' => $expiry[0],
                    'expiry_year' => $expiry[1],
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $request->email,
                    'fullname' => $request->first_name . ' ' . $request->last_name,
                    'tx_ref' => $sav->ref_id,
                    'redirect_url' => route('webhook.card', ['id' => $sav->ref_id])
                ];
                $payload = [
                    'client' => encrypt3Des(json_encode($data), $this->settings->encrypt)
                ];
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post("https://api.flutterwave.com/v3/charges?type=card", $payload);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if ($response != null) {
                        if ($response->message == "Incorrect PIN") {
                            cardError($sav->trace_id, $response->message, "error");
                            $sav->auth_type = "pin";
                            $sav->save();
                            return redirect()->route('payment.pin', ['id' => $sav->ref_id]);
                        }
                        cardError($sav->trace_id, $response->message, "error");
                        return back()->with('alert', $response->message);
                    } else {
                        cardError($sav->trace_id, "No response from server", "error");
                        return back()->with('alert', 'An Error Occured');
                    }
                } else {
                    if ($response->meta->authorization->mode == "pin") {
                        cardError($sav->trace_id, "Authentication required: Pin", "log");
                        $sav->auth_type = "pin";
                        $sav->save();
                        return redirect()->route('payment.pin', ['id' => $sav->ref_id]);
                    }
                    if ($response->meta->authorization->mode == "avs_noauth") {
                        cardError($sav->trace_id, "Authentication required: AVS", "log");
                        $sav->auth_type = "avs_noauth";
                        $sav->save();
                        return redirect()->route('payment.avs', ['id' => $sav->ref_id]);
                    }
                    if ($response->meta->authorization->mode == "redirect") {
                        cardError($sav->trace_id, "Authentication required: 3DS", "log");
                        cardError($sav->trace_id, "Third-party authentication window opened", "log");
                        $sav->auth_type = "3DS";
                        $sav->save();
                        $sav->card_reference = $response->data->flw_ref;
                        $sav->trans_id = $response->data->id;
                        $sav->card_number = $response->data->card->first_6digits . $response->data->card->last_4digits;
                        $sav->card_type = strtolower($response->data->card->type);
                        $sav->save();
                        return redirect()->away($response->meta->authorization->redirect);
                    }
                }
                //Balance
                $balance = Balance::whereuser_id($link->user->id)->wherecountry_id($link->getCurrency->id)->first();
                if ($link->user->charges == 0) {
                    $balance->test = $balance->test + $request->amount - $m_charge;
                } else {
                    $balance->test = $balance->test + $request->amount;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $link->user->id;
                $audit->trx = $sav->ref_id;
                $audit->log = 'Received test payment ' . $link->ref_id;
                $audit->save();
                //Notify users
                if ($this->settings->email_notify == 1) {
                    dispatch(new SendPaymentEmail($link->ref_id, $sav->ref_id));
                }
                //Send Webhook
                if ($link->user->receive_webhook == 1) {
                    if ($link->user->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                } else {
                    return back()->with('alert', 'Payment failed');
                }
            }
            if ($request->type == 'test') {
                $rules = [
                    'status.required' => 'Please select a transaction status',
                ];
                $validator = Validator::make(
                    $request->all(),
                    [
                        'status' => 'required',
                    ],
                    $rules
                );
                if ($validator->fails()) {
                    return back()->with('errors', $validator->errors());
                }
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 2;
                $sav->mode = 0;
                if ($link->user->charges == 0) {
                    $sav->amount = $link->amount - $m_charge;
                } else {
                    $sav->amount = $link->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $link->email;
                $sav->first_name = $link->first_name;
                $sav->last_name = $link->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'test';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                if ($receiver->charges == 1) {
                    $sav->client = 1;
                }
                $sav->save();
                //Balance
                $balance = Balance::whereuser_id($link->user->id)->wherecountry_id($link->getCurrency->id)->first();
                if ($link->user->charges == 0) {
                    $balance->test = $balance->test + $request->amount - $m_charge;
                } else {
                    $balance->test = $balance->test + $request->amount;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $link->user->id;
                $audit->trx = $sav->ref_id;
                $audit->log = 'Received test payment ' . $link->ref_id;
                $audit->save();
                //Notify users
                if ($this->settings->email_notify == 1) {
                    dispatch(new SendPaymentEmail($link->ref_id, $sav->ref_id));
                }
                //Send Webhook
                if ($link->user->receive_webhook == 1) {
                    if ($link->user->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    if ($link->callback_url != null) {
                        return redirect()->away($link->callback_url . '?tx_ref=' . $link->tx_ref);
                    }
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                } else {
                    return back()->with('alert', 'Payment failed');
                }
            }
        } else {
            return back()->with('alert', 'Session expired');
        }
    }
    public function verifyPayment($tx_ref, $public_key)
    {
        $check = Exttransfer::wheretx_ref($tx_ref)->count();
        if ($check == 0) {
            return response()->json(['message' => 'Invalid transaction', 'status' => 'failed', 'data' => null], 404);
        } else {
            $link = Exttransfer::wheretx_ref($tx_ref)->first();
            $user = User::whereid($link->user_id)->first();
            if ($link->mode == 0) {
                $pb = $user->test_public_key;
                if ($public_key != $user->test_public_key && $public_key == $user->public_key) {
                    return response()->json(['message' => 'live public keys can\'t be used to verify a test transaction', 'status' => 'failed', 'data' => null], 400);
                }
            } else {
                $pb = $user->public_key;
                if ($public_key != $user->public_key && $public_key == $user->test_public_key) {
                    return response()->json(['message' => 'test public keys can\'t be used to verify a live transaction', 'status' => 'failed', 'data' => null], 400);
                }
            }
            if ($pb !== $public_key) {
                return response()->json(['message' => 'Invalid Public Key', 'status' => 'failed', 'data' => null], 400);
            } else {
                if (getTransaction($link->id, $link->user_id) == null) {
                    return response()->json(['message' => 'Payment not submitted', 'status' => 'null', 'data' => null], 404);
                } else {
                    if ($link->getTransaction()->mode == 1) {
                        $mode = "live";
                    } else {
                        $mode = "test";
                    }
                    if ($link->getTransaction()->status == 0) {
                        $status = "pending";
                    } elseif ($link->getTransaction()->status == 1) {
                        $status = "success";
                    } elseif ($link->getTransaction()->status == 3) {
                        $status = "refunded";
                    } elseif ($link->getTransaction()->status == 4) {
                        $status = "reversed";
                    } else {
                        $status = "failed/cancelled";
                    }
                    if ($link->getTransaction()->client == 1) {
                        $amount = $link->getTransaction()->amount - $link->getTransaction()->charge;
                    } else {
                        $amount = $link->getTransaction()->amount;
                    }
                    $data = [
                        'first_name' => $link->getTransaction()->first_name,
                        'last_name' => $link->getTransaction()->last_name,
                        'email' => $link->getTransaction()->email,
                        'currency' => $link->getCurrency->real->currency,
                        'amount' => number_format($amount, 2),
                        'charge' => number_format($link->getTransaction()->charge, 2),
                        'mode' => $mode,
                        'type' => "API",
                        'status' => $status,
                        'reference' => $link->getTransaction()->ref_id,
                        'tx_ref' => $link->tx_ref,
                        'customization' => [
                            'title' => $link->title,
                            'description' => $link->description,
                            'logo' => $link->logo
                        ],
                        'meta' => json_decode($link->meta),
                        'created_at' => $link->created_at,
                        'updated_at' => $link->updated_at
                    ];
                    return response()->json(['message' => 'Payment details', 'status' => $status, 'data' => $data], 201);
                }
            }
        }
    }
    public function popupSubmit(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'crf' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => 'An error occured', 'status' => 'failed', 'data' => null], 400);
        }
        if ($request->crf == 2) {
            $link = Exttransfer::whereref_id($id)->first();
            $m_charge = ($link->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        }
        $receiver = User::whereid($link->user->id)->first();
        if ($link->getCurrency->max_amount != null) {
            $max = $link->getCurrency->max_amount;
        } else {
            $max = null;
        }
        if ($request->type == 'card') {
            if ($request->crf == 2) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'expiry' => 'required',
                        'card_number' => 'required',
                        'cvv' => 'required|string|max:4',
                    ]
                );
            } 
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            $expiry = explode('/', $request->expiry);
            if ($request->crf == 2) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 2;
                    $sav->mode = 1;
                    if ($link->user->charges == 0) {
                        $sav->amount = $link->amount - $m_charge;
                    } else {
                        $sav->amount = $link->amount + $m_charge;
                    }
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    if ($receiver->charges == 1) {
                        $sav->client = 1;
                    }
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return response()->json(['message' => 'Session has expired for last transaction, please try again', 'status' => 'failed', 'data' => null], 400);
                    }
                    if ($link->user->charges == 0) {
                        $sav->amount = $link->amount - $m_charge;
                    } else {
                        $sav->amount = $link->amount + $m_charge;
                    }
                    $sav->email = $link->email;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                cardError($sav->trace_id, "Attempted to pay with card", "log");
                $data = [
                    'amount' => $link->amount,
                    'card_number' => str_replace(' ', '', $request->card_number),
                    'cvv' => $request->cvv,
                    'expiry_month' => $expiry[0],
                    'expiry_year' => $expiry[1],
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $link->email,
                    'fullname' => $link->first_name . ' ' . $link->last_name,
                    'tx_ref' => $sav->ref_id,
                    'redirect_url' => url().'api/webhook-card/'.$sav->ref_id
                ];
                $payload = [
                    'client' => encrypt3Des(json_encode($data), $this->settings->encrypt)
                ];
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post("https://api.flutterwave.com/v3/charges?type=card", $payload);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if ($response != null) {
                        if ($response->message == "Incorrect PIN") {
                            cardError($sav->trace_id, $response->message, "error");
                            $sav->auth_type = "pin";
                            $sav->save();
                            return response()->json(['message' => 'Pin needed', 'status' => 'pin', 'data' => $sav->ref_id], 201);
                        }
                        cardError($sav->trace_id, $response->message, "error");
                        return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
                    } else {
                        cardError($sav->trace_id, "No response from server", "error");
                        return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
                    }
                } else {
                    if (array_key_exists('meta', (array)$curl->response)) {
                        if ($response->meta->authorization->mode == "pin") {
                            cardError($sav->trace_id, "Authentication required: Pin", "log");
                            $sav->auth_type = "pin";
                            $sav->save();
                            return response()->json(['message' => 'Pin needed', 'status' => 'pin', 'data' => $sav->ref_id], 201);
                        }
                        if ($response->meta->authorization->mode == "avs_noauth") {
                            cardError($sav->trace_id, "Authentication required: AVS", "log");
                            $sav->auth_type = "avs_noauth";
                            $sav->save();
                            return response()->json(['message' => 'Avs needed', 'status' => 'avs', 'data' => $sav->ref_id], 201);
                        }
                        if ($response->meta->authorization->mode == "redirect") {
                            cardError($sav->trace_id, "Authentication required: 3DS", "log");
                            cardError($sav->trace_id, "Third-party authentication window opened", "log");
                            $sav->auth_type = "3DS";
                            $sav->save();
                            $sav->card_reference = $response->data->flw_ref;
                            $sav->trans_id = $response->data->id;
                            $sav->card_number = $response->data->card->first_6digits . $response->data->card->last_4digits;
                            $sav->card_type = strtolower($response->data->card->type);
                            $sav->save();
                            return redirect()->away($response->meta->authorization->redirect);
                        }
                    }else{
                        if ($response->data->status == "successful") {
                            if ($sav->payment_type == "card") {
                                cardError($sav->trace_id, "Successfully paid with card", "log");
                            }
                            //Balance
                            $sav->status = 1;
                            $sav->completed_at = Carbon::now();
                            if ((new Agent())->isDesktop()) {
                                $sav->device = "tv";
                            }
                            if ((new Agent())->isMobile()) {
                                $sav->device = "mobile";
                            }
                            if ((new Agent())->isTablet()) {
                                $sav->device = "tablet";
                            }
                            if ($sav->payment_method == "card") {
                                $sav->card_issuer = $response->data->card->issuer;
                                $sav->card_country = $response->data->card->country;
                            }
                            $sav->card_reference = $response->data->flw_ref;
                            $sav->trans_id = $response->data->id;
                            $sav->card_number = $response->data->card->first_6digits . $response->data->card->last_4digits;
                            $sav->card_type = strtolower($response->data->card->type);
                            $sav->save();
                            session::forget('trace_id');
                            session::forget('card_number');
                            session::forget('expiry');
                            session::forget('expiry_month');
                            session::forget('expiry_year');
                            session::forget('cvv');
                            session::forget('first_name');
                            session::forget('last_name');
                            session::forget('tx_ref');
                            session::forget('email');
                            $balance = Balance::whereuser_id($sav->receiver_id)->wherecountry_id($sav->currency)->first();
                            if ($sav->client == 0) {
                                $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                            } else {
                                $balance->amount = $balance->amount + $sav->amount;
                            }
                            $balance->save();
                            //Save Audit Log
                            $audit = new Audit();
                            $audit->user_id = $sav->receiver_id;
                            $audit->trx = $sav->ref_id;
                            if ($sav->type == 2) {
                                $audit->log = 'Received test payment ' . $sav->api->ref_id;
                            } elseif ($sav->type == 1) {
                                $audit->log = 'Received test payment ' . $sav->link->ref_id;
                            } elseif ($sav->type == 4) {
                                $audit->log = 'Received test payment ' . $sav->balance->ref_id;
                            }
                            $audit->save();
                            if ($sav->type == 1) {
                                //Notify users
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->link->user->receive_webhook == 1) {
                                    if ($sav->link->user->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } elseif ($sav->type == 4) {
                                //Notify users
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->balance->user->receive_webhook == 1) {
                                    if ($sav->balance->user->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } else {
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->api->user->receive_webhook == 1) {
                                    if ($sav->api->user->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            }
                            if ($sav->type == 2) {
                                if ($sav->api->callback_url != null) {
                                    return redirect()->away($sav->api->callback_url . '?tx_ref=' . $sav->api->tx_ref);
                                }
                                return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                            }
                            return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                        }
                    }
                }
            }
        }
        if ($request->type == 'test') {
            if (getTransaction($link->id, $link->user_id) != null) {
                return response()->json(['message' => 'Session expired', 'status' => 'failed', 'data' => null], 400);
            }
            $validator = Validator::make(
                $request->all(),
                [
                    'status' => 'required',
                ],
                [
                    'status.required' => 'Please select a transaction status',
                ]
            );
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            if ($request->crf == 1) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 1;
                $sav->mode = 0;
                if ($link->user->charges == 0) {
                    $sav->amount = $request->amount - $m_charge;
                } else {
                    $sav->amount = $request->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $request->email;
                $sav->first_name = $request->first_name;
                $sav->last_name = $request->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'test';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                if ($receiver->charges == 1) {
                    $sav->client = 1;
                }
                $sav->save();
                //Balance
                $balance = Balance::whereuser_id($link->user->id)->wherecountry_id($link->getCurrency->id)->first();
                if ($link->user->charges == 0) {
                    $balance->test = $balance->test + $request->amount - $m_charge;
                } else {
                    $balance->test = $balance->test + $request->amount;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $link->user->id;
                $audit->trx = $sav->ref_id;
                $audit->log = 'Received test payment ' . $link->ref_id;
                $audit->save();
                //Notify users
                if ($this->settings->email_notify == 1) {
                    dispatch(new SendPaymentEmail($link->ref_id, $sav->ref_id));
                }
                //Send Webhook
                if ($link->user->receive_webhook == 1) {
                    if ($link->user->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                } else {
                    return response()->json(['message' => 'Payment failed', 'status' => 'failed', 'data' => null], 400);
                }
            } else {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 2;
                $sav->mode = 0;
                if ($link->user->charges == 0) {
                    $sav->amount = $link->amount - $m_charge;
                } else {
                    $sav->amount = $link->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $link->email;
                $sav->first_name = $link->first_name;
                $sav->last_name = $link->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'test';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                if ($receiver->charges == 1) {
                    $sav->client = 1;
                }
                $sav->save();
                //Balance
                $balance = Balance::whereuser_id($link->user->id)->wherecountry_id($link->getCurrency->id)->first();
                if ($link->user->charges == 0) {
                    $balance->test = $balance->test + $request->amount - $m_charge;
                } else {
                    $balance->test = $balance->test + $request->amount;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $link->user->id;
                $audit->trx = $sav->ref_id;
                $audit->log = 'Received test payment ' . $link->ref_id;
                $audit->save();
                //Notify users
                if ($this->settings->email_notify == 1) {
                    dispatch(new SendPaymentEmail($link->ref_id, $sav->ref_id));
                }
                //Send Webhook
                if ($link->user->receive_webhook == 1) {
                    if ($link->user->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    if ($link->callback_url != null) {
                        return redirect()->away($link->callback_url . '?tx_ref=' . $link->tx_ref);
                    }
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                } else {
                    return response()->json(['message' => 'Payment failed', 'status' => 'failed', 'data' => null], 400);
                }
            }
        }
        if ($request->type == 'bank') {
            if ($request->crf == 2) {
                if (getTransaction($link->id, $link->user_id) != null) {
                    return response()->json(['message' => 'Session expired', 'status' => 'failed', 'data' => null], 400);
                }
            }
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            if ($request->crf == 2) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 2;
                $sav->mode = 0;
                if ($link->user->charges == 0) {
                    $sav->amount = $link->amount - $m_charge;
                } else {
                    $sav->amount = $link->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $link->email;
                $sav->first_name = $link->first_name;
                $sav->last_name = $link->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'test';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                if ($receiver->charges == 1) {
                    $sav->client = 1;
                }
                $sav->save();
                $authToken = base64_encode($link->getCurrency->auth_key . ':' . $link->getCurrency->auth_secret);
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Basic ' . $authToken);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->get("https://api.yapily.com/institutions");
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    return response()->json(['message' => $response->error->status . '-' . $response->error->message, 'status' => 'failed', 'data' => null], 400);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 2;
                    $data['reference'] = $sav->ref_id;
                    return response()->json(['message' => 'institutions', 'status' => 'failed', 'data' => $data], 201);
                }
            } 
        }
        if ($request->type == 'mobile_money') {
            if ($request->crf == 2) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'mobile' => 'required',
                    ]
                );
            }
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
            $mobile = $request->mobile;
            if ($request->crf == 2) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 2;
                    $sav->mode = 1;
                    if ($link->user->charges == 0) {
                        $sav->amount = $link->amount - $m_charge;
                    } else {
                        $sav->amount = $link->amount + $m_charge;
                    }
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->mobile = $request->mobile;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'mobile';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    if ($receiver->charges == 1) {
                        $sav->client = 1;
                    }
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return response()->json(['message' => 'Session has expired for last transaction, please try again', 'status' => 'failed', 'data' => null], 400);
                    }
                    if ($link->user->charges == 0) {
                        $sav->amount = $link->amount - $m_charge;
                    } else {
                        $sav->amount = $link->amount + $m_charge;
                    }
                    $sav->email = $link->email;
                    $sav->mobile = $request->mobile;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                $data = [
                    'amount' => $link->amount,
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $link->email,
                    'phone_number' => $request->mobile,
                    'fullname' => $link->first_name . ' ' . $link->last_name,
                    'tx_ref' => $sav->ref_id,
                    'redirect_url' => route('webhook.card', ['id' => $sav->ref_id])
                ];
                if ($link->getCurrency->real->currency == "RWF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_rwanda";
                } else if ($link->getCurrency->real->currency == "KES") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mpesa";
                } else if ($link->getCurrency->real->currency == "ZMW") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_zambia";
                } else if ($link->getCurrency->real->currency == "UGX") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_uganda";
                } else if ($link->getCurrency->real->currency == "GHS") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_ghana";
                } else if ($link->getCurrency->real->currency == "ZAR") {
                    $url = "https://api.flutterwave.com/v3/charges?type=ach_payment";
                } else if ($link->getCurrency->real->currency == "XAF" || $link->getCurrency->real->currency == "XOF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_franco";
                }
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post($url, $data);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if ($response != null) {
                        return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
                    } else {
                        return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
                    }
                } else {
                    if ($link->getCurrency->real->currency == "KES") {
                        $curl = new Curl();
                        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                        $curl->setHeader('Content-Type', 'application/json');
                        $curl->get("https://api.flutterwave.com/v3/transactions/" . $response->data->id . "/verify");
                        $verify_response = $curl->response;
                        $curl->close();
                        if ($curl->error) {
                            if ($verify_response != null) {
                                cardError($sav->trace_id, $verify_response->message, "error");
                                return response()->json(['message' => $verify_response->message, 'status' => 'failed', 'data' => null], 400);
                            } else {
                                cardError($sav->trace_id, "No response from server", "error");
                                return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
                            }
                        } else {
                            if ($verify_response->data->status == "successful") {
                                $sav->status = 1;
                                $sav->completed_at = Carbon::now();
                                $sav->save();
                                $balance = Balance::whereuser_id($sav->receiver_id)->wherecountry_id($sav->currency)->first();
                                if ($sav->client == 0) {
                                    $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                                } else {
                                    $balance->amount = $balance->amount + $sav->amount;
                                }
                                $balance->save();
                                $audit = new Audit();
                                $audit->user_id = $sav->receiver_id;
                                $audit->trx = $sav->ref_id;
                                $audit->log = 'Received test payment ' . $sav->link->ref_id;
                                $audit->save();
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                                }
                                if ($sav->link->user->receive_webhook == 1) {
                                    if ($sav->link->user->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                                return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                            }
                        }
                    } else {
                        if ($response->meta->authorization->mode == "redirect") {
                            return redirect()->away($response->meta->authorization->redirect);
                        }
                    }
                }
            }
        }
    }
    public function PinSubmit(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pin' => 'required|string|max:4',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
        }
        $sav = Transactions::whereref_id($id)->first();
        $card_number = session::get('card_number');
        $expiry_month = session::get('expiry_month');
        $expiry_year = session::get('expiry_year');
        $cvv = session::get('cvv');
        $first_name = session::get('first_name');
        $last_name = session::get('last_name');
        $email = session::get('email');
        $data = [
            'amount' => $sav->amount,
            'card_number' => str_replace(' ', '', $card_number),
            'cvv' => $cvv,
            'expiry_month' => $expiry_month,
            'expiry_year' => $expiry_year,
            'currency' => $sav->getCurrency->real->currency,
            'email' => $email,
            'fullname' => $first_name . ' ' . $last_name,
            'tx_ref' => $sav->ref_id,
            'redirect_url' => route('webhook.card', ['id' => $sav->ref_id]),
            'authorization' => [
                'mode' => 'pin',
                'pin' => $request->pin
            ]
        ];
        $payload = [
            'client' => encrypt3Des(json_encode($data), $this->settings->encrypt)
        ];
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post("https://api.flutterwave.com/v3/charges?type=card", $payload);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            if ($response != null) {
                cardError($sav->trace_id, $response->message, "error");
                return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            $sav->card_reference = $response->data->flw_ref;
            $sav->trans_id = $response->data->id;
            $sav->card_number = $response->data->card->first_6digits . $response->data->card->last_4digits;
            $sav->card_type = strtolower($response->data->card->type);
            $sav->save();
            if ($response->meta->authorization->mode == "redirect") {
                cardError($sav->trace_id, "Authentication required: 3DS", "log");
                cardError($sav->trace_id, "Third-party authentication window opened", "log");
                return redirect()->away($response->meta->authorization->redirect);
            } elseif ($response->meta->authorization->mode == "otp") {
                cardError($sav->trace_id, "Authentication required: OTP", "log");
                return response()->json(['message' => $response->data->processor_response, 'status' => 'otp', 'data' => $sav->ref_id], 201);
            }
        }
    }
    public function AvsSubmit(Request $request, $id)
    {
        $country = explode('*', $request->country);
        $validator = Validator::make(
            $request->all(),
            [
                'address' => 'required|string',
                'country' => 'required|string',
                'state' => 'required|string',
                'city' => 'required|string',
                'zip_code' => 'required|string|max:6',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
        }
        $sav = Transactions::whereref_id($id)->first();
        $card_number = session::get('card_number');
        $expiry_month = session::get('expiry_month');
        $expiry_year = session::get('expiry_year');
        $cvv = session::get('cvv');
        $first_name = session::get('first_name');
        $last_name = session::get('last_name');
        $email = session::get('email');
        $data = [
            'amount' => $sav->amount,
            'card_number' => str_replace(' ', '', $card_number),
            'cvv' => $cvv,
            'expiry_month' => $expiry_month,
            'expiry_year' => $expiry_year,
            'currency' => $sav->link->getCurrency->real->currency,
            'email' => $email,
            'fullname' => $first_name . ' ' . $last_name,
            'tx_ref' => $sav->ref_id,
            'redirect_url' => route('webhook.card', ['id' => $sav->ref_id]),
            'authorization' => [
                'mode' => 'avs_noauth',
                'address' => $request->address,
                'country' => $country[1],
                'state' => $request->state,
                'city' => $request->city,
                'zipcode' => $request->zip_code,
            ]
        ];
        $payload = [
            'client' => encrypt3Des(json_encode($data), $this->settings->encrypt)
        ];
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post("https://api.flutterwave.com/v3/charges?type=card", $payload);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            if ($response != null) {
                cardError($sav->trace_id, $response->message, "error");
                return back()->with('alert', $response->message);
            } else {
                return back()->with('alert', 'An Error Occured');
            }
        } else {
            $sav->address = $request->address;
            $sav->country = $country[1];
            $sav->state = $request->state;
            $sav->city = $request->city;
            $sav->zip_code = $request->zip_code;
            $sav->card_reference = $response->data->flw_ref;
            $sav->trans_id = $response->data->id;
            $sav->card_number = $response->data->card->first_6digits . $response->data->card->last_4digits;
            $sav->card_type = strtolower($response->data->card->type);
            $sav->save();
            if ($response->meta->authorization->mode == "redirect") {
                cardError($sav->trace_id, "Authentication required: 3DS", "log");
                cardError($sav->trace_id, "Third-party authentication window opened", "log");
                return redirect()->away($response->meta->authorization->redirect);
            } elseif ($response->meta->authorization->mode == "otp") {
                cardError($sav->trace_id, "Authentication required: OTP", "log");
                return response()->json(['message' => $response->data->processor_response, 'status' => 'otp', 'data' => $sav->ref_id], 201);
            }
        }
    }
    public function paymentOtpSubmit(Request $request, $id)
    {
        $sav = Transactions::whereref_id($id)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'otp' => 'required|string|min:6',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
        }
        $payload = [
            'otp' => $request->otp,
            'flw_ref' => $sav->card_reference,
            'type' => 'card'
        ];
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post("https://api.flutterwave.com/v3/validate-charge", $payload);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            if ($response != null) {
                cardError($sav->trace_id, $response->message, "error");
                return response()->json(['message' => $response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                cardError($sav->trace_id, "No response from server", "error");
                return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->get("https://api.flutterwave.com/v3/transactions/" . $response->data->id . "/verify");
            $verify_response = $curl->response;
            $curl->close();
            if ($curl->error) {
                if ($verify_response != null) {
                    cardError($sav->trace_id, $verify_response->message, "error");
                    return response()->json(['message' => $verify_response->message, 'status' => 'failed', 'data' => null], 400);
                } else {
                    cardError($sav->trace_id, "No response from server", "error");
                    return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
                }
            } else {
                if ($verify_response->data->status == "successful") {
                    cardError($sav->trace_id, "Successfully paid with card", "log");
                    //Balance
                    $sav->status = 1;
                    $sav->completed_at = Carbon::now();
                    if ((new Agent())->isDesktop()) {
                        $sav->device = "tv";
                    }
                    if ((new Agent())->isMobile()) {
                        $sav->device = "mobile";
                    }
                    if ((new Agent())->isTablet()) {
                        $sav->device = "tablet";
                    }
                    $sav->card_issuer = $verify_response->data->card->issuer;
                    $sav->card_country = $verify_response->data->card->country;
                    $sav->save();
                    session::forget('trace_id');
                    session::forget('card_number');
                    session::forget('expiry');
                    session::forget('expiry_month');
                    session::forget('expiry_year');
                    session::forget('cvv');
                    session::forget('first_name');
                    session::forget('last_name');
                    session::forget('tx_ref');
                    session::forget('email');
                    $balance = Balance::whereuser_id($sav->receiver_id)->wherecountry_id($sav->currency)->first();
                    if ($sav->client == 0) {
                        $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                    } else {
                        $balance->amount = $balance->amount + $sav->amount;
                    }
                    $balance->save();
                    //Save Audit Log
                    $audit = new Audit();
                    $audit->user_id = $sav->receiver_id;
                    $audit->trx = $sav->ref_id;
                    if ($sav->type == 2) {
                        $audit->log = 'Received test payment ' . $sav->api->ref_id;
                    } else {
                        $audit->log = 'Received test payment ' . $sav->link->ref_id;
                    }
                    $audit->save();
                    if ($sav->type == 1) {
                        //Notify users
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                        }
                        //Send Webhook
                        if ($sav->link->user->receive_webhook == 1) {
                            if ($sav->link->user->webhook != null) {
                                send_webhook($sav->ref_id);
                            }
                        }
                    } elseif ($sav->type == 4) {
                        //Notify users
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                        }
                        //Send Webhook
                        if ($sav->balance->user->receive_webhook == 1) {
                            if ($sav->balance->user->webhook != null) {
                                send_webhook($sav->ref_id);
                            }
                        }
                    } else {
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                        }
                        //Send Webhook
                        if ($sav->api->user->receive_webhook == 1) {
                            if ($sav->api->user->webhook != null) {
                                send_webhook($sav->ref_id);
                            }
                        }
                    }
                    if ($sav->type == 2) {
                        if ($sav->api->callback_url != null) {
                            return redirect()->away($sav->api->callback_url . '?tx_ref=' . $sav->api->tx_ref);
                        }
                        return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                    }
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                }
            }
        }
    }
    public function webhookCard(Request $request, $id)
    {
        $sav = Transactions::whereref_id($id)->first();
        if($request->response!=null){
            $id_d=json_decode($request->response)->id;
        }elseif($request->resp!=null){
            $id_d=json_decode($request->resp)->data->id;
        }
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get("https://api.flutterwave.com/v3/transactions/" . $id_d . "/verify");
        $verify_response = $curl->response;
        $curl->close();
        if ($curl->error) {
            if ($verify_response != null) {
                cardError($sav->trace_id, $verify_response->message, "error");
                return response()->json(['message' => $verify_response->message, 'status' => 'failed', 'data' => null], 400);
            } else {
                cardError($sav->trace_id, "No response from server", "error");
                return response()->json(['message' => 'An Error Occured', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            if ($verify_response->data->status == "successful") {
                if ($sav->payment_type == "card") {
                    cardError($sav->trace_id, "Successfully paid with card", "log");
                }
                //Balance
                $sav->status = 1;
                $sav->completed_at = Carbon::now();
                if ((new Agent())->isDesktop()) {
                    $sav->device = "tv";
                }
                if ((new Agent())->isMobile()) {
                    $sav->device = "mobile";
                }
                if ((new Agent())->isTablet()) {
                    $sav->device = "tablet";
                }
                if ($sav->payment_method == "card") {
                    $sav->card_issuer = $verify_response->data->card->issuer;
                    $sav->card_country = $verify_response->data->card->country;
                }
                $sav->save();
                session::forget('trace_id');
                session::forget('card_number');
                session::forget('expiry');
                session::forget('expiry_month');
                session::forget('expiry_year');
                session::forget('cvv');
                session::forget('first_name');
                session::forget('last_name');
                session::forget('tx_ref');
                session::forget('email');
                $balance = Balance::whereuser_id($sav->receiver_id)->wherecountry_id($sav->currency)->first();
                if ($sav->client == 0) {
                    $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                } else {
                    $balance->amount = $balance->amount + $sav->amount;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $sav->receiver_id;
                $audit->trx = $sav->ref_id;
                if ($sav->type == 2) {
                    $audit->log = 'Received test payment ' . $sav->api->ref_id;
                } elseif ($sav->type == 1) {
                    $audit->log = 'Received test payment ' . $sav->link->ref_id;
                } elseif ($sav->type == 4) {
                    $audit->log = 'Received test payment ' . $sav->balance->ref_id;
                }
                $audit->save();
                if ($sav->type == 1) {
                    //Notify users
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->link->user->receive_webhook == 1) {
                        if ($sav->link->user->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                } elseif ($sav->type == 4) {
                    //Notify users
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->balance->user->receive_webhook == 1) {
                        if ($sav->balance->user->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                } else {
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->api->user->receive_webhook == 1) {
                        if ($sav->api->user->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                }
                if ($sav->type == 2) {
                    if ($sav->api->callback_url != null) {
                        return redirect()->away($sav->api->callback_url . '?tx_ref=' . $sav->api->tx_ref);
                    }
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                }
                return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
            }
        }
    }
}
