<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TrainingStyle;
use Illuminate\Database\Seeder;

class TrainingStyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $styles = [
            ['code' => 'BODYBUILDING', 'name' => 'Bodybuilding', 'display_order' => 10],
            ['code' => 'FUNCTIONAL', 'name' => 'Functional Training', 'display_order' => 20],
            ['code' => 'OLYMPIC', 'name' => 'Olympic Lifting', 'display_order' => 30],
            ['code' => 'CALISTHENICS', 'name' => 'Calisthenics', 'display_order' => 40],
        ];

        foreach ($styles as $style) {
            TrainingStyle::firstOrCreate(
                ['code' => $style['code']],
                $style
            );
        }

        $this->command->info('Training styles seeded successfully!');
    }
}
