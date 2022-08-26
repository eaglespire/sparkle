<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Paymentlink;
use App\Models\Transactions;
use App\Models\Exttransfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPaymentEmail;
use Curl\Curl;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Redirect;
use Propaganistas\LaravelPhone\PhoneNumber;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function payment()
    {
        $data['title'] = "Payment";
        $data['status'] = 2;
        $data['limit'] = 6;
        $data['currency'] = 0;
        $data['links'] = auth()->guard('user')->user()->getPayment($data['limit']);
        if (count(auth()->guard('user')->user()->getPayment($data['limit'])) > 0) {
            $first = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wheremode(auth()->guard('user')->user()->business()->live)->orderby('created_at', 'desc')->first();
            $last = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wheremode(auth()->guard('user')->user()->business()->live)->orderby('created_at', 'asc')->first();
            $data['order'] = date("m/d/Y", strtotime($last->created_at)) . ' - ' . date("m/d/Y", strtotime($first->created_at));
        } else {
            $data['order'] = null;
        }

        return view('user.link.index', $data);
    }
    public function paymentSort(Request $request)
    {
        $data['title'] = "Payments";
        $data['status'] = $request->status;
        $data['limit'] = $request->limit;
        $data['order'] = $request->date;
        $data['currency'] = $request->currency;
        $date = explode('-', $request->date);
        $from = Carbon::create($date[0])->toDateString();
        $to = Carbon::create($date[1])->addDays(1)->toDateString();
        if ($request->status == "1" && $request->currency != "0") {
            if ($request->currency != "0") {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wherecurrency($request->currency)->whereuser_id(auth()->guard('user')->user()->id)->whereactive(1)->wheremode(auth()->guard('user')->user()->live)->paginate($data['limit']);
            } else {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->whereuser_id(auth()->guard('user')->user()->id)->whereactive(1)->wheremode(auth()->guard('user')->user()->live)->paginate($data['limit']);
            }
        } elseif ($request->status == "0") {
            if ($request->currency != "0") {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wherecurrency($request->currency)->wheremode(auth()->guard('user')->user()->live)->whereactive(0)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            } else {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wheremode(auth()->guard('user')->user()->live)->whereactive(0)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            }
        } elseif ($request->status == "2") {
            if ($request->currency != "0") {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wherecurrency($request->currency)->wheremode(auth()->guard('user')->user()->live)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            } else {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wheremode(auth()->guard('user')->user()->live)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            }
        }
        return view('user.link.index', $data);
    }
    public function paymentTransactions($id)
    {
        $data['title'] = "Transactions";
        $payment = Paymentlink::whereref_id($id)->first();
        $data['links'] = Transactions::wherepayment_link($payment->id)->latest()->get();
        return view('user.link.transactions', $data);
    }
    public function paymentShare($id)
    {
        $data['title'] = "New payment link";
        $data['link'] = Paymentlink::whereref_id($id)->first();
        return view('user.link.share', $data);
    }
    public function paymentPin($id)
    {
        $data['title'] = "Pin is required";
        $data['link'] = Transactions::whereref_id($id)->first();
        if ($data['link']->status == 0) {
            return view('user.card.pin', $data);
        } elseif ($data['link']->status == 2) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction was cancelled');
        } elseif ($data['link']->status == 1) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction already paid');
        }
    }
    public function paymentAvs($id)
    {
        $data['title'] = "Address verification";
        $data['link'] = Transactions::whereref_id($id)->first();
        if ($data['link']->status == 0) {
            return view('user.card.avs', $data);
        } elseif ($data['link']->status == 2) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction was cancelled');
        } elseif ($data['link']->status == 1) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction already paid');
        }
    }
    public function paymentOtp($id, $message)
    {
        $data['title'] = "OTP";
        $data['message'] = $message;
        $data['link'] = Transactions::whereref_id($id)->first();
        if ($data['link']->status == 0) {
            return view('user.card.otp', $data);
        } elseif ($data['link']->status == 2) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction was cancelled');
        } elseif ($data['link']->status == 1) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction already paid');
        }
    }
    public function disablePayment($id)
    {
        $page = Paymentlink::whereref_id($id)->first();
        $page->active = 0;
        $page->save();
        return back()->with('success', 'Disabled');
    }
    public function enablePayment($id)
    {
        $page = Paymentlink::whereref_id($id)->first();
        $page->active = 1;
        $page->save();
        return back()->with('success', 'Enabled');
    }
    public function paymentCancel($id)
    {
        $data = Transactions::whereref_id($id)->first();
        $data->status = 2;
        $data->save();
        cardError($data->trace_id, "Cancelled Transaction", "log");
        session::forget('card_number');
        session::forget('expiry');
        session::forget('expiry_month');
        session::forget('expiry_year');
        session::forget('cvv');
        session::forget('first_name');
        session::forget('last_name');
        session::forget('tx_ref');
        session::forget('email');
        return redirect()->route('payment.link', ['id' => $data->link->ref_id])->with('success', 'Payment cancelled');
    }
    public function updatePayment(Request $request, $id)
    {
        $currency = explode('*', $request->currency);
        $link = getCountry($currency[0]);
        if ($link->max_amount != null) {
            $max = $link->max_amount;
        } else {
            $max = null;
        }
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $max,
                'description' => 'required|string',
                'name' => 'required|string|max:255'
            ]
        );
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $data = Paymentlink::whereref_id($id)->first();
        $data->amount = $request->amount;
        $data->description = $request->description;
        $data->name = $request->name;
        $data->currency = $currency[0];
        $data->save();
        return back()->with('success', 'Payment updated');
    }
    public function createPayment(Request $request)
    {
        $user = User::find(auth()->guard('user')->user()->id);
        $currency = explode('*', $request->currency);
        $link = getCountry($currency[0]);
        if ($link->max_amount != null) {
            $max = $link->max_amount;
        } else {
            $max = null;
        }
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $max,
                'description' => 'required|string',
                'name' => 'required|string|max:255'
            ]
        );
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $data = new Paymentlink();
        $trx = 'SC-' . str_random(6);
        $data->ref_id = $trx;
        $data->amount = $request->amount;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->user_id = Auth::guard('user')->user()->id;
        $data->business_id = Auth::guard('user')->user()->business_id;
        $data->mode = $user->business()->live;
        $data->currency = $currency[0];
        $data->save();
        $audit = new Audit();
        $audit->user_id = Auth::guard('user')->user()->id;
        $audit->trx = str_random(16);
        $audit->log = 'Created Payment Link - ' . $trx;
        $audit->save();
        return redirect()->route('payment.share', ['id' => $data->ref_id])->with('success', 'Payment added');
    }
    public function deletePayment($id)
    {
        $data = Paymentlink::whereref_id($id)->first();
        $data->delete();
        $transactions = Transactions::wherepayment_link($data->id)->get();
        foreach ($transactions as $val) {
            $val->delete();
        }
        return back()->with('success', 'Payment deleted!');
    }
    public function paymentLink($id, $type = null)
    {
        $data['link'] = $link = Paymentlink::whereref_id($id)->first();
        $data['merchant'] = $user = User::find($link->user_id);
        $data['title'] = 'Payment';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->status == 0) {
                if ($link->active == 1) {
                    if ($link->mode == 1) {
                        if ($link->user->business()->kyc_status != "DECLINED") {
                            return view('user.link.live', $data);
                        }
                    } else {
                        return view('user.link.test', $data);
                    }
                } else {
                    $data['title'] = 'Error Occured';
                    return view('errors.error', $data)->withErrors('Payment link has been disabled');
                }
            } else {
                $data['title'] = 'Error Occured';
                return view('errors.error', $data)->withErrors('Payment link has been suspended');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }
    public function paymentSubmit(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'crf' => 'required',
            ]
        );
        if ($validator->fails()) {
            return back()->with('alert', 'An error occured');
        }
        if ($request->crf == 1) {
            $link = Paymentlink::whereref_id($id)->first();
            $m_charge = ($request->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        } else if ($request->crf == 2) {
            $link = Exttransfer::whereref_id($id)->first();
            $m_charge = ($link->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        } else {
            $link = Balance::whereref_id($id)->first();
            $m_charge = ($link->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        }
        $receiver = User::whereid($link->user->id)->first();
        if ($link->getCurrency->max_amount != null) {
            $max = $link->getCurrency->max_amount;
        } else {
            $max = null;
        }
        if ($request->type == 'card') {
            if ($request->crf == 1) {
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
            } else if ($request->crf == 2) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'expiry' => 'required',
                        'card_number' => 'required',
                        'cvv' => 'required|string|max:4',
                    ]
                );
            } else if ($request->crf == 3) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'expiry' => 'required',
                        'card_number' => 'required',
                        'cvv' => 'required|string|max:4',
                    ]
                );
            }
            if ($validator->fails()) {
                return back()->with('errors', $validator->errors());
            }
            $expiry = explode('/', $request->expiry);
            if ($request->crf == 1) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 1;
                    $sav->mode = 1;
                    $sav->amount = $request->amount;
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    if($link->getCurrency->pending_balance_duration!=0){
                        $sav->pending = 1;
                    }
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
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
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
                    if (array_key_exists('meta', (array)$curl->response)) {
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
                    }else{
                        if ($response->data->status == "successful") {
                            if ($sav->payment_type == "card") {
                                cardError($sav->trace_id, "Successfully paid with card", "log");
                            }
                            //Balance
                            $sav->status = 1;
                            if($sav->getCurrency->pending_balance_duration!=0 && $sav->type!=4){
                                $sav->pending = 1;
                            }
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
                            $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                            if($sav->pending==1){
                                if ($sav->client == 0) {
                                    $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                                    $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                                } else {
                                    $sav->pending_amount = $sav->pending_amount + $sav->amount;
                                    $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                                }
                                $sav->save();
                            }else{
                                if($sav->type==4){
                                    $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                                }else{
                                    if ($sav->client == 0) {
                                        $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                                    } else {
                                        $balance->amount = $balance->amount + $sav->amount;
                                    }
                                }
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
                                if ($sav->link->user->business()->receive_webhook == 1) {
                                    if ($sav->link->user->business()->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } elseif ($sav->type == 4) {
                                //Notify users
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->balance->user->business()->receive_webhook == 1) {
                                    if ($sav->balance->user->business()->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } else {
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->api->user->business()->receive_webhook == 1) {
                                    if ($sav->api->user->business()->webhook != null) {
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
            } else if ($request->crf == 2) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 2;
                    $sav->mode = 1;
                    if ($link->user->business()->charges == 0) {
                        $sav->amount = $link->amount - $m_charge;
                    } else {
                        $sav->amount = $link->amount + $m_charge;
                    }
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    if($link->getCurrency->pending_balance_duration!=0){
                        $sav->pending = 1;
                    }
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
                //Store session
                session::put('card_number', $request->card_number);
                session::put('expiry', $request->expiry);
                session::put('expiry_month', $expiry[0]);
                session::put('expiry_year', $expiry[1]);
                session::put('cvv', $request->cvv);
                session::put('first_name', $link->first_name);
                session::put('last_name', $link->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $link->email);
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
                    if (array_key_exists('meta', (array)$curl->response)) {
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
                    }else{
                        if ($response->data->status == "successful") {
                            if ($sav->payment_type == "card") {
                                cardError($sav->trace_id, "Successfully paid with card", "log");
                            }
                            //Balance
                            $sav->status = 1;
                            if($sav->getCurrency->pending_balance_duration!=0 && $sav->type!=4){
                                $sav->pending = 1;
                            }
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
                            $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                            if($sav->pending==1){
                                if ($sav->client == 0) {
                                    $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                                    $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                                } else {
                                    $sav->pending_amount = $sav->pending_amount + $sav->amount;
                                    $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                                }
                                $sav->save();
                            }else{
                                if($sav->type==4){
                                    $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                                }else{
                                    if ($sav->client == 0) {
                                        $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                                    } else {
                                        $balance->amount = $balance->amount + $sav->amount;
                                    }
                                }
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
                                if ($sav->link->user->business()->receive_webhook == 1) {
                                    if ($sav->link->user->business()->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } elseif ($sav->type == 4) {
                                //Notify users
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->balance->user->business()->receive_webhook == 1) {
                                    if ($sav->balance->user->business()->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } else {
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->api->user->business()->receive_webhook == 1) {
                                    if ($sav->api->user->business()->webhook != null) {
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
            } else if ($request->crf == 3) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 4;
                    $sav->mode = 1;
                    $sav->amount = $request->amount - $m_charge;
                    $sav->charge = $m_charge;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->country_id;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    if ($link->user->business()->charges == 0) {
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
                session::put('tx_ref', $sav->ref_id);
                $data = [
                    'amount' => $request->amount,
                    'card_number' => str_replace(' ', '', $request->card_number),
                    'cvv' => $request->cvv,
                    'expiry_month' => $expiry[0],
                    'expiry_year' => $expiry[1],
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $receiver->email,
                    'fullname' => $receiver->first_name . ' ' . $receiver->last_name,
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
                    if (array_key_exists('meta', (array)$curl->response)) {
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
                            $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
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
                                if ($sav->link->user->business()->receive_webhook == 1) {
                                    if ($sav->link->user->business()->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } elseif ($sav->type == 4) {
                                //Notify users
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->balance->user->business()->receive_webhook == 1) {
                                    if ($sav->balance->user->business()->webhook != null) {
                                        send_webhook($sav->ref_id);
                                    }
                                }
                            } else {
                                if ($this->settings->email_notify == 1) {
                                    dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                                }
                                //Send Webhook
                                if ($sav->api->user->business()->receive_webhook == 1) {
                                    if ($sav->api->user->business()->webhook != null) {
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
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                        'status' => 'required',
                    ]
                );
            } else {
                if (getTransaction($link->id, $link->user_id) != null) {
                    return back()->with('alert', 'Session expired');
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
            }
            if ($validator->fails()) {
                return back()->with('errors', $validator->errors());
            }
            if ($request->crf == 1) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 1;
                $sav->mode = 0;
                if ($link->user->business()->charges == 0) {
                    $sav->amount = $request->amount - $m_charge;
                } else {
                    $sav->amount = $request->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $request->email;
                $sav->first_name = $request->first_name;
                $sav->last_name = $request->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
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
                $balance = Balance::whereuser_id($link->user->id)->wherebusiness_id($link->business_id)->wherecountry_id($link->getCurrency->id)->first();
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
                if ($link->user->business()->receive_webhook == 1) {
                    if ($link->user->business()->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id])->with('success', 'Payment was successful');
                } else {
                    return back()->with('alert', 'Payment failed');
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
                $sav->business_id = $link->business_id;
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
                $balance = Balance::whereuser_id($link->user->id)->wherebusiness_id($link->business_id)->wherecountry_id($link->getCurrency->id)->first();
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
                if ($link->user->business()->receive_webhook == 1) {
                    if ($link->user->business()->webhook != null) {
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
        }
        if ($request->type == 'bank') {
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                    ]
                );
            } else if ($request->crf == 2) {
                if (getTransaction($link->id, $link->user_id) != null) {
                    return back()->with('alert', 'Session expired');
                }
            } else if ($request->crf == 3) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max
                    ]
                );
            }
            if ($validator->fails()) {
                return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('errors', $validator->errors());
            }
            if ($request->crf == 1) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 1;
                $sav->mode = 1;
                if ($link->user->business()->charges == 0) {
                    $sav->amount = $request->amount - $m_charge;
                } else {
                    $sav->amount = $request->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $request->email;
                $sav->first_name = $request->first_name;
                $sav->last_name = $request->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'bank';
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
                    return back()->with('alert', $response->error->status . '-' . $response->error->message);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 1;
                    $data['reference'] = $sav->ref_id;
                    return view('user.dashboard.institution', $data);
                }
            } else if ($request->crf == 2) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 2;
                $sav->mode = 0;
                if ($link->user->business()->charges == 0) {
                    $sav->amount = $link->amount - $m_charge;
                } else {
                    $sav->amount = $link->amount + $m_charge;
                }
                $sav->charge = $m_charge;
                $sav->email = $link->email;
                $sav->first_name = $link->first_name;
                $sav->last_name = $link->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'bank';
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
                    return back()->with('alert', $response->error->status . '-' . $response->error->message);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 2;
                    $data['reference'] = $sav->ref_id;
                    return view('user.dashboard.institution', $data);
                }
            } else if ($request->crf == 3) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 4;
                $sav->mode = 0;
                $sav->amount = $request->amount;
                $sav->charge = $m_charge;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'bank';
                $sav->ip_address = user_ip();
                $sav->currency = $link->country_id;
                $sav->status = $request->status;
                $sav->save();
                $authToken = base64_encode($link->getCurrency->auth_key . ':' . $link->getCurrency->auth_secret);
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Basic ' . $authToken);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->get("https://api.yapily.com/institutions");
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    return back()->with('alert', $response->error->status . '-' . $response->error->message);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 2;
                    $data['reference'] = $sav->ref_id;
                    return view('user.dashboard.institution', $data);
                }
            }
        }
        if ($request->type == 'mobile_money') {
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                        'mobile' => 'required',
                    ]
                );
            } else if ($request->crf == 2) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'mobile' => 'required',
                    ]
                );
            } else if ($request->crf == 3) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'mobile' => 'required',
                    ]
                );
            }
            if ($validator->fails()) {
                return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('errors', $validator->errors());
            }
            $mobile = $request->mobile;
            if ($request->crf == 1) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 1;
                    $sav->mode = 1;
                    if ($link->user->business()->charges == 0) {
                        $sav->amount = $request->amount - $m_charge;
                    } else {
                        $sav->amount = $request->amount + $m_charge;
                    }
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->mobile = $request->mobile;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
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
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    if ($link->user->charges == 0) {
                        $sav->amount = $request->amount - $m_charge;
                    } else {
                        $sav->amount = $request->amount + $m_charge;
                    }
                    $sav->email = $request->email;
                    $sav->mobile = $mobile;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                session::put('mobile', $request->mobile);
                session::put('first_name', $request->first_name);
                session::put('last_name', $request->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $request->email);
                $data = [
                    'amount' => $request->amount,
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $request->email,
                    'phone_number' => $request->mobile,
                    'fullname' => $request->first_name . ' ' . $request->last_name,
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
                        return back()->with('alert', $response->message);
                    } else {
                        return back()->with('alert', 'An Error Occured');
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
                                return back()->with('alert', $verify_response->message);
                            } else {
                                cardError($sav->trace_id, "No response from server", "error");
                                return back()->with('alert', 'An Error Occured');
                            }
                        } else {
                            if ($verify_response->data->status == "successful") {
                                $sav->status = 1;
                                $sav->completed_at = Carbon::now();
                                $sav->save();
                                session::forget('trace_id');
                                session::forget('first_name');
                                session::forget('mobile');
                                session::forget('last_name');
                                session::forget('tx_ref');
                                session::forget('email');
                                $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
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
                                if ($sav->link->user->business()->receive_webhook == 1) {
                                    if ($sav->link->user->business()->webhook != null) {
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
            } else if ($request->crf == 2) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 2;
                    $sav->mode = 1;
                    if ($link->user->business()->charges == 0) {
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
                    $sav->business_id = $link->business_id;
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
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    if ($link->user->business()->charges == 0) {
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
                session::put('mobile', $link->mobile);
                session::put('first_name', $link->first_name);
                session::put('last_name', $link->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $link->email);
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
                        return back()->with('alert', $response->message);
                    } else {
                        return back()->with('alert', 'An Error Occured');
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
                                return back()->with('alert', $verify_response->message);
                            } else {
                                cardError($sav->trace_id, "No response from server", "error");
                                return back()->with('alert', 'An Error Occured');
                            }
                        } else {
                            if ($verify_response->data->status == "successful") {
                                $sav->status = 1;
                                $sav->completed_at = Carbon::now();
                                $sav->save();
                                session::forget('trace_id');
                                session::forget('first_name');
                                session::forget('mobile');
                                session::forget('last_name');
                                session::forget('tx_ref');
                                session::forget('email');
                                $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
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
                                if ($sav->link->user->business()->receive_webhook == 1) {
                                    if ($sav->link->user->business()->webhook != null) {
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
            } else if ($request->crf == 3) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 4;
                    $sav->mode = 1;
                    $sav->amount = $request->amount;
                    $sav->charge = $m_charge;
                    $sav->mobile = $request->mobile;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'mobile';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->country_id;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = $request->amount;
                    $sav->mobile = $request->mobile;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                session::put('mobile', $link->mobile);
                session::put('tx_ref', $sav->ref_id);
                $data = [
                    'amount' => $link->amount,
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $receiver->email,
                    'phone_number' => $request->mobile,
                    'fullname' => $receiver->first_name . ' ' . $receiver->last_name,
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
                        return back()->with('alert', $response->message);
                    } else {
                        return back()->with('alert', 'An Error Occured');
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
                                return back()->with('alert', $verify_response->message);
                            } else {
                                cardError($sav->trace_id, "No response from server", "error");
                                return back()->with('alert', 'An Error Occured');
                            }
                        } else {
                            if ($verify_response->data->status == "successful") {
                                $sav->status = 1;
                                $sav->completed_at = Carbon::now();
                                $sav->save();
                                session::forget('trace_id');
                                session::forget('mobile');
                                session::forget('tx_ref');
                                $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
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
                                if ($sav->link->user->business()->receive_webhook == 1) {
                                    if ($sav->link->user->business()->webhook != null) {
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
    public function authorize_payment($auth_token, $bank_id, $trans_type, $reference)
    {
        $transaction = Transactions::whereref_id($reference)->first();
        if ($transaction->getCurrency->real->currency == "GBP") {
            $bank_array = [
                [
                    'type' => "ACCOUNT_NUMBER",
                    'identification' => $transaction->getCurrency->acct_no,
                ], [
                    'type' => "SORT_CODE",
                    'identification' => $transaction->getCurrency->sort_code,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "USD") {
            $bank_array = [
                [
                    'type' => "ROUTING_NUMBER",
                    'identification' => $transaction->getCurrency->routing_no,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "EUR") {
            $bank_array = [
                [
                    'type' => "IBAN",
                    'identification' => $transaction->getCurrency->iban,
                ]
            ];
        }
        if ($transaction->type == 1) {
            $d_reference = "Payment";
        } elseif ($transaction->type == 2) {
            $d_reference = "API";
        } elseif ($transaction->type == 4) {
            $d_reference = "FUNDING";
        }
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Basic ' . $auth_token);
        $curl->setHeader('Content-Type', 'application/json');
        $data = [
            'applicationUserId' => $transaction->receiver->email,
            'institutionId' => $bank_id,
            'callback' => route('bankcallback'),
            'paymentRequest' => [
                'type' => "DOMESTIC_PAYMENT",
                'reference' => $d_reference,
                'paymentIdempotencyId' => $reference,
                'amount' => [
                    'amount' => number_format($transaction->amount, 2),
                    'currency' => $transaction->getCurrency->real->currency,
                ],
                'payee' => [
                    'name' => $transaction->getCurrency->first_name . ' ' . $transaction->getCurrency->last_name,
                    'accountIdentifications' => $bank_array,
                ],
            ],
        ];
        $curl->post("https://api.yapily.com/payment-auth-requests", $data);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($response->error->status . '-' . $response->error->message);
        } else {
            $transaction->tracing_id = $response->meta->tracingId;
            $transaction->save();
            Session::put('trans', $transaction->ref_id);
            return Redirect::away($response->data->authorisationUrl);
        }
    }
    public function bankcallback(Request $request)
    {
        $transaction = Transactions::whereref_id(Session('trans'))->firstOrFail();
        if ($transaction->getCurrency->real->currency == "GBP") {
            $bank_array = [
                [
                    'type' => "ACCOUNT_NUMBER",
                    'identification' => $transaction->getCurrency->acct_no,
                ], [
                    'type' => "SORT_CODE",
                    'identification' => $transaction->getCurrency->sort_code,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "USD") {
            $bank_array = [
                [
                    'type' => "ROUTING_NUMBER",
                    'identification' => $transaction->getCurrency->routing_no,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "EUR") {
            $bank_array = [
                [
                    'type' => "IBAN",
                    'identification' => $transaction->getCurrency->iban,
                ]
            ];
        }
        if (!empty($request->consent)) {
            if ($transaction->type == 1) {
                $d_reference = "Payment";
            } elseif ($transaction->type == 2) {
                $d_reference = "API";
            } elseif ($transaction->type == 4) {
                $d_reference = "FUNDING";
            }
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Basic ' . base64_encode($transaction->getCurrency->auth_key . ':' . $transaction->getCurrency->auth_secret));
            $curl->setHeader('Consent', $request->consent);
            $curl->setHeader('Content-Type', 'application/json');
            $data = [
                'type' => "DOMESTIC_PAYMENT",
                'reference' => $d_reference,
                'paymentIdempotencyId' => $transaction->ref_id,
                'amount' => [
                    'amount' => number_format($transaction->amount, 2),
                    'currency' => $transaction->getCurrency->real->currency,
                ],
                'payee' => [
                    'name' => $transaction->getCurrency->first_name . ' ' . $transaction->getCurrency->last_name,
                    'accountIdentifications' => $bank_array,
                ],
            ];
            $curl->post("https://api.yapily.com/payments", $data);
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                $data['title'] = 'Error Message';
                return view('user.merchant.error', $data)->withErrors($response->error->status . '-' . $response->error->message);
            } else {
                $transaction->charge_id = $response->data->id;
                $transaction->consent = $request->consent;
                $transaction->tracing_id = $response->meta->tracingId;
                $transaction->save();
                if ($response->data->status == "PENDING") {
                    if ($transaction->type == 1) {
                        return redirect()->route('payment.link', ['id' => $transaction->link->ref_id])->with('alert', 'Payment might be pending due to bank service. Please allow up to 2 hours for the settling bank to return a successful or failed transaction');
                    } else {
                        return redirect()->route('payment.link', ['id' => $transaction->link->ref_id, 'type' => 'card'])->with('alert', 'Payment might be pending due to bank service. Please allow up to 2 hours for the settling bank to return a successful or failed transaction');
                    }
                } elseif ($response->data->status == "FAILED") {
                    $transaction->status = 2;
                    $transaction->save();
                    if ($transaction->type == 1) {
                        return redirect()->route('payment.link', ['id' => $transaction->link->ref_id])->with('alert', 'Payment Failed');
                    } else {
                        return redirect()->route('payment.link', ['id' => $transaction->link->ref_id, 'type' => 'card'])->with('alert', 'Payment Failed');
                    }
                } elseif ($response->data->status == "COMPLETED") {
                    $transaction->status = 1;
                    $transaction->save();
                    $balance = Balance::whereuser_id($transaction->receiver_id)->wherebusiness_id($transaction->business_id)->wherecountry_id($transaction->currency)->first();
                    if ($transaction->client == 0) {
                        $balance->amount = $balance->amount + $transaction->amount - $transaction->charge;
                    } else {
                        $balance->amount = $balance->amount + $transaction->amount;
                    }
                    $balance->save();
                    //Save Audit Log
                    $audit = new Audit();
                    $audit->user_id = $transaction->receiver_id;
                    $audit->trx = $transaction->ref_id;
                    if ($transaction->type == 2) {
                        $audit->log = 'Received test payment ' . $transaction->api->ref_id;
                    } else {
                        $audit->log = 'Received test payment ' . $transaction->link->ref_id;
                    }
                    $audit->save();
                    if ($transaction->type == 1) {
                        //Notify users
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($transaction->link->ref_id, $transaction->ref_id));
                        }
                        //Send Webhook
                        if ($transaction->link->user->business()->receive_webhook == 1) {
                            if ($transaction->link->user->business()->webhook != null) {
                                send_webhook($transaction->ref_id);
                            }
                        }
                    } else {
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($transaction->api->ref_id, $transaction->ref_id));
                        }
                        //Send Webhook
                        if ($transaction->api->user->business()->receive_webhook == 1) {
                            if ($transaction->api->user->business()->webhook != null) {
                                send_webhook($transaction->ref_id);
                            }
                        }
                    }
                    if ($transaction->type == 2) {
                        if ($transaction->api->callback_url != null) {
                            return redirect()->away($transaction->api->callback_url . '?tx_ref=' . $transaction->api->tx_ref);
                        }
                        return redirect()->route('generate.receipt', ['id' => $transaction->ref_id])->with('success', 'Payment was successful');
                    }
                    return redirect()->route('generate.receipt', ['id' => $transaction->ref_id])->with('success', 'Payment was successful');
                }
            }
        } else {
            $transaction->status = 2;
            $transaction->save();
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors("Sorry but your payment was cancelled. <a href=" . route('bankrecall', ['id' => $transaction->ref_id]) . ">Go back?</a>");
        }
    }
    public function bankrecall($id)
    {
        $trans = Transactions::whereref_id($id)->first();
        $authToken = base64_encode($trans->getCurrency->auth_key . ':' . $trans->getCurrency->auth_secret);
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Basic ' . $authToken);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get("https://api.yapily.com/institutions");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($response->error->status . '-' . $response->error->message);
        } else {
            $data['authtoken'] = $authToken;
            $data['institution'] = $response->data;
            $data['title'] = 'Select Preferred Bank';
            $data['reference'] = $trans->ref_id;
            $data['type'] = $trans->type;
            return view('user.dashboard.institution', $data);
        }
    }
    public function paymentPinSubmit(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pin' => 'required|string|max:4',
            ]
        );
        if ($validator->fails()) {
            return back()->with('errors', $validator->errors());
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
                return back()->with('alert', $response->message);
            } else {
                return back()->with('alert', 'An Error Occured');
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
                return redirect()->route('payment.otp', ['id' => $sav->ref_id, 'message' => $response->data->processor_response]);
            }
        }
    }
    public function paymentAvsSubmit(Request $request, $id)
    {
        $country = explode('*', $request->country);
        session::put('address', $request->address);
        session::put('country', $country[0]);
        session::put('state', $request->state);
        session::put('city', $request->city);
        session::put('zip_code', $request->zip_code);
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
            return back()->with('errors', $validator->errors());
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
                return redirect()->route('payment.otp', ['id' => $sav->ref_id, 'message' => $response->data->processor_response]);
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
            return back()->with('errors', $validator->errors());
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
                return back()->with('alert', $response->message);
            } else {
                cardError($sav->trace_id, "No response from server", "error");
                return back()->with('alert', 'An Error Occured');
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
                    return back()->with('alert', $verify_response->message);
                } else {
                    cardError($sav->trace_id, "No response from server", "error");
                    return back()->with('alert', 'An Error Occured');
                }
            } else {
                if ($verify_response->data->status == "successful") {
                    cardError($sav->trace_id, "Successfully paid with card", "log");
                    //Balance
                    $sav->status = 1;
                    if($sav->getCurrency->pending_balance_duration!=0 && $sav->type!=4){
                        $sav->pending = 1;
                    }
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
                    $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                    if($sav->pending==1){
                        if ($sav->client == 0) {
                            $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                            $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                        } else {
                            $sav->pending_amount = $sav->pending_amount + $sav->amount;
                            $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                        }
                        $sav->save();
                    }else{
                        if($sav->type==4){
                            $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                        }else{
                            if ($sav->client == 0) {
                                $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                            } else {
                                $balance->amount = $balance->amount + $sav->amount;
                            }
                        }
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
                        if ($sav->link->user->business()->receive_webhook == 1) {
                            if ($sav->link->user->business()->webhook != null) {
                                send_webhook($sav->ref_id);
                            }
                        }
                    } elseif ($sav->type == 4) {
                        //Notify users
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                        }
                        //Send Webhook
                        if ($sav->balance->user->business()->receive_webhook == 1) {
                            if ($sav->balance->user->business()->webhook != null) {
                                send_webhook($sav->ref_id);
                            }
                        }
                    } else {
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                        }
                        //Send Webhook
                        if ($sav->api->user->business()->receive_webhook == 1) {
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
                if ($sav->payment_type == "card") {
                    cardError($sav->trace_id, $verify_response->message, "error");
                }
                return back()->with('alert', $verify_response->message);
            } else {
                if ($sav->payment_type == "card") {
                    cardError($sav->trace_id, "No response from server", "error");
                }
                return back()->with('alert', 'An Error Occured');
            }
        } else {
            //dd($verify_response);
            if ($verify_response->data->status == "successful") {
                if ($sav->payment_type == "card") {
                    cardError($sav->trace_id, "Successfully paid with card", "log");
                }
                //Balance
                $sav->status = 1;
                if($sav->getCurrency->pending_balance_duration!=0 && $sav->type!=4){
                    $sav->pending = 1;
                }
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
                $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                if($sav->pending==1){
                    if ($sav->client == 0) {
                        $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                        $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                    } else {
                        $sav->pending_amount = $sav->pending_amount + $sav->amount;
                        $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                    }
                    $sav->save();
                }else{
                    if($sav->type==4){
                        $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                    }else{
                        if ($sav->client == 0) {
                            $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                        } else {
                            $balance->amount = $balance->amount + $sav->amount;
                        }
                    }
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
                    if ($sav->link->user->business()->receive_webhook == 1) {
                        if ($sav->link->user->business()->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                } elseif ($sav->type == 4) {
                    //Notify users
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->balance->user->business()->receive_webhook == 1) {
                        if ($sav->balance->user->business()->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                } else {
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->api->user->business()->receive_webhook == 1) {
                        if ($sav->api->user->business()->webhook != null) {
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
    public function search(Request $request)
    {
        $data['title'] = "Search Result for: " . $request->search;
        $data['status'] = 0;
        $data['limit'] = 3;
        $data['currency'] = 0;
        $data['links'] = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->where('name', 'LIKE', '%' . $request->search . '%')->orwhere('amount', 'LIKE', '%' . $request->search . '%')->orwhere('description', 'LIKE', '%' . $request->search . '%')->wheremode(auth()->guard('user')->user()->live)->orderby('created_at', 'desc')->paginate($data['limit']);
        if (count(auth()->guard('user')->user()->getPayment($data['limit'])) > 0) {
            $first = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wheremode(auth()->guard('user')->user()->live)->orderby('created_at', 'desc')->first();
            $last = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wheremode(auth()->guard('user')->user()->live)->orderby('created_at', 'asc')->first();
            $data['order'] = date("m/d/Y", strtotime($last->created_at)) . ' - ' . date("m/d/Y", strtotime($first->created_at));
        } else {
            $data['order'] = null;
        }
        return view('user.link.index', $data);
    }
    public function swapSubmit(Request $request, $id)
    {
        $balance=Balance::whereref_id($id)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'from_amount' => 'integer|required|max:'.getCountry($balance->country_id)->swap_max_amount.'|min:'.getCountry($balance->country_id)->swap_min_amount,
            ]
        );
        if ($validator->fails()) {
            return back()->with('errors', $validator->errors());
        }
        $to_currency=explode('*', $request->currency);
        if($balance->amount>$request->from_amount||$balance->amount==$request->from_amount){
            $sav = new Transactions();
            $sav->ref_id = randomNumber(11);
            $sav->type = 5;
            $sav->mode = 1;
            $sav->amount = $request->amount - getCountryRatesUnique($balance->country_id, $to_currency[2])->charge;
            $sav->charge = getCountryRatesUnique($balance->country_id, $to_currency[2])->charge;
            $sav->receiver_id = $balance->user_id;
            $sav->business_id = $balance->business_id;
            $sav->payment_link = $id;
            $sav->payment_type = 'swap';
            $sav->ip_address = user_ip();
            $sav->currency = $to_currency[2];
            $sav->status = 1;
            $sav->save();
            $balance->amount = $balance->amount - $request->from_amount;
            $balance->save();
            $credit=Balance::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wherecountry_id($to_currency[2])->first();
            $credit->amount = $credit->amount + ($request->from_amount*getCountryRatesUnique($balance->country_id, $to_currency[2])->rate);
            $credit->save();
            return redirect()->route('user.transactions', ['balance'=>$credit->ref_id])->with('success', 'Conversion successful');
        }else{
            return back()->with('alert', 'Insufficient Balance');
        }
    }
}
