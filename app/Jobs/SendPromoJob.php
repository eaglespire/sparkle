<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Settings;
use App\Models\User;

class SendPromoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $subject;
    public $message;

    public function __construct($subject, $message)
    {
        $this->subject=$subject;
        $this->message=$message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $set=Settings::first();
        $user=User::whereemail_verify(1)->get();
        foreach ($user as $val) {
            if($set->email_notify==1){
                send_email($val->email, $val->first_name, $this->subject, $this->message);
            }
        }     
    }
}
