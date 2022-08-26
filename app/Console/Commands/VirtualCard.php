<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Virtual;
use Curl\Curl;
use App\Models\Settings;
use App\Models\Virtualtransactions;
use Carbon\Carbon;

class VirtualCard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:vcard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks virtual card transactions every 1 hour';

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
        $vcard=Virtual::all();
        foreach($vcard as $val){
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' .$this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->get("https://api.flutterwave.com/v3/virtual-cards/".$val->card_hash,);
            $curl->close();
            if ($curl->error) {

            }else{
                $result = $curl->response->data; 
                $amo=str_replace( ',', '', $result->amount);
                if($amo<$val->amount){
                    if($result->is_active==true){
                        $val->status=1;
                        $val->amount=$amo;
                        $val->save();
                    }else{
                        $val->status=0;
                        $val->amount=0;
                        $val->save();
                    }
                }else{
                    $val->amount=$amo;
                    $val->save();
                }
            }
            $postfield=[
                'from' => date('Y-m-d', strtotime($val->created_at)),
                'to' => Carbon::tomorrow()->format('Y-m-d'),
                'index' => 1,
                'size' => 100
            ];
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Bearer ' .$this->settings->secret_key);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->get("https://api.flutterwave.com/v3/virtual-cards/".$val->card_hash."/transactions", $postfield);
            $curl->close();
            if ($curl->error) {

            }else{
                $response = $curl->response->data;   
                foreach($response as $trans){
                    $check=Virtualtransactions::whereref_id($trans->reference)->count();
                    if($check==0){
                        $sav=new Virtualtransactions();
                        $sav->user_id=$val->user_id;
                        $sav->business_id = $val->business_id;
                        if($trans->product=='Card Issuance Fee'){
                            $sav->amount=$val->charge;
                        }else{
                            $sav->amount=$trans->amount;
                        }
                        $sav->description=$trans->narration;
                        $sav->ref_id=$trans->reference;
                        $sav->card_hash=$val->card_hash;
                        $sav->status=$trans->status;
                        $sav->gate=preg_replace('~^.{4}|.{4}(?!$)~', '$0 ', $val->card_pan);
                        if($trans->indicator=='C'){
                            $sav->type="Credit";
                        }elseif($trans->indicator=='D'){
                            $sav->type="Debit";
                        }
                        $sav->save();
                    } 
                }
            }   
        }
    }
}
