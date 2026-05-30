<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venue>
 */
class VenueFactory extends Factory
{
    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'name' => $this->faker->company,
            // 'description' => $this->faker->optional()->paragraph,
            // 'address' => $this->faker->address,
            // 'city' => $this->faker->city,
            // 'state' => $this->faker->optional()->state,
            // 'zip_code' => $this->faker->postcode,
            // 'country' => $this->faker->country,
            // 'capacity' => $this->faker->numberBetween(50, 1000),
            // 'price_per_hour' => $this->faker->optional()->randomFloat(2, 10, 500),
            // 'manager_id' => User::where('user_type', 'local_manager')->inRandomOrder()->value('user_id'),
            // 'is_active' => $this->faker->boolean(90),
        ];
    }
}