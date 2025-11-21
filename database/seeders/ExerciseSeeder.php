<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories indexed by slug for easy lookup
        $categories = Category::pluck('id', 'slug');

        $exercises = [
            // Chest
            ['name' => 'Barbell Bench Press', 'category_id' => $categories['chest'], 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Bench Press', 'category_id' => $categories['chest'], 'default_rest_sec' => 90],
            ['name' => 'Incline Barbell Bench Press', 'category_id' => $categories['chest'], 'default_rest_sec' => 120],
            ['name' => 'Incline Dumbbell Press', 'category_id' => $categories['chest'], 'default_rest_sec' => 90],
            ['name' => 'Decline Bench Press', 'category_id' => $categories['chest'], 'default_rest_sec' => 90],
            ['name' => 'Dumbbell Flyes', 'category_id' => $categories['chest'], 'default_rest_sec' => 60],
            ['name' => 'Cable Flyes', 'category_id' => $categories['chest'], 'default_rest_sec' => 60],
            ['name' => 'Push-ups', 'category_id' => $categories['chest'], 'default_rest_sec' => 60],
            ['name' => 'Dips (Chest)', 'category_id' => $categories['chest'], 'default_rest_sec' => 90],

            // Back
            ['name' => 'Deadlift', 'category_id' => $categories['back'], 'default_rest_sec' => 180],
            ['name' => 'Barbell Row', 'category_id' => $categories['back'], 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Row', 'category_id' => $categories['back'], 'default_rest_sec' => 90],
            ['name' => 'Pull-ups', 'category_id' => $categories['back'], 'default_rest_sec' => 120],
            ['name' => 'Lat Pulldown', 'category_id' => $categories['back'], 'default_rest_sec' => 90],
            ['name' => 'Seated Cable Row', 'category_id' => $categories['back'], 'default_rest_sec' => 90],
            ['name' => 'T-Bar Row', 'category_id' => $categories['back'], 'default_rest_sec' => 90],
            ['name' => 'Face Pulls', 'category_id' => $categories['back'], 'default_rest_sec' => 60],
            ['name' => 'Hyperextensions', 'category_id' => $categories['back'], 'default_rest_sec' => 60],

            // Legs
            ['name' => 'Barbell Squat', 'category_id' => $categories['legs'], 'default_rest_sec' => 180],
            ['name' => 'Front Squat', 'category_id' => $categories['legs'], 'default_rest_sec' => 150],
            ['name' => 'Leg Press', 'category_id' => $categories['legs'], 'default_rest_sec' => 120],
            ['name' => 'Romanian Deadlift', 'category_id' => $categories['legs'], 'default_rest_sec' => 120],
            ['name' => 'Leg Curl', 'category_id' => $categories['legs'], 'default_rest_sec' => 60],
            ['name' => 'Leg Extension', 'category_id' => $categories['legs'], 'default_rest_sec' => 60],
            ['name' => 'Walking Lunges', 'category_id' => $categories['legs'], 'default_rest_sec' => 90],
            ['name' => 'Bulgarian Split Squat', 'category_id' => $categories['legs'], 'default_rest_sec' => 90],
            ['name' => 'Calf Raises', 'category_id' => $categories['legs'], 'default_rest_sec' => 60],

            // Shoulders
            ['name' => 'Overhead Press', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 120],
            ['name' => 'Dumbbell Shoulder Press', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 90],
            ['name' => 'Lateral Raises', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 60],
            ['name' => 'Front Raises', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 60],
            ['name' => 'Rear Delt Flyes', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 60],
            ['name' => 'Arnold Press', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 90],
            ['name' => 'Upright Row', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 60],
            ['name' => 'Shrugs', 'category_id' => $categories['shoulders'], 'default_rest_sec' => 60],

            // Arms
            ['name' => 'Barbell Curl', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Dumbbell Curl', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Hammer Curl', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Preacher Curl', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Cable Curl', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Close-Grip Bench Press', 'category_id' => $categories['arms'], 'default_rest_sec' => 90],
            ['name' => 'Tricep Dips', 'category_id' => $categories['arms'], 'default_rest_sec' => 90],
            ['name' => 'Overhead Tricep Extension', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Tricep Pushdown', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],
            ['name' => 'Skull Crushers', 'category_id' => $categories['arms'], 'default_rest_sec' => 60],

            // Core
            ['name' => 'Plank', 'category_id' => $categories['core'], 'default_rest_sec' => 60],
            ['name' => 'Crunches', 'category_id' => $categories['core'], 'default_rest_sec' => 45],
            ['name' => 'Hanging Leg Raises', 'category_id' => $categories['core'], 'default_rest_sec' => 60],
            ['name' => 'Russian Twists', 'category_id' => $categories['core'], 'default_rest_sec' => 45],
            ['name' => 'Cable Crunches', 'category_id' => $categories['core'], 'default_rest_sec' => 60],
            ['name' => 'Ab Wheel Rollout', 'category_id' => $categories['core'], 'default_rest_sec' => 60],

            // Cardio
            ['name' => 'Treadmill Running', 'category_id' => $categories['cardio'], 'default_rest_sec' => null],
            ['name' => 'Cycling', 'category_id' => $categories['cardio'], 'default_rest_sec' => null],
            ['name' => 'Rowing Machine', 'category_id' => $categories['cardio'], 'default_rest_sec' => null],
            ['name' => 'Jump Rope', 'category_id' => $categories['cardio'], 'default_rest_sec' => 60],
            ['name' => 'Burpees', 'category_id' => $categories['cardio'], 'default_rest_sec' => 60],
        ];

        foreach ($exercises as $exercise) {
            Exercise::firstOrCreate(
                [
                    'name' => $exercise['name'],
                    'user_id' => null,
                ],
                [
                    'category_id' => $exercise['category_id'],
                    'default_rest_sec' => $exercise['default_rest_sec'],
                ]
            );
        }

        $this->command->info('Global exercises seeded successfully!');
    }
}
