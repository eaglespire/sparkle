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

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $subject;
    public $message;
    public $to;
    public $name;
    public function __construct($to, $name, $subject, $message)
    {
        $this->subject=$subject;
        $this->message=$message;
        $this->to=$to;
        $this->name=$name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $set=Settings::first();
        if($set->email_notify==1){
            send_email($this->to, $this->name, $this->subject, $this->message);  
        }
    }
}
