<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EquipmentType;
use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['code' => 'BARBELL', 'name' => 'Barbell', 'display_order' => 10],
            ['code' => 'DUMBBELL', 'name' => 'Dumbbell', 'display_order' => 20],
            ['code' => 'CABLE', 'name' => 'Cable', 'display_order' => 30],
            ['code' => 'MACHINE', 'name' => 'Machine', 'display_order' => 40],
            ['code' => 'SMITH', 'name' => 'Smith Machine', 'display_order' => 50],
            ['code' => 'BODYWEIGHT', 'name' => 'Bodyweight', 'display_order' => 60],
            ['code' => 'BAND', 'name' => 'Band', 'display_order' => 70],
            ['code' => 'KETTLEBELL', 'name' => 'Kettlebell', 'display_order' => 80],
            ['code' => 'TRX', 'name' => 'TRX', 'display_order' => 90],
            ['code' => 'MEDICINE_BALL', 'name' => 'Medicine Ball', 'display_order' => 100],
        ];

        foreach ($types as $type) {
            EquipmentType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }

        $this->command->info('Equipment types seeded successfully!');
    }
}
