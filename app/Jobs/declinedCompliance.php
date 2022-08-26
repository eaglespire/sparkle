<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Settings;
use App\Models\User;
use App\Models\Logo;

class declinedCompliance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user;
    public $reason;
    public function __construct($user, $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::whereid($this->user)->first();
        $name = $user->first_name . ' ' . $user->last_name;
        $to = $user->email;
        $set = Settings::first();
        $subject = 'Resubmit Compliance';
        $mlogo = Logo::first();
        $from = env('MAIL_USERNAME');
        $logo = url('/') . '/asset/' . $mlogo->dark;
        $text = str_replace(
            "{{reason}}",
            $this->reason,
            str_replace(
                "{{logo}}",
                $logo,
                (str_replace(
                    "{{site_name}}",
                    $set->site_name,
                    str_replace("{{message}}", $set->compliance_resubmit, $set->email_template)
                ))
            )
        );
        Mail::send([], [], function ($message) use ($subject, $from, $set, $to, $text, $name) {
            $message->to($to, $name)
                ->subject($subject)
                ->from($from, $set->site_name)
                ->setBody($text, 'text/html');
        });
    }
}
