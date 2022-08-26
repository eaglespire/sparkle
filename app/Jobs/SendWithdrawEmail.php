<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Transactions;
use App\Models\Settings;
use App\Models\Logo;

class SendWithdrawEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $trans;
    public $reason;
    public function __construct($trans, $reason=null)
    {
        $this->trans=$trans;    
        $this->reason=$reason;   
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token=$this->trans;
        $link = Transactions::whereref_id($token)->first();
        $receiver = User::whereid($link->receiver_id)->first();
        $set = Settings::first();
        $mlogo = Logo::first();
        $receiver_name = $receiver->first_name . ' ' . $receiver->last_name;
        $from = env('MAIL_FROM_ADDRESS');
        $receiver_email = $receiver->email;
        $site = $set->site_name;
        $details = $set->site_desc;
        $method = $link->payment_type;
        $reference = $token;
        $payment_link = $link->ref_id;
        $to_amount = $link->getCurrency->real->currency_symbol . ' ' . number_format($link->amount, 2);
        $charge = $link->getCurrency->real->currency_symbol . ' ' . number_format($link->charge, 2);
        $logo = url('/') . '/asset/' . $mlogo->image_link;
        if($link->status==0){
            $receiver_subject = 'Payout request';
            $receiver_text = 'Your payout will be processed within '.$receiver->getCountrySupported()->duration.' days';
        }elseif($link->status==1){
            $receiver_subject = 'Payout approved';
            $receiver_text = 'Hi we sent you money';
        }elseif($link->status==2){
            $receiver_subject = 'Payout declined';
            $receiver_text = 'Your payout was declined,  Reason: '.$this->reason;
        }
        $data = array(
            'created' => $link->created_at,
            'next_settlement' => $link->next_settlement,
            'receiver_subject' => $receiver_subject,
            'receiver_name' => $receiver_name,
            'website' => $set->site_name,
            'receiver_text' => $receiver_text,
            'details' => $details,
            'to_amount' => $to_amount,
            'charges' => $charge,
            'method' => $method,
            'reference' => $reference,
            'payment_link' => $payment_link,
            'data' => $link,
            'logo' => $logo
        );
        if($receiver->business()->email_receiver==1){
            Mail::send(['html' => 'emails/payout'], $data, function ($r_message) use ($receiver_name, $receiver_email, $receiver_subject, $from, $site) {
                $r_message->to($receiver_email, $receiver_name)->subject($receiver_subject)->from($from, $site);
            });
        }
    }
}
