<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $this->order->loadMissing([
            'user',
            'pizza.ingredients',
        ]);

        if (!$this->order->user) {
            return;
        }

        Mail::to($this->order->user->email)->send(new OrderPlacedMail($this->order));
    }
}
