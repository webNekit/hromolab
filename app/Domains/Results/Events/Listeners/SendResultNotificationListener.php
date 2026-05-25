<?php

declare(strict_types=1);

namespace App\Domains\Results\Events\Listeners;

use App\Domains\Results\Events\MedicalResultVerifiedEvent;
use App\Notifications\MedicalResultReadyNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendResultNotificationListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(MedicalResultVerifiedEvent $event): void
    {
        $result = $event->result;
        $order = $result->orderItem->order;

        if ($order->user !== null) {
            $order->user->notify(new MedicalResultReadyNotification($result));
        }
    }
}
