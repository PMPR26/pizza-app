<?php

namespace App\Jobs;

use App\Mail\OrderPlacedMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderEmailJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    /** Evita dos envíos si el mismo pedido se encolara dos veces. */
    public int $uniqueFor = 3600;

    /**
     * Solo IDs: la cola serializa bien y no rompe al cambiar la forma del payload (Collection vs Order suelto).
     *
     * @param  array<int, int>  $orderIds
     */
    public function __construct(public array $orderIds) {}

    public function uniqueId(): string
    {
        $ids = $this->orderIds;
        sort($ids);

        return 'order-mail:'.implode('-', $ids);
    }


    public function handle(): void
    {
        if ($this->orderIds === []) {
            return;
        }

        $orders = Order::query()
            ->with(['user', 'pizza.ingredients'])
            ->whereIn('id', $this->orderIds)
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        $first = $orders->first();
        if (! $first->user) {
            return;
        }

        $to = $first->user->email;
        $mailable = new OrderPlacedMail($orders);

        $pending = Mail::to($to);
        $bcc = config('mail.order_bcc');
        if (is_string($bcc) && filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
            $pending->bcc($bcc);
        }

        $pending->send($mailable);

        Log::info('Correo de pedido enviado', [
            'to' => $to,
            'order_ids' => $this->orderIds,
        ]);
    }
}
