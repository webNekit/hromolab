<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Orders\Models;

use App\Domains\Orders\Models\PromoCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromoCode>
 */
class PromoCodeFactory extends Factory
{
    protected $model = PromoCode::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('PROMO-####'),
            'discount_type' => fake()->randomElement(['percent', 'fixed']),
            'discount_value' => fake()->randomFloat(2, 5, 50),
            'max_uses' => fake()->numberBetween(10, 1000),
            'uses_count' => fake()->numberBetween(0, 10),
            'valid_from' => now()->subDays(fake()->numberBetween(0, 30)),
            'valid_until' => now()->addDays(fake()->numberBetween(30, 365)),
            'is_active' => true,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_until' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
