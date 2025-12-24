<?php

namespace Database\Seeders;

use App\Enums\FitnessGoal;
use App\Enums\Gender;
use App\Enums\TrainingExperience;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👤 Creating user profiles...');

        // Get all users without profiles
        $users = User::whereDoesntHave('profile')->get();

        if ($users->isEmpty()) {
            $this->command->info('✅ All users already have profiles.');

            return;
        }

        foreach ($users as $user) {
            UserProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        }

        $this->command->info("✅ Created {$users->count()} user profile(s).");

        // Also ensure the demo user has a realistic profile
        $demoUser = User::where('email', 'atanasoski992@gmail.com')->first();
        if ($demoUser) {
            $profile = $demoUser->profile;
            if ($profile) {
                // Update with realistic data for fitness metrics
                $profile->update([
                    'fitness_goal' => FitnessGoal::MuscleGain,
                    'age' => 28,
                    'gender' => Gender::Male,
                    'height' => 180,
                    'weight' => 80.5,
                    'training_experience' => TrainingExperience::Intermediate,
                    'training_days_per_week' => 4,
                    'workout_duration_minutes' => 60,
                ]);
                $this->command->info('✅ Updated demo user profile with realistic data.');
            } else {
                UserProfile::factory()->create([
                    'user_id' => $demoUser->id,
                    'fitness_goal' => FitnessGoal::MuscleGain,
                    'age' => 28,
                    'gender' => Gender::Male,
                    'height' => 180,
                    'weight' => 80.5,
                    'training_experience' => TrainingExperience::Intermediate,
                    'training_days_per_week' => 4,
                    'workout_duration_minutes' => 60,
                ]);
                $this->command->info('✅ Created demo user profile with realistic data.');
            }
        }
    }
}
