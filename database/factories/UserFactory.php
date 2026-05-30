<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     */
    public function definition(): array
    {
        return [
            // 'username' => $this->faker->unique()->userName,
            // 'email' => $this->faker->unique()->safeEmail,
            // 'password_hash' => Hash::make('password'),
            // 'first_name' => $this->faker->firstName,
            // 'last_name' => $this->faker->lastName,
            // 'phone' => $this->faker->optional()->phoneNumber,
            // 'profile_picture' => $this->faker->optional()->imageUrl(),
            // 'user_type' => $this->faker->randomElement(['admin', 'event_manager', 'local_manager', 'user']),
            // 'is_active' => $this->faker->boolean(90), // 90% de probabilidades
        ];
    }
}