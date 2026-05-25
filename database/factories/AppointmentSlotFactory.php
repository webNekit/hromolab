<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Laboratories\Models\AppointmentSlot;
use App\Domains\Laboratories\Models\Laboratory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AppointmentSlot>
 */
class AppointmentSlotFactory extends Factory
{
    protected $model = AppointmentSlot::class;

    public function definition(): array
    {
        return [
            'laboratory_id' => Laboratory::factory(),
            'slot_datetime' => fake()->dateTimeBetween('today', '+14 days'),
            'capacity' => fake()->numberBetween(1, 5),
            'booked' => fake()->numberBetween(0, 3),
            'is_available' => true,
        ];
    }

    public function fullyBooked(): static
    {
        return $this->state(fn (array $attributes) => [
            'booked' => $attributes['capacity'] ?? 1,
            'is_available' => false,
        ]);
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'booked' => 0,
            'is_available' => true,
        ]);
    }
}
