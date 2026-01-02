<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MuscleGroup>
 */
class MuscleGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'body_region' => fake()->randomElement(['upper', 'lower', 'core']),
        ];
    }

    /**
     * Indicate that the muscle group is in the upper body region.
     */
    public function upperBody(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_region' => 'upper',
        ]);
    }

    /**
     * Indicate that the muscle group is in the lower body region.
     */
    public function lowerBody(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_region' => 'lower',
        ]);
    }

    /**
     * Indicate that the muscle group is in the core region.
     */
    public function core(): static
    {
        return $this->state(fn (array $attributes) => [
            'body_region' => 'core',
        ]);
    }
}
