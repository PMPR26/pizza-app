<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlacedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  Collection<int, \App\Models\Order>  $orders
     */
    public function __construct(public Collection $orders) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de pedido — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-placed',
        );
    }
}
