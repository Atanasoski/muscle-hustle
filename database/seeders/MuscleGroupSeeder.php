<?php

namespace Database\Seeders;

use App\Models\MuscleGroup;
use Illuminate\Database\Seeder;

class MuscleGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $muscleGroups = [
            // Upper body
            ['name' => 'Chest', 'body_region' => 'upper'],
            ['name' => 'Lats', 'body_region' => 'upper'],
            ['name' => 'Upper Back', 'body_region' => 'upper'],
            ['name' => 'Lower Back', 'body_region' => 'upper'],
            ['name' => 'Front Delts', 'body_region' => 'upper'],
            ['name' => 'Side Delts', 'body_region' => 'upper'],
            ['name' => 'Rear Delts', 'body_region' => 'upper'],
            ['name' => 'Traps', 'body_region' => 'upper'],
            ['name' => 'Biceps', 'body_region' => 'upper'],
            ['name' => 'Triceps', 'body_region' => 'upper'],
            ['name' => 'Forearms', 'body_region' => 'upper'],

            // Lower body
            ['name' => 'Quadriceps', 'body_region' => 'lower'],
            ['name' => 'Hamstrings', 'body_region' => 'lower'],
            ['name' => 'Glutes', 'body_region' => 'lower'],
            ['name' => 'Calves', 'body_region' => 'lower'],

            // Core
            ['name' => 'Abs', 'body_region' => 'core'],
            ['name' => 'Obliques', 'body_region' => 'core'],
        ];

        foreach ($muscleGroups as $group) {
            MuscleGroup::firstOrCreate(
                ['name' => $group['name']],
                $group
            );
        }

        $this->command->info('Muscle groups seeded successfully!');
    }
}
