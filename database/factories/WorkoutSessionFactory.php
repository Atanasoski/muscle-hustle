<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkoutTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutSession>
 */
class WorkoutSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $performedAt = fake()->dateTimeBetween('-30 days', 'now');

        return [
            'user_id' => User::factory(),
            'workout_template_id' => WorkoutTemplate::factory(),
            'performed_at' => $performedAt,
            'completed_at' => fake()->boolean(80) ? fake()->dateTimeBetween($performedAt, '+2 hours') : null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }
}
