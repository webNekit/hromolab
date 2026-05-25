<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Domains\Orders\Services\CartServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CartWidget extends Component
{
    #[On('cart-add')]
    public function addToCart(int $analysisId): void
    {
        app(CartServiceInterface::class)->add($analysisId);
        $this->dispatch('cart-updated');
    }

    #[On('cart-removed')]
    public function refresh(): void
    {
        // Just refresh
    }

    #[On('cart-updated')]
    public function updated(): void
    {
        // Just refresh
    }

    #[Computed]
    public function count(): int
    {
        return app(CartServiceInterface::class)->getCount();
    }

    #[Computed]
    public function total(): float
    {
        return app(CartServiceInterface::class)->getTotal();
    }

    public function render(): View
    {
        return view('livewire.cart-widget', [
            'count' => $this->count,
            'total' => $this->total,
        ]);
    }
}
