<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Results\Models\MedicalResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalResult>
 */
class MedicalResultFactory extends Factory
{
    protected $model = MedicalResult::class;

    public function definition(): array
    {
        return [
            'order_item_id' => OrderItem::factory(),
            'lab_assistant_id' => User::factory(),
            'parameter_values' => [
                'hemoglobin' => ['value' => fake()->numberBetween(110, 170), 'unit' => 'g/L', 'reference' => '120-160'],
                'wbc' => ['value' => fake()->randomFloat(1, 3, 12), 'unit' => 'x10^9/L', 'reference' => '4.0-9.0'],
                'rbc' => ['value' => fake()->randomFloat(2, 3.5, 5.5), 'unit' => 'x10^12/L', 'reference' => '3.8-5.3'],
            ],
            'pdf_path' => 'medical_results/'.now()->format('Y/m/d').'/result_test.pdf',
            'download_count' => fake()->numberBetween(0, 5),
            'verified_at' => fake()->optional(0.7)->dateTimeBetween('-7 days'),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => now(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verified_at' => null,
            'pdf_path' => null,
        ]);
    }
}
