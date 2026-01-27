<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'type' => CategoryType::Workout,
            'name' => ucfirst($name),
            'slug' => strtolower($name),
            'display_order' => fake()->numberBetween(1, 100),
            'icon' => fake()->randomElement(['🏋️', '💪', '🏃', '⚡', '🧘']),
            'color' => fake()->hexColor(),
        ];
    }

    /**
     * Indicate that the category is a workout category.
     */
    public function workout(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => CategoryType::Workout,
        ]);
    }
}
