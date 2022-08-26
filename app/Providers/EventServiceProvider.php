<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Listeners\WebhookResponse;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        WebhookCallSucceededEvent::class => [
            WebhookResponse::class,
        ],        
        WebhookCallFailedEvent::class => [
            WebhookResponse::class,
        ],        
        FinalWebhookCallFailedEvent::class => [
            WebhookResponse::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
