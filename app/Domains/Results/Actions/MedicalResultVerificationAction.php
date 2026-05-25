<?php

declare(strict_types=1);

namespace App\Domains\Results\Actions;

use App\Domains\Auth\Models\User;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Results\Events\MedicalResultVerifiedEvent;
use App\Domains\Results\Models\MedicalResult;
use App\Domains\Results\Services\MedicalPdfGenerator;

class MedicalResultVerificationAction
{
    public function __construct(
        private readonly MedicalPdfGenerator $pdfGenerator,
    ) {}

    /**
     * Verify and approve medical results.
     *
     * @param  array<string, mixed>  $parameterValues
     *
     * @throws \InvalidArgumentException If user lacks permissions
     */
    public function execute(
        int $orderItemId,
        array $parameterValues,
        User $user,
    ): MedicalResult {
        if (! $user->hasRole(['lab-assistant', 'super-admin'])) {
            throw new \InvalidArgumentException('Only lab assistants can verify results');
        }

        $orderItem = OrderItem::with(['order', 'analysis'])->findOrFail($orderItemId);

        $result = MedicalResult::updateOrCreate(
            ['order_item_id' => $orderItemId],
            [
                'lab_assistant_id' => $user->id,
                'parameter_values' => $parameterValues,
                'verified_at' => now(),
            ],
        );

        // Generate PDF
        $pdfPath = $this->pdfGenerator->generate($orderItem, $parameterValues);
        $result->update(['pdf_path' => $pdfPath]);

        // Check if all order items are now completed
        $order = $orderItem->order;

        if ($order->areAllItemsCompleted()) {
            $order->updateStatus('completed', $user, 'Все результаты готовы');
        } elseif ($order->current_status === 'new') {
            $order->updateStatus('analyzing', $user, 'Результаты обрабатываются');
        }

        // Dispatch notification event
        MedicalResultVerifiedEvent::dispatch($result);

        return $result->load(['orderItem.analysis', 'orderItem.order.user']);
    }
}
