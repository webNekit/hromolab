<?php

declare(strict_types=1);

namespace Database\Factories\Domains\Laboratories\Models;

use App\Domains\Laboratories\Models\Laboratory;
use App\Domains\Laboratories\Models\LaboratoryWorkingHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaboratoryWorkingHour>
 */
class LaboratoryWorkingHourFactory extends Factory
{
    protected $model = LaboratoryWorkingHour::class;

    public function definition(): array
    {
        return [
            'laboratory_id' => Laboratory::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'open_time' => '07:00',
            'close_time' => '20:00',
            'slot_interval_minutes' => 15,
        ];
    }

    public function weekday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => fake()->numberBetween(1, 5),
            'open_time' => '07:00',
            'close_time' => '20:00',
        ]);
    }

    public function saturday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 6,
            'open_time' => '08:00',
            'close_time' => '18:00',
        ]);
    }

    public function sunday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 0,
            'open_time' => '09:00',
            'close_time' => '16:00',
        ]);
    }
}
