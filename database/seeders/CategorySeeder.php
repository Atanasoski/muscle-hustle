<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
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

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}
