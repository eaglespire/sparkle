<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Curl\Curl;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Transactions;
use App\Jobs\SendPaymentEmail;
use App\Models\Settings;

class Chargeback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:chargeback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for chargebacks every 1 hour';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings = Settings::find(1);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = Transactions::wherestatus(1)->where('type', '!=', 3)->wherepayment_type('card')->wheremode(1)->get();
        foreach ($transactions as $val) {
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->get("https://api.flutterwave.com/v3/chargebacks?status=lost&currency=" . $val->getCurrency->real->currency . "flw_ref=" . $val->trans_id);
            $curl->close();
            if ($curl->error) {
            } else {
                if ($val->chargebacks == 0) {
                    if ($curl->response->data != null) {
                        if ($curl->response->data->status == "lost") {
                            $balance = Balance::whereuser_id($val->receiver_id)->wherecountry_id($val->currency)->first();
                            if ($val->client == 0) {
                                $balance->amount = $balance->amount - $val->amount;
                            } else {
                                $balance->amount = $balance->amount - $val->amount + $val->charge;
                            }
                            $balance->save();
                            $val->status = 4;
                            $val->save();
                        }
                    }
                }
            }
        }
        $transactions = Transactions::wherestatus(0)->where('type', '!=', 3)->wherepayment_type('bank')->wheremode(1)->get();
        foreach ($transactions as $val) {
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Basic ' . base64_encode($val->getCurrency->auth_key . ':' . $val->getCurrency->auth_secret));
            $curl->setHeader('Consent', $val->consent);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->get("https:api.yapily.com/payments/" . $val->charge_id . "/details");
            $response = $curl->response;
            if ($response->data->payments[0]->status == "FAILED") {
                $val->status = 2;
                $val->save();
            }elseif ($response->data->status == "COMPLETED") {
                $val->status = 1;
                $val->save();
                $balance = Balance::whereuser_id($val->receiver_id)->wherecountry_id($val->currency)->first();
                if ($val->client == 0) {
                    $balance->amount = $balance->amount + $val->amount - $val->charge;
                } else {
                    $balance->amount = $balance->amount + $val->amount;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $val->receiver_id;
                $audit->trx = $val->ref_id;
                if ($val->type == 2) {
                    $audit->log = 'Received test payment ' . $val->api->ref_id;
                } else {
                    $audit->log = 'Received test payment ' . $val->link->ref_id;
                }
                $audit->save();
                if ($val->type == 1) {
                    //Notify users
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($val->link->ref_id, $val->ref_id));
                    }
                    //Send Webhook
                    if ($val->link->user->receive_webhook == 1) {
                        if ($val->link->user->webhook != null) {
                            send_webhook($val->ref_id);
                        }
                    }
                } else {
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($val->api->ref_id, $val->ref_id));
                    }
                    //Send Webhook
                    if ($val->api->user->receive_webhook == 1) {
                        if ($val->api->user->webhook != null) {
                            send_webhook($val->ref_id);
                        }
                    }
                }
            }
        }
    }
}
