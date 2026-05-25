<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('super-admin'));
    }

    public function labAssistant(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('lab-assistant'));
    }

    public function patient(): static
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole('patient'));
    }
}
