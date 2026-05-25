<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Laboratories\Models;

use App\Domains\Laboratories\Models\Laboratory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Laboratory>
 */
class LaboratoryFactory extends Factory
{
    protected $model = Laboratory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Лаборатория',
            'address' => fake()->address(),
            'latitude' => fake()->latitude(55.5, 56.0),
            'longitude' => fake()->longitude(37.0, 38.0),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
