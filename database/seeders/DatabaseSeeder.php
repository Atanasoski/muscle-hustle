<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in the correct order due to foreign key dependencies
        $this->call([
            RoleSeeder::class,           // Create roles first
            UserSeeder::class,           // Create demo user first
            UserProfileSeeder::class,    // Create user profiles
            CategorySeeder::class,       // Create exercise categories
            MuscleGroupSeeder::class,    // Create muscle groups
            ExerciseSeeder::class,       // Create global exercises
            PlanSeeder::class,           // Create plans
            WorkoutTemplateSeeder::class, // Create workout templates with exercises
            WorkoutSessionSeeder::class,  // Create workout session test data
            WorkoutSessionDataSeeder::class, // Create workout sessions with set logs for fitness metrics
            PartnerSeeder::class,        // Create partners with identities
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeded successfully!');
        $this->command->info('📧 User: atanasoski992@gmail.com');
        $this->command->info('🔑 Password: password');
        $this->command->info('');
    }
}
