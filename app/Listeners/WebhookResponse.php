<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Webhook;

class WebhookResponse
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //dd($event);
        $payload=json_encode($event->payload);
        $reference=json_decode($payload);
        $data=new Webhook();
        $data->uuid=$event->uuid;
        $data->url=$event->webhookUrl;
        $data->payload=json_encode($event->payload);
        $data->reference=$reference->reference;
        $data->response=json_encode($event->response);
        $data->headers=json_encode($event->headers);
        $data->response_status_code=$event->response->getStatusCode();
        $data->attempts=$event->attempt;
        $data->save();
    }
}
