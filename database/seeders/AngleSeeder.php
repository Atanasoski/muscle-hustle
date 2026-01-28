<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Angle;
use Illuminate\Database\Seeder;

class AngleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $angles = [
            ['code' => 'FLAT', 'name' => 'Flat', 'display_order' => 10],
            ['code' => 'INCLINE', 'name' => 'Incline', 'display_order' => 20],
            ['code' => 'DECLINE', 'name' => 'Decline', 'display_order' => 30],
            ['code' => 'HORIZONTAL', 'name' => 'Horizontal', 'display_order' => 40],
            ['code' => 'VERTICAL', 'name' => 'Vertical', 'display_order' => 50],
            ['code' => 'LOW_TO_HIGH', 'name' => 'Low to High', 'display_order' => 60],
            ['code' => 'HIGH_TO_LOW', 'name' => 'High to Low', 'display_order' => 70],
        ];

        foreach ($angles as $angle) {
            Angle::firstOrCreate(
                ['code' => $angle['code']],
                $angle
            );
        }

        $this->command->info('Angles seeded successfully!');
    }
}
