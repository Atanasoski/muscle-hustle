<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Workout categories (exercise types/modalities)
        $workoutCategories = [
            [
                'name' => 'Strength Training',
                'slug' => 'strength-training',
                'display_order' => 1,
                'icon' => '🏋️',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Functional Training',
                'slug' => 'functional-training',
                'display_order' => 2,
                'icon' => '🤸',
                'color' => '#10b981',
            ],
            [
                'name' => 'Cardio',
                'slug' => 'cardio',
                'display_order' => 3,
                'icon' => '🏃',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Power/Olympic Lifting',
                'slug' => 'power-olympic-lifting',
                'display_order' => 4,
                'icon' => '⚡',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Mobility & Flexibility',
                'slug' => 'mobility-flexibility',
                'display_order' => 5,
                'icon' => '🧘',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Bodyweight',
                'slug' => 'bodyweight',
                'display_order' => 6,
                'icon' => '🤸‍♂️',
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Hybrid/CrossFit',
                'slug' => 'hybrid-crossfit',
                'display_order' => 7,
                'icon' => '🔥',
                'color' => '#ec4899',
            ],
        ];

        foreach ($workoutCategories as $category) {
            Category::firstOrCreate(
                ['type' => CategoryType::Workout, 'slug' => $category['slug']],
                array_merge($category, ['type' => CategoryType::Workout])
            );
        }

        $this->command->info('Workout categories seeded successfully!');
    }
}
