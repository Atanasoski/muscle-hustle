<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutTemplate>
 */
class WorkoutTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plan_id' => \App\Models\Plan::factory(),
            'name' => fake()->words(3, true).' Workout',
            'description' => fake()->sentence(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'week_number' => 1,
            'order_index' => 0,
        ];
    }
}
