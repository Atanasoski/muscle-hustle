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
        // Workout categories (equipment types)
        $workoutCategories = [
            [
                'name' => 'Bodyweight',
                'slug' => 'bodyweight',
                'display_order' => 1,
                'icon' => '🤸',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Dumbbell',
                'slug' => 'dumbbell',
                'display_order' => 2,
                'icon' => '🏋️',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Barbell',
                'slug' => 'barbell',
                'display_order' => 3,
                'icon' => '🏋️‍♂️',
                'color' => '#06b6d4',
            ],
            [
                'name' => 'Machine (Plate Loaded)',
                'slug' => 'machine-plate-loaded',
                'display_order' => 4,
                'icon' => '⚙️',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Machine (Cable)',
                'slug' => 'machine-cable',
                'display_order' => 5,
                'icon' => '🔧',
                'color' => '#10b981',
            ],
            [
                'name' => 'Cable',
                'slug' => 'cable',
                'display_order' => 6,
                'icon' => '🔗',
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Bands',
                'slug' => 'bands',
                'display_order' => 7,
                'icon' => '🎯',
                'color' => '#ec4899',
            ],
            [
                'name' => 'TRX',
                'slug' => 'trx',
                'display_order' => 8,
                'icon' => '🪢',
                'color' => '#14b8a6',
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
