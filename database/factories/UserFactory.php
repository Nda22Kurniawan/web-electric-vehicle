<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => $this->generatePhoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'customer', // Default role
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Generate Indonesian phone number format.
     *
     * @return string
     */
    private function generatePhoneNumber(): string
    {
        $prefixes = ['0812', '0813', '0814', '0815', '0816', '0817', '0818', '0819', '0821', '0822', '0823'];
        $prefix = fake()->randomElement($prefixes);
        $number = fake()->numerify('########');
        
        return $prefix . $number;
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is a mechanic.
     */
    public function mechanic(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'mechanic',
        ]);
    }

    /**
     * Indicate that the user is a customer.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'customer',
        ]);
    }
}