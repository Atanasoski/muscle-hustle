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
        // Workout categories (exercise types)
        $workoutCategories = [
            [
                'name' => 'Compound',
                'slug' => 'compound',
                'display_order' => 1,
                'icon' => '🏋️',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Isolation',
                'slug' => 'isolation',
                'display_order' => 2,
                'icon' => '💪',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Cardio',
                'slug' => 'cardio',
                'display_order' => 3,
                'icon' => '🏃',
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Plyometrics',
                'slug' => 'plyometrics',
                'display_order' => 4,
                'icon' => '⚡',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Mobility',
                'slug' => 'mobility',
                'display_order' => 5,
                'icon' => '🧘',
                'color' => '#10b981',
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
