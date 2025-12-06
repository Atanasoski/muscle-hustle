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
        // Workout categories
        $workoutCategories = [
            [
                'name' => 'Chest',
                'slug' => 'chest',
                'display_order' => 1,
                'icon' => '💪',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Back',
                'slug' => 'back',
                'display_order' => 2,
                'icon' => '🦾',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Legs',
                'slug' => 'legs',
                'display_order' => 3,
                'icon' => '🦵',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Shoulders',
                'slug' => 'shoulders',
                'display_order' => 4,
                'icon' => '💪',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Arms',
                'slug' => 'arms',
                'display_order' => 5,
                'icon' => '💪',
                'color' => '#ec4899',
            ],
            [
                'name' => 'Core',
                'slug' => 'core',
                'display_order' => 6,
                'icon' => '🔥',
                'color' => '#10b981',
            ],
            [
                'name' => 'Cardio',
                'slug' => 'cardio',
                'display_order' => 7,
                'icon' => '🏃',
                'color' => '#06b6d4',
            ],
        ];

        foreach ($workoutCategories as $category) {
            Category::firstOrCreate(
                ['type' => CategoryType::Workout, 'slug' => $category['slug']],
                array_merge($category, ['type' => CategoryType::Workout])
            );
        }

        $this->command->info('Workout categories seeded successfully!');

        // Food categories
        $foodCategories = [
            [
                'name' => 'Protein',
                'slug' => 'protein',
                'display_order' => 1,
                'icon' => '🍗',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Vegetables',
                'slug' => 'vegetables',
                'display_order' => 2,
                'icon' => '🥦',
                'color' => '#10b981',
            ],
            [
                'name' => 'Fruits',
                'slug' => 'fruits',
                'display_order' => 3,
                'icon' => '🍎',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Grains',
                'slug' => 'grains',
                'display_order' => 4,
                'icon' => '🌾',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Dairy',
                'slug' => 'dairy',
                'display_order' => 5,
                'icon' => '🥛',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Fats & Oils',
                'slug' => 'fats-oils',
                'display_order' => 6,
                'icon' => '🥑',
                'color' => '#10b981',
            ],
            [
                'name' => 'Snacks',
                'slug' => 'snacks',
                'display_order' => 7,
                'icon' => '🍪',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Beverages',
                'slug' => 'beverages',
                'display_order' => 8,
                'icon' => '☕',
                'color' => '#6366f1',
            ],
        ];

        foreach ($foodCategories as $category) {
            Category::firstOrCreate(
                ['type' => CategoryType::Food, 'slug' => $category['slug']],
                array_merge($category, ['type' => CategoryType::Food])
            );
        }

        $this->command->info('Food categories seeded successfully!');
    }
}
