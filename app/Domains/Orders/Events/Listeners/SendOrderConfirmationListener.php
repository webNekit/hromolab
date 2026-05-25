<?php

declare(strict_types=1);

namespace App\Domains\Orders\Events\Listeners;

use App\Domains\Orders\Events\OrderCreatedEvent;
use App\Notifications\OrderConfirmationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmationListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        $order = $event->order;

        if ($order->user !== null) {
            $order->user->notify(new OrderConfirmationNotification($order));
        }
    }
}
