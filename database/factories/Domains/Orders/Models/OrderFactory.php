<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Orders\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Laboratories\Models\Laboratory;
use App\Domains\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', '+14 days');

        return [
            'user_id' => User::factory(),
            'laboratory_id' => Laboratory::factory(),
            'order_number' => Order::generateOrderNumber(),
            'appointment_datetime' => $date,
            'total_price' => fake()->randomFloat(2, 500, 30000),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'refunded']),
            'current_status' => fake()->randomElement(['new', 'processing', 'ready_for_lab', 'analyzing', 'completed', 'cancelled']),
            'promo_code' => fake()->optional()->word(),
            'discount_amount' => fake()->optional(0.3)->randomFloat(2, 0, 5000),
        ];
    }

    public function withStatus(string $status): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => $status,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'current_status' => 'cancelled',
        ]);
    }
}
