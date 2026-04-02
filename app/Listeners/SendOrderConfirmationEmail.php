<?php

namespace App\Listeners;

use App\Events\CheckoutCompleted;
use App\Jobs\SendOrderEmailJob;

class SendOrderConfirmationEmail
{
    /**
     * Un solo correo con el pedido completo (todas las líneas del carrito).
     */
    public function handle(CheckoutCompleted $event): void
    {
        SendOrderEmailJob::dispatch($event->orders->pluck('id')->values()->all());
    }
}
