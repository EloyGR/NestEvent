<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDatetime = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $endDatetime = $this->faker->dateTimeBetween($startDatetime, $startDatetime->format('Y-m-d H:i:s') . ' +2 days');

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'organizer_id' => User::factory(), // Crear un usuario relacionado como organizador
            'event_type' => $this->faker->optional()->word,
            'expected_attendance' => $this->faker->optional()->numberBetween(50, 500),
            'is_public' => $this->faker->boolean(80), // 80% de probabilidad de ser publico
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled']),
        ];
    }
}