<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Catalog\Models\Analysis;
use App\Domains\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Analysis>
 */
class AnalysisFactory extends Factory
{
    protected $model = Analysis::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(fake()->numberBetween(2, 5), true);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => 'HL-'.fake()->unique()->bothify('####??'),
            'description' => fake()->paragraph(),
            'preparation' => fake()->optional()->sentence(),
            'biomaterial' => fake()->randomElement(['Кровь венозная', 'Кровь капиллярная', 'Моча', 'Кал', 'Мазок']),
            'price' => fake()->randomFloat(2, 200, 15000),
            'lead_time_days' => fake()->numberBetween(1, 14),
            'reference_ranges' => null,
            'is_active' => true,
            'is_popular' => fake()->boolean(20),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_popular' => true,
        ]);
    }
}
