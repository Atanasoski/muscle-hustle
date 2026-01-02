<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUser = User::where('email', 'atanasoski992@gmail.com')->first();

        if (! $demoUser) {
            $this->command->error('User not found. Run UserSeeder first.');

            return;
        }

        // Create a default active plan
        $activePlan = Plan::firstOrCreate(
            [
                'user_id' => $demoUser->id,
                'name' => 'My Training Plan',
            ],
            [
                'description' => 'Main workout plan for strength and muscle building',
                'is_active' => true,
            ]
        );

        $this->command->info("Created plan: {$activePlan->name} (Active)");

        // Create an inactive plan for demonstration
        $inactivePlan = Plan::firstOrCreate(
            [
                'user_id' => $demoUser->id,
                'name' => 'Cutting Plan',
            ],
            [
                'description' => 'Plan for cutting phase',
                'is_active' => false,
            ]
        );

        $this->command->info("Created plan: {$inactivePlan->name} (Inactive)");

        $this->command->info('Plans seeded successfully!');
    }
}
