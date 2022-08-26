<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Paymentlink;
use App\Models\User;
use App\Models\Transactions;
use App\Models\Settings;
use App\Models\Exttransfer;
use App\Models\Logo;
use App\Models\Balance;

class SendPaymentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $link;
    public $trans;

    public function __construct($link, $trans)
    {
        $this->link = $link;
        $this->trans = $trans;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ref = $this->link;
        $token = $this->trans;
        $dd = Transactions::whereref_id($token)->first();
        if($dd->type==1){
            $link = Paymentlink::whereref_id($ref)->first();
        }elseif($dd->type==2){
            $link = Exttransfer::whereref_id($ref)->first();
        }elseif($dd->type==4){
            $link = Balance::whereref_id($ref)->first();
        }
        $receiver = User::whereid($link->user_id)->first();
        $set = Settings::first();
        $mlogo = Logo::first();
        $receiver_name = $receiver->first_name . ' ' . $receiver->last_name;
        $from = env('MAIL_FROM_ADDRESS');
        $receiver_email = $receiver->email;
        $sender_email = $dd->email;
        $site = $set->site_name;
        $details = $set->site_desc;
        $method = $dd->payment_type;
        $reference = $token;
        $payment_link = $link->ref_id;
        if ($dd->client == 1) {
            $from_amount = $link->getCurrency->real->currency_symbol . ' ' . number_format($dd->amount + $dd->charge, 2);
            $to_amount = $link->getCurrency->real->currency_symbol . ' ' . number_format($dd->amount + $dd->charge, 2);
        } else {
            $from_amount = $link->getCurrency->real->currency_symbol . ' ' . number_format($dd->amount, 2);
            $to_amount = $link->getCurrency->real->currency_symbol . ' ' . number_format($dd->amount, 2);
        }
        $charge = $link->getCurrency->real->currency_symbol . ' ' . number_format($dd->charge, 2);
        $logo = url('/') . '/asset/' . $mlogo->image_link;
        if ($dd->mode == 1) {
            $receiver_subject = 'New successful transaction';
            $sender_subject = 'Receipt from ' . $receiver->first_name . ' ' . $receiver->last_name;
            $sender_text = $receiver->first_name . ' ' . $receiver->last_name . ' received your payment';
            $receiver_text = 'A payment from ' . $dd->first_name . ' ' . $dd->last_name . ' was successfully received';
        } else {
            $receiver_subject = 'New successful transaction';
            $sender_subject = 'Receipt from ' . $receiver->first_name . ' ' . $receiver->last_name;
            $sender_text = $receiver->first_name . ' ' . $receiver->last_name . ' received your payment, this is not real money';
            $receiver_text = 'A payment from ' . $dd->first_name . ' ' . $dd->last_name . ' was successfully received, this is not real money';
        }
        $sender_name = $dd->first_name . ' ' . $dd->last_name;
        $data = array(
            'created' => $dd->created_at,
            'sender_subject' => $sender_subject,
            'receiver_subject' => $receiver_subject,
            'receiver_name' => $receiver_name,
            'sender_name' => $sender_name,
            'website' => $set->site_name,
            'sender_text' => $sender_text,
            'receiver_text' => $receiver_text,
            'details' => $details,
            'from_amount' => $from_amount,
            'to_amount' => $to_amount,
            'charges' => $charge,
            'method' => $method,
            'reference' => $reference,
            'payment_link' => $payment_link,
            'logo' => $logo,
            'dd' => $dd
        );
        if($receiver->business()->email_receiver==1){
            Mail::send(['html' => 'emails/payment_links/rpmail'], $data, function ($r_message) use ($receiver_name, $receiver_email, $receiver_subject, $from, $site) {
                $r_message->to($receiver_email, $receiver_name)->subject($receiver_subject)->from($from, $site);
            });
        }
        if($receiver->business()->email_sender==1){
            Mail::send(['html' => 'emails/payment_links/spmail'], $data, function ($s_message) use ($sender_name, $sender_email, $sender_subject, $from, $site) {
                $s_message->to($sender_email, $sender_name)->subject($sender_subject)->from($from, $site);
            });
        }
    }
}
