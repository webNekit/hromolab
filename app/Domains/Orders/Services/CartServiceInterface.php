<?php

declare(strict_types=1);

namespace App\Domains\Orders\Services;

interface CartServiceInterface
{
    /**
     * Add an analysis to the cart.
     */
    public function add(int $analysisId, int $quantity = 1): void;

    /**
     * Remove an analysis from the cart.
     */
    public function remove(int $analysisId): void;

    /**
     * Update quantity of an analysis in the cart.
     */
    public function updateQuantity(int $analysisId, int $quantity): void;

    /**
     * Clear the entire cart.
     */
    public function clear(): void;

    /**
     * Get all cart items with analysis details.
     *
     * @return array<int, array{analysis_id: int, name: string, sku: string, price: float, quantity: int, subtotal: float}>
     */
    public function getItems(): array;

    /**
     * Get total number of items in cart.
     */
    public function getCount(): int;

    /**
     * Get total price of cart.
     */
    public function getTotal(): float;

    /**
     * Get cart key identifier (session or user based).
     */
    public function getKey(): string;
}
