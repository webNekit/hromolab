<?php

declare(strict_types=1);

use App\Domains\Auth\Models\User;
use App\Domains\Catalog\Models\Analysis;
use App\Domains\Orders\Services\CartServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed();
    Cache::flush();
});

it('adds an analysis to the cart', function (): void {
    $analysis = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis->id);

    expect($cart->getCount())->toBe(1);
    expect($cart->getItems())->toHaveCount(1);
    expect($cart->getItems()[0]['analysis_id'])->toBe($analysis->id);
});

it('adds multiple quantities of the same analysis', function (): void {
    $analysis = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis->id, 3);
    $cart->add($analysis->id, 2);

    expect($cart->getCount())->toBe(5);
    expect($cart->getItems()[0]['quantity'])->toBe(5);
    expect($cart->getItems()[0]['subtotal'])->toBe((float) $analysis->price * 5);
});

it('removes an analysis from the cart', function (): void {
    $analysis1 = Analysis::factory()->active()->create();
    $analysis2 = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis1->id);
    $cart->add($analysis2->id);

    expect($cart->getCount())->toBe(2);

    $cart->remove($analysis1->id);

    expect($cart->getCount())->toBe(1);
    expect($cart->getItems())->toHaveCount(1);
    expect($cart->getItems()[0]['analysis_id'])->toBe($analysis2->id);
});

it('updates quantity of an analysis in the cart', function (): void {
    $analysis = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis->id, 2);

    $cart->updateQuantity($analysis->id, 5);

    expect($cart->getCount())->toBe(5);
    expect($cart->getItems()[0]['quantity'])->toBe(5);
});

it('removes item when quantity set to zero', function (): void {
    $analysis = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis->id, 2);

    $cart->updateQuantity($analysis->id, 0);

    expect($cart->getCount())->toBe(0);
    expect($cart->getItems())->toBeEmpty();
});

it('calculates total correctly', function (): void {
    $analysis1 = Analysis::factory()->active()->create(['price' => 1000]);
    $analysis2 = Analysis::factory()->active()->create(['price' => 2000]);

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis1->id, 2);
    $cart->add($analysis2->id, 3);

    $expectedTotal = (1000 * 2) + (2000 * 3);

    expect($cart->getTotal())->toBe((float) $expectedTotal);
});

it('clears the cart', function (): void {
    $analysis = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis->id, 3);

    expect($cart->getCount())->toBe(3);

    $cart->clear();

    expect($cart->getCount())->toBe(0);
    expect($cart->getItems())->toBeEmpty();
});

it('uses different keys for different users', function (): void {
    $cart = app(CartServiceInterface::class);

    $guestKey = $cart->getKey();

    $user = User::factory()->patient()->create();
    actingAs($user);

    $userKey = $cart->getKey();

    expect($guestKey)->not->toBe($userKey);
});

it('throws exception when adding inactive analysis', function (): void {
    $analysis = Analysis::factory()->inactive()->create();

    $cart = app(CartServiceInterface::class);

    expect(fn () => $cart->add($analysis->id))
        ->toThrow(InvalidArgumentException::class);
});

it('persists cart across requests via cache', function (): void {
    $analysis = Analysis::factory()->active()->create();

    $cart = app(CartServiceInterface::class);
    $cart->add($analysis->id);

    // Simulate new request by resolving a fresh instance
    $freshCart = app(CartServiceInterface::class);

    expect($freshCart->getCount())->toBe(1);
});

it('handles empty cart gracefully', function (): void {
    $cart = app(CartServiceInterface::class);

    expect($cart->getCount())->toBe(0);
    expect($cart->getItems())->toBeArray();
    expect($cart->getItems())->toBeEmpty();
    expect($cart->getTotal())->toBe(0.0);
});
