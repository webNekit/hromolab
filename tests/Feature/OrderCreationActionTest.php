<?php

declare(strict_types=1);

use App\Domains\Auth\Models\User;
use App\Domains\Catalog\Models\Analysis;
use App\Domains\Laboratories\Models\AppointmentSlot;
use App\Domains\Laboratories\Models\Laboratory;
use App\Domains\Laboratories\Models\LaboratoryWorkingHour;
use App\Domains\Orders\Actions\OrderCreationAction;
use App\Domains\Orders\DataTransferObjects\CheckoutDTO;
use App\Domains\Orders\Events\OrderCreatedEvent;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Services\CartServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Event::fake();
    Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::create(['name' => 'lab-assistant', 'guard_name' => 'web']);
    Role::create(['name' => 'patient', 'guard_name' => 'web']);
});

it('creates an order from checkout dto', function (): void {
    $user = User::factory()->patient()->create();
    $laboratory = Laboratory::factory()->create();
    $analysis1 = Analysis::factory()->active()->create(['price' => 1000]);
    $analysis2 = Analysis::factory()->active()->create(['price' => 2000]);

    // Create working hours and an available slot
    LaboratoryWorkingHour::factory()->create([
        'laboratory_id' => $laboratory->id,
        'day_of_week' => now()->addDays(1)->format('w'),
        'open_time' => '09:00',
        'close_time' => '18:00',
        'slot_interval_minutes' => 15,
    ]);

    $slotDatetime = now()->addDays(1)->setTime(10, 0, 0);

    AppointmentSlot::create([
        'laboratory_id' => $laboratory->id,
        'slot_datetime' => $slotDatetime,
        'capacity' => 2,
        'booked' => 0,
        'is_available' => true,
    ]);

    $dto = new CheckoutDTO(
        userId: $user->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [
            ['analysis_id' => $analysis1->id, 'quantity' => 1],
            ['analysis_id' => $analysis2->id, 'quantity' => 2],
        ],
        paymentMethod: 'online',
    );

    $cartService = app(CartServiceInterface::class);
    $cartService->add($analysis1->id);
    $cartService->add($analysis2->id, 2);

    $action = app(OrderCreationAction::class);
    $order = $action->execute($dto);

    expect($order)->toBeInstanceOf(Order::class);
    expect($order->user_id)->toBe($user->id);
    expect($order->laboratory_id)->toBe($laboratory->id);
    expect($order->payment_status)->toBe('pending');
    expect($order->current_status)->toBe('new');
    expect((float) $order->total_price)->toBe((float) (1000 + 2000 * 2));
    expect($order->order_number)->not->toBeNull();

    assertDatabaseCount('orders', 1);
    assertDatabaseCount('order_items', 2);
    assertDatabaseCount('order_status_histories', 1);

    assertDatabaseHas('order_items', [
        'order_id' => $order->id,
        'analysis_id' => $analysis1->id,
        'price' => 1000,
    ]);

    assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'status' => 'new',
    ]);
});

it('dispatches OrderCreatedEvent after order creation', function (): void {
    $user = User::factory()->patient()->create();
    $laboratory = Laboratory::factory()->create();
    $analysis = Analysis::factory()->active()->create();

    $slotDatetime = now()->addDays(1)->setTime(10, 0, 0);

    AppointmentSlot::create([
        'laboratory_id' => $laboratory->id,
        'slot_datetime' => $slotDatetime,
        'capacity' => 2,
        'booked' => 0,
        'is_available' => true,
    ]);

    $dto = new CheckoutDTO(
        userId: $user->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [['analysis_id' => $analysis->id, 'quantity' => 1]],
        paymentMethod: 'online',
    );

    $cartService = app(CartServiceInterface::class);
    $cartService->add($analysis->id);

    $action = app(OrderCreationAction::class);
    $action->execute($dto);

    Event::assertDispatched(OrderCreatedEvent::class);
});

