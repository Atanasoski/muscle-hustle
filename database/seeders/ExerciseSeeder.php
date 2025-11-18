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
            ['name' => 'Barbell Bench Press', 'category' => 'chest', 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Bench Press', 'category' => 'chest', 'default_rest_sec' => 90],
            ['name' => 'Incline Barbell Bench Press', 'category' => 'chest', 'default_rest_sec' => 120],
            ['name' => 'Incline Dumbbell Press', 'category' => 'chest', 'default_rest_sec' => 90],
            ['name' => 'Decline Bench Press', 'category' => 'chest', 'default_rest_sec' => 90],
            ['name' => 'Dumbbell Flyes', 'category' => 'chest', 'default_rest_sec' => 60],
            ['name' => 'Cable Flyes', 'category' => 'chest', 'default_rest_sec' => 60],
            ['name' => 'Push-ups', 'category' => 'chest', 'default_rest_sec' => 60],
            ['name' => 'Dips (Chest)', 'category' => 'chest', 'default_rest_sec' => 90],
            
            // Back
            ['name' => 'Deadlift', 'category' => 'back', 'default_rest_sec' => 180],
            ['name' => 'Barbell Row', 'category' => 'back', 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Row', 'category' => 'back', 'default_rest_sec' => 90],
            ['name' => 'Pull-ups', 'category' => 'back', 'default_rest_sec' => 120],
            ['name' => 'Lat Pulldown', 'category' => 'back', 'default_rest_sec' => 90],
            ['name' => 'Seated Cable Row', 'category' => 'back', 'default_rest_sec' => 90],
            ['name' => 'T-Bar Row', 'category' => 'back', 'default_rest_sec' => 90],
            ['name' => 'Face Pulls', 'category' => 'back', 'default_rest_sec' => 60],
            ['name' => 'Hyperextensions', 'category' => 'back', 'default_rest_sec' => 60],
            
            // Legs
            ['name' => 'Barbell Squat', 'category' => 'legs', 'default_rest_sec' => 180],
            ['name' => 'Front Squat', 'category' => 'legs', 'default_rest_sec' => 150],
            ['name' => 'Leg Press', 'category' => 'legs', 'default_rest_sec' => 120],
            ['name' => 'Romanian Deadlift', 'category' => 'legs', 'default_rest_sec' => 120],
            ['name' => 'Leg Curl', 'category' => 'legs', 'default_rest_sec' => 60],
            ['name' => 'Leg Extension', 'category' => 'legs', 'default_rest_sec' => 60],
            ['name' => 'Walking Lunges', 'category' => 'legs', 'default_rest_sec' => 90],
            ['name' => 'Bulgarian Split Squat', 'category' => 'legs', 'default_rest_sec' => 90],
            ['name' => 'Calf Raises', 'category' => 'legs', 'default_rest_sec' => 60],
            
            // Shoulders
            ['name' => 'Overhead Press', 'category' => 'shoulders', 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Shoulder Press', 'category' => 'shoulders', 'default_rest_sec' => 90],
            ['name' => 'Lateral Raises', 'category' => 'shoulders', 'default_rest_sec' => 60],
            ['name' => 'Front Raises', 'category' => 'shoulders', 'default_rest_sec' => 60],
            ['name' => 'Rear Delt Flyes', 'category' => 'shoulders', 'default_rest_sec' => 60],
            ['name' => 'Arnold Press', 'category' => 'shoulders', 'default_rest_sec' => 90],
            ['name' => 'Upright Row', 'category' => 'shoulders', 'default_rest_sec' => 60],
            ['name' => 'Shrugs', 'category' => 'shoulders', 'default_rest_sec' => 60],
            
            // Arms
            ['name' => 'Barbell Curl', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Dumbbell Curl', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Hammer Curl', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Preacher Curl', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Cable Curl', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Close-Grip Bench Press', 'category' => 'arms', 'default_rest_sec' => 90],
            ['name' => 'Tricep Dips', 'category' => 'arms', 'default_rest_sec' => 90],
            ['name' => 'Overhead Tricep Extension', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Tricep Pushdown', 'category' => 'arms', 'default_rest_sec' => 60],
            ['name' => 'Skull Crushers', 'category' => 'arms', 'default_rest_sec' => 60],
            
            // Core
            ['name' => 'Plank', 'category' => 'core', 'default_rest_sec' => 60],
            ['name' => 'Crunches', 'category' => 'core', 'default_rest_sec' => 45],
            ['name' => 'Hanging Leg Raises', 'category' => 'core', 'default_rest_sec' => 60],
            ['name' => 'Russian Twists', 'category' => 'core', 'default_rest_sec' => 45],
            ['name' => 'Cable Crunches', 'category' => 'core', 'default_rest_sec' => 60],
            ['name' => 'Ab Wheel Rollout', 'category' => 'core', 'default_rest_sec' => 60],
            
            // Cardio
            ['name' => 'Treadmill Running', 'category' => 'cardio', 'default_rest_sec' => null],
            ['name' => 'Cycling', 'category' => 'cardio', 'default_rest_sec' => null],
            ['name' => 'Rowing Machine', 'category' => 'cardio', 'default_rest_sec' => null],
            ['name' => 'Jump Rope', 'category' => 'cardio', 'default_rest_sec' => 60],
            ['name' => 'Burpees', 'category' => 'cardio', 'default_rest_sec' => 60],
        ];

        foreach ($exercises as $exercise) {
            Exercise::firstOrCreate(
                [
                    'name' => $exercise['name'],
                    'user_id' => null,
                ],
                [
                    'category' => $exercise['category'],
                    'default_rest_sec' => $exercise['default_rest_sec'],
                ]
            );
        }

        $this->command->info('Global exercises seeded successfully!');
    }
}
