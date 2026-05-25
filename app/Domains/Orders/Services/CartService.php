<?php

declare(strict_types=1);

namespace App\Domains\Orders\Services;

use App\Domains\Catalog\Models\Analysis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class CartService implements CartServiceInterface
{
    /**
     * Cart data structure: {analysis_id => quantity}
     */
    public function __construct(
        private readonly int $cacheTtl = 3600,
    ) {}

    public function add(int $analysisId, int $quantity = 1): void
    {
        $analysis = Analysis::findOrFail($analysisId);

        if (! $analysis->is_active) {
            throw new \InvalidArgumentException("Analysis {$analysisId} is not active");
        }

        $cart = $this->getCart();
        $cart[$analysisId] = ($cart[$analysisId] ?? 0) + $quantity;

        $this->saveCart($cart);
    }

    public function remove(int $analysisId): void
    {
        $cart = $this->getCart();
        unset($cart[$analysisId]);
        $this->saveCart($cart);
    }

    public function updateQuantity(int $analysisId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($analysisId);

            return;
        }

        $cart = $this->getCart();

        if (isset($cart[$analysisId])) {
            $cart[$analysisId] = $quantity;
            $this->saveCart($cart);
        }
    }

    public function clear(): void
    {
        $this->saveCart([]);
    }

    public function getItems(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return [];
        }

        $analysisIds = array_keys($cart);

        $analyses = Analysis::whereIn('id', $analysisIds)
            ->active()
            ->get()
            ->keyBy('id');

        $items = [];

        foreach ($cart as $analysisId => $quantity) {
            $analysis = $analyses->get($analysisId);

            if ($analysis === null) {
                continue;
            }

            $items[] = [
                'analysis_id' => $analysis->id,
                'name' => $analysis->name,
                'sku' => $analysis->sku,
                'price' => (float) $analysis->price,
                'quantity' => $quantity,
                'subtotal' => (float) $analysis->price * $quantity,
            ];
        }

        return $items;
    }

    public function getCount(): int
    {
        return array_sum($this->getCart());
    }

    public function getTotal(): float
    {
        $items = $this->getItems();

        return (float) array_sum(array_column($items, 'subtotal'));
    }

    public function getKey(): string
    {
        return auth()->check()
            ? 'cart:user:'.auth()->id()
            : 'cart:session:'.Session::getId();
    }

    /**
     * Get raw cart data from cache/session.
     *
     * @return array<int, int>
     */
    private function getCart(): array
    {
        return Cache::get($this->getKey(), []);
    }

    /**
     * Save cart data to cache/session.
     *
     * @param  array<int, int>  $cart
     */
    private function saveCart(array $cart): void
    {
        Cache::put($this->getKey(), $cart, now()->addSeconds($this->cacheTtl));
    }

    /**
     * Merge guest cart into user cart on login.
     */
    public function mergeGuestCartIntoUserCart(): void
    {
        if (! auth()->check()) {
            return;
        }

        $guestKey = 'cart:session:'.Session::getId();
        $userKey = 'cart:user:'.auth()->id();

        $guestCart = Cache::get($guestKey, []);
        $userCart = Cache::get($userKey, []);

        foreach ($guestCart as $analysisId => $quantity) {
            $userCart[$analysisId] = ($userCart[$analysisId] ?? 0) + $quantity;
        }

        Cache::put($userKey, $userCart, now()->addSeconds($this->cacheTtl));
        Cache::forget($guestKey);
    }
}
