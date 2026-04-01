<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\SendOrderEmailJob;

class SendOrderConfirmationEmail
{
    public function handle(OrderCreated $event): void
    {
        SendOrderEmailJob::dispatch($event->order);
    }
}
