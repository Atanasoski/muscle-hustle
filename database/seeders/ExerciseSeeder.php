<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exercise;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exercises = [
            // Chest
            ['name' => 'Barbell Bench Press', 'category' => 'Chest', 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Bench Press', 'category' => 'Chest', 'default_rest_sec' => 90],
            ['name' => 'Incline Barbell Bench Press', 'category' => 'Chest', 'default_rest_sec' => 120],
            ['name' => 'Incline Dumbbell Press', 'category' => 'Chest', 'default_rest_sec' => 90],
            ['name' => 'Decline Bench Press', 'category' => 'Chest', 'default_rest_sec' => 90],
            ['name' => 'Dumbbell Flyes', 'category' => 'Chest', 'default_rest_sec' => 60],
            ['name' => 'Cable Flyes', 'category' => 'Chest', 'default_rest_sec' => 60],
            ['name' => 'Push-ups', 'category' => 'Chest', 'default_rest_sec' => 60],
            ['name' => 'Dips (Chest)', 'category' => 'Chest', 'default_rest_sec' => 90],
            
            // Back
            ['name' => 'Deadlift', 'category' => 'Back', 'default_rest_sec' => 180],
            ['name' => 'Barbell Row', 'category' => 'Back', 'default_rest_sec' => 90],
            ['name' => 'Dumbbell Row', 'category' => 'Back', 'default_rest_sec' => 90],
            ['name' => 'Pull-ups', 'category' => 'Back', 'default_rest_sec' => 120],
            ['name' => 'Lat Pulldown', 'category' => 'Back', 'default_rest_sec' => 90],
            ['name' => 'Seated Cable Row', 'category' => 'Back', 'default_rest_sec' => 90],
            ['name' => 'T-Bar Row', 'category' => 'Back', 'default_rest_sec' => 90],
            ['name' => 'Face Pulls', 'category' => 'Back', 'default_rest_sec' => 60],
            ['name' => 'Hyperextensions', 'category' => 'Back', 'default_rest_sec' => 60],
            
            // Legs
            ['name' => 'Barbell Squat', 'category' => 'Legs', 'default_rest_sec' => 180],
            ['name' => 'Front Squat', 'category' => 'Legs', 'default_rest_sec' => 150],
            ['name' => 'Leg Press', 'category' => 'Legs', 'default_rest_sec' => 120],
            ['name' => 'Romanian Deadlift', 'category' => 'Legs', 'default_rest_sec' => 120],
            ['name' => 'Leg Curl', 'category' => 'Legs', 'default_rest_sec' => 60],
            ['name' => 'Leg Extension', 'category' => 'Legs', 'default_rest_sec' => 60],
            ['name' => 'Walking Lunges', 'category' => 'Legs', 'default_rest_sec' => 90],
            ['name' => 'Bulgarian Split Squat', 'category' => 'Legs', 'default_rest_sec' => 90],
            ['name' => 'Calf Raises', 'category' => 'Legs', 'default_rest_sec' => 60],
            
            // Shoulders
            ['name' => 'Overhead Press', 'category' => 'Shoulders', 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Shoulder Press', 'category' => 'Shoulders', 'default_rest_sec' => 90],
            ['name' => 'Lateral Raises', 'category' => 'Shoulders', 'default_rest_sec' => 60],
            ['name' => 'Front Raises', 'category' => 'Shoulders', 'default_rest_sec' => 60],
            ['name' => 'Rear Delt Flyes', 'category' => 'Shoulders', 'default_rest_sec' => 60],
            ['name' => 'Arnold Press', 'category' => 'Shoulders', 'default_rest_sec' => 90],
            ['name' => 'Upright Row', 'category' => 'Shoulders', 'default_rest_sec' => 60],
            ['name' => 'Shrugs', 'category' => 'Shoulders', 'default_rest_sec' => 60],
            
            // Arms
            ['name' => 'Barbell Curl', 'category' => 'Biceps', 'default_rest_sec' => 60],
            ['name' => 'Dumbbell Curl', 'category' => 'Biceps', 'default_rest_sec' => 60],
            ['name' => 'Hammer Curl', 'category' => 'Biceps', 'default_rest_sec' => 60],
            ['name' => 'Preacher Curl', 'category' => 'Biceps', 'default_rest_sec' => 60],
            ['name' => 'Cable Curl', 'category' => 'Biceps', 'default_rest_sec' => 60],
            ['name' => 'Close-Grip Bench Press', 'category' => 'Triceps', 'default_rest_sec' => 90],
            ['name' => 'Tricep Dips', 'category' => 'Triceps', 'default_rest_sec' => 90],
            ['name' => 'Overhead Tricep Extension', 'category' => 'Triceps', 'default_rest_sec' => 60],
            ['name' => 'Tricep Pushdown', 'category' => 'Triceps', 'default_rest_sec' => 60],
            ['name' => 'Skull Crushers', 'category' => 'Triceps', 'default_rest_sec' => 60],
            
            // Core
            ['name' => 'Plank', 'category' => 'Core', 'default_rest_sec' => 60],
            ['name' => 'Crunches', 'category' => 'Core', 'default_rest_sec' => 45],
            ['name' => 'Hanging Leg Raises', 'category' => 'Core', 'default_rest_sec' => 60],
            ['name' => 'Russian Twists', 'category' => 'Core', 'default_rest_sec' => 45],
            ['name' => 'Cable Crunches', 'category' => 'Core', 'default_rest_sec' => 60],
            ['name' => 'Ab Wheel Rollout', 'category' => 'Core', 'default_rest_sec' => 60],
            
            // Cardio
            ['name' => 'Treadmill Running', 'category' => 'Cardio', 'default_rest_sec' => null],
            ['name' => 'Cycling', 'category' => 'Cardio', 'default_rest_sec' => null],
            ['name' => 'Rowing Machine', 'category' => 'Cardio', 'default_rest_sec' => null],
            ['name' => 'Jump Rope', 'category' => 'Cardio', 'default_rest_sec' => 60],
            ['name' => 'Burpees', 'category' => 'Cardio', 'default_rest_sec' => 60],
        ];

        foreach ($exercises as $exercise) {
            Exercise::create([
                'user_id' => null, // Global exercises
                'name' => $exercise['name'],
                'category' => $exercise['category'],
                'default_rest_sec' => $exercise['default_rest_sec'],
            ]);
        }

        $this->command->info('Global exercises seeded successfully!');
    }
}
