<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class CheckoutCompleted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  Collection<int, \App\Models\Order>  $orders
     */
    public function __construct(public Collection $orders) {}
}
