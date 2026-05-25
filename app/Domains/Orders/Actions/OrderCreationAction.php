<?php

declare(strict_types=1);

namespace App\Domains\Orders\Actions;

use App\Domains\Catalog\Models\Analysis;
use App\Domains\Laboratories\Models\AppointmentSlot;
use App\Domains\Orders\DataTransferObjects\CheckoutDTO;
use App\Domains\Orders\Events\OrderCreatedEvent;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Orders\Models\OrderStatusHistory;
use App\Domains\Orders\Services\CartService;
use Illuminate\Support\Facades\DB;

class OrderCreationAction
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    /**
     * Create a new order from checkout data.
     *
     * @throws \RuntimeException If slot is not available
     */
    public function execute(CheckoutDTO $dto): Order
    {
        return DB::transaction(function () use ($dto): Order {
            // Find or create the appointment slot to prevent double booking
            $slot = AppointmentSlot::firstOrCreate(
                [
                    'laboratory_id' => $dto->laboratoryId,
                    'slot_datetime' => $dto->appointmentDatetime,
                ],
                [
                    'capacity' => 1,
                    'booked' => 0,
                    'is_available' => true,
                ],
            );

            if (! $slot->hasCapacity()) {
                throw new \RuntimeException('Selected time slot is no longer available');
            }

            // Book the slot
            $slot->book();

            // Create the order
            $order = Order::create([
                'user_id' => $dto->userId,
                'laboratory_id' => $dto->laboratoryId,
                'order_number' => Order::generateOrderNumber(),
                'appointment_datetime' => $dto->appointmentDatetime,
                'total_price' => $this->calculateTotal($dto->items),
                'payment_status' => 'pending',
                'current_status' => 'new',
                'promo_code' => $dto->promoCode,
            ]);

            // Record initial status history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'new',
                'previous_status' => null,
                'changed_by_user_id' => $dto->userId,
            ]);

            // Create order items from cart
            foreach ($dto->items as $itemData) {
                $analysis = Analysis::findOrFail($itemData['analysis_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'analysis_id' => $analysis->id,
                    'price' => $analysis->price,
                    'discount' => 0,
                ]);
            }

            // Clear the user's cart
            $this->cartService->clear();

            // Dispatch event for notifications
            OrderCreatedEvent::dispatch($order);

            return $order->load(['items.analysis', 'laboratory']);
        });
    }

    /**
     * Calculate total from items.
     *
     * @param  array<int, array{analysis_id: int, quantity: int}>  $items
     */
    private function calculateTotal(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $analysis = Analysis::findOrFail($item['analysis_id']);
            $total += (float) $analysis->price * $item['quantity'];
        }

        return $total;
    }
}
