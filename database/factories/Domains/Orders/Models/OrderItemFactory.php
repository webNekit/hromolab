<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Orders\Models;

use App\Domains\Catalog\Models\Analysis;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'analysis_id' => Analysis::factory(),
            'price' => fake()->randomFloat(2, 200, 15000),
            'discount' => fake()->optional(0.2)->randomFloat(2, 0, 3000),
        ];
    }
}