it('prevents double booking of the same time slot', function (): void {
    $user = User::factory()->patient()->create();
    $user2 = User::factory()->patient()->create();
    $laboratory = Laboratory::factory()->create();
    $analysis = Analysis::factory()->active()->create();

    $slotDatetime = now()->addDays(1)->setTime(10, 0, 0);

    $slot = AppointmentSlot::create([
        'laboratory_id' => $laboratory->id,
        'slot_datetime' => $slotDatetime,
        'capacity' => 1,
        'booked' => 0,
        'is_available' => true,
    ]);

    $dto1 = new CheckoutDTO(
        userId: $user->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [['analysis_id' => $analysis->id, 'quantity' => 1]],
        paymentMethod: 'online',
    );

    $action = app(OrderCreationAction::class);
    $cartService = app(CartServiceInterface::class);
    $cartService->add($analysis->id);
    $action->execute($dto1);

    // Try booking the same slot with another user
    $cartService2 = app(CartServiceInterface::class);
    $cartService2->add($analysis->id);

    $dto2 = new CheckoutDTO(
        userId: $user2->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [['analysis_id' => $analysis->id, 'quantity' => 1]],
        paymentMethod: 'online',
    );

    expect(fn () => $action->execute($dto2))->toThrow(RuntimeException::class, 'Selected time slot is no longer available');
});

it('creates order with correct status history', function (): void {
    $user = User::factory()->patient()->create();
    $laboratory = Laboratory::factory()->create();
    $analysis = Analysis::factory()->active()->create();

    $slotDatetime = now()->addDays(1)->setTime(10, 0, 0);

    AppointmentSlot::create([
        'laboratory_id' => $laboratory->id,
        'slot_datetime' => $slotDatetime,
        'capacity' => 2,
        'booked' => 0,
        'is_available' => true,
    ]);

    $dto = new CheckoutDTO(
        userId: $user->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [['analysis_id' => $analysis->id, 'quantity' => 1]],
        paymentMethod: 'online',
    );

    $cartService = app(CartServiceInterface::class);
    $cartService->add($analysis->id);

    $action = app(OrderCreationAction::class);
    $order = $action->execute($dto);

    expect($order->current_status)->toBe('new');
    expect($order->payment_status)->toBe('pending');

    $order->updateStatus('processing', $user);
    $order->updateStatus('completed', $user);

    expect($order->statusHistories)->toHaveCount(3);
    expect($order->statusHistories[0]->status)->toBe('completed');
});

it('rolls back transaction on failure', function (): void {
    $user = User::factory()->patient()->create();
    $laboratory = Laboratory::factory()->create();
    $analysis = Analysis::factory()->active()->create();

    $slotDatetime = now()->addDays(1)->setTime(10, 0, 0);

    AppointmentSlot::create([
        'laboratory_id' => $laboratory->id,
        'slot_datetime' => $slotDatetime,
        'capacity' => 2,
        'booked' => 0,
        'is_available' => true,
    ]);

    $dto = new CheckoutDTO(
        userId: $user->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [
            ['analysis_id' => 99999, 'quantity' => 1],
        ],
        paymentMethod: 'online',
    );

    $action = app(OrderCreationAction::class);

    try {
        $action->execute($dto);
    } catch (Exception) {
        // Expected
    }

    assertDatabaseCount('orders', 0);
    assertDatabaseCount('order_items', 0);
});

it('clears the cart after successful order', function (): void {
    $user = User::factory()->patient()->create();
    $laboratory = Laboratory::factory()->create();
    $analysis = Analysis::factory()->active()->create();

    $slotDatetime = now()->addDays(1)->setTime(10, 0, 0);

    AppointmentSlot::create([
        'laboratory_id' => $laboratory->id,
        'slot_datetime' => $slotDatetime,
        'capacity' => 2,
        'booked' => 0,
        'is_available' => true,
    ]);

    $dto = new CheckoutDTO(
        userId: $user->id,
        laboratoryId: $laboratory->id,
        appointmentDatetime: $slotDatetime,
        items: [['analysis_id' => $analysis->id, 'quantity' => 1]],
        paymentMethod: 'online',
    );

    $cartService = app(CartServiceInterface::class);
    $cartService->add($analysis->id);

    expect($cartService->getCount())->toBe(1);

    $action = app(OrderCreationAction::class);
    $action->execute($dto);

    expect($cartService->getCount())->toBe(0);
});
