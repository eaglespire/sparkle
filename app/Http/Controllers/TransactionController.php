<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Settings;
use App\Models\beneficiary;
use PDF;
use Curl\curl;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWithdrawEmail;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function transactions($balance)
    {
        $data['title'] = 'Transactions';
        $data['balance'] = $balance;
        return view('user.transactions.index', $data);
    }
    public function viewTransactions($id, $type)
    {
        $data['val'] = Transactions::whereref_id($id)->first();
        if ($data['val']->type == 1) {
            $tt = "Payment";
        } elseif ($data['val']->type == 2) {
            $tt = "API";
        } elseif ($data['val']->type == 3) {
            $tt = "Payout";
        } elseif ($data['val']->type == 4) {
            $tt = "Funding";
        } elseif ($data['val']->type == 5) {
            $tt = "Swapping";
        }
        $data['title'] = $tt;
        $data['type'] = $type;
        return view('user.transactions.view', $data);
    }
    public function initiateRefund($id)
    {
        $data = Transactions::wheretrans_id($id)->first();
        $post = [
            'amount' => $data->amount
        ];
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post("https://api.flutterwave.com/v3/transactions/" . $id . "/refund", $post);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            if ($response != null) {
                return back()->with('alert', $response->message);
            } else {
                return back()->with('alert', 'An Error Occured');
            }
        } else {
            $data->refund_id = $response->data->id;
            $data->save();
            return back()->with('success', 'Refund will take 5 to 15 days to process');
        }
    }
    public function chargeback()
    {
        $data['title'] = 'Charge Backs';
        return view('user.transactions.chargeback', $data);
    }
    public function generatereceipt($id)
    {
        $data['link'] = $trans = Transactions::whereref_id($id)->first();
        if ($trans->status == 1) {
            $data['title'] = "Receipt from " . $trans->receiver->first_name . ' ' . $trans->receiver->last_name;
            return view('user.transactions.receipt', $data);
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }
    public function downloadreceipt($id)
    {
        $data['link'] = $trans = Transactions::whereref_id($id)->first();
        if ($trans->status == 1) {
            $data['title'] = "Receipt from " . $trans->receiver->first_name . ' ' . $trans->receiver->last_name;
            $pdf = PDF::loadView('user.transactions.download', $data)->setPaper('a4');
            return $pdf->download($id . '.pdf');
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }
    public function withdrawSubmit(Request $request, $id)
    {
        $balance = Balance::whereref_id($id)->first();
        $country = getCountry($balance->country_id);
        $payout_type = explode('*',$request->payout_type);
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'required|integer|min:1|max:' . auth()->guard('user')->user()->getBalance($country->id)->amount,
            ]
        );
        if ($payout_type[0] == 1) {
            if ($country->bank_format == "us") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'routing_no' => 'required|string|max:9',
                    ]
                );
            }
            if ($country->bank_format == "eur") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'iban' => 'required|string|max:16',
                    ]
                );
            }
            if ($country->bank_format == "uk") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'acct_no' => 'required|string|max:8',
                        'sort_code' => 'required|string|max:6',
                    ]
                );
            }
            if ($country->bank_format == "normal") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'bank_name' => 'required',
                        'acct_no' => 'required|string|max:10',
                        'acct_name' => 'required|string|max:255',
                    ]
                );
            }
        }
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $charge = $country->withdraw_fiat_charge + ($request->amount * $country->withdraw_percent_charge / 100);
        if ($request->amount > $charge || $request->amount == $charge) {
            $sav = new Transactions();
            $sav->ref_id = randomNumber(11);
            $sav->type = 3;
            $sav->mode = 1;
            $sav->amount = $request->amount - $charge;
            $sav->charge = $charge;
            $sav->payment_type = 'bank';
            $sav->ip_address = user_ip();
            $sav->currency = $balance->country_id;
            $sav->receiver_id = auth()->guard('user')->user()->id;
            $sav->business_id = auth()->guard('user')->user()->business_id;
            if ($payout_type[0] == 2) {
                $data = beneficiary::find($request->beneficiary);
                if ($country->bank_format == "us") {
                    $sav->routing_no = $data->routing_no;
                } elseif ($country->bank_format == "eur") {
                    $sav->iban = $data->iban;
                } elseif ($country->bank_format == "uk") {
                    $sav->acct_no = $data->acct_no;
                    $sav->sort_code = $data->sort_code;
                } elseif ($country->bank_format == "normal") {
                    $sav->bank_name = $data->bank_name;
                    $sav->acct_no = $data->acct_no;
                    $sav->acct_name = $data->acct_name;
                }
            } else {
                if ($country->bank_format == "us") {
                    $sav->routing_no = $request->routing_no;
                } elseif ($country->bank_format == "eur") {
                    $sav->iban = $request->iban;
                } elseif ($country->bank_format == "uk") {
                    $sav->acct_no = $request->acct_no;
                    $sav->sort_code = $request->sort_code;
                } elseif ($country->bank_format == "normal") {
                    $sav->bank_name = $request->bank_name;
                    $sav->acct_no = $request->acct_no;
                    $sav->acct_name = $request->acct_name;
                }
            }
            $sav->name = $request->name;
            $sav->next_settlement = nextPayoutDate($country->duration);
            $sav->save();
            //Balance
            //$balance = Balance::whereuser_id(auth()->guard('user')->user()->id)->wherecountry_id($id)->first();
            $balance->amount = $balance->amount - $request->amount;
            $balance->save();
            //Save Audit Log
            $audit = new Audit();
            $audit->user_id = auth()->guard('user')->user()->id;
            $audit->trx = $sav->ref_id;
            $audit->log = 'Sent Payout request ' . $sav->ref_id;
            $audit->save();
            //Notify users
            if ($this->settings->email_notify == 1) {
                dispatch(new SendWithdrawEmail($sav->ref_id));
            }
            //Send Webhook
            if (auth()->guard('user')->user()->receive_webhook == 1) {
                if (auth()->guard('user')->user()->webhook != null) {
                    send_webhook($sav->ref_id);
                }
            }
            if (!empty($request->new_beneficiary)) {
                $data = new beneficiary();
                if ($country->bank_format == "us") {
                    $data->routing_no = $request->routing_no;
                } elseif ($country->bank_format == "eur") {
                    $data->iban = $request->iban;
                } elseif ($country->bank_format == "uk") {
                    $data->acct_no = $request->acct_no;
                    $data->sort_code = $request->sort_code;
                } elseif ($country->bank_format == "normal") {
                    $data->bank_name = $request->bank_name;
                    $data->acct_no = $request->acct_no;
                    $data->acct_name = $request->acct_name;
                }
                $data->business_id = auth()->guard('user')->user()->business_id;
                $data->user_id = auth()->guard('user')->user()->id;
                $data->country = $balance->country_id;
                $data->name = $request->name;
                $data->save();
            }
            return back()->with('success', 'Request submitted');
        } else {
            return back()->with('alert', 'Charge can\'t be greater than amount');
        }
    }
    public function Fundaccount($id, $type = null)
    {
        $data['link'] = $link = Balance::whereref_id($id)->first();
        $data['title'] = 'Fund account';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->user->kyc_status != "DECLINED") {
                return view('user.transactions.fund', $data);
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }
}
