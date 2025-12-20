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
            UserSeeder::class,           // Create demo user first
            CategorySeeder::class,       // Create exercise categories
            ExerciseSeeder::class,       // Create global exercises
            WorkoutTemplateSeeder::class, // Create workout templates with exercises
            PartnerSeeder::class,        // Create partners with identities
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeded successfully!');
        $this->command->info('📧 User: atanasoski992@gmail.com');
        $this->command->info('🔑 Password: kiril123');
        $this->command->info('');
    }
}
