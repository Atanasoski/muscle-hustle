<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;
use Illuminate\Database\Seeder;

class WorkoutTemplateSeeder extends Seeder
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

        // Define workout templates structure
        $templates = [
            [
                'name' => 'Upper A - Push Focus',
                'description' => 'Upper body workout emphasizing pressing movements',
                'day_of_week' => 0, // Monday
                'exercises' => [
                    ['name' => 'Barbell Bench Press', 'sets' => 4, 'reps' => 8, 'weight' => 60.00, 'rest' => 1],
                    ['name' => 'Barbell Row', 'sets' => 4, 'reps' => 8, 'weight' => 50.00, 'rest' => 1],
                    ['name' => 'Dumbbell Shoulder Press', 'sets' => 3, 'reps' => 10, 'weight' => 20.00, 'rest' => 1],
                    ['name' => 'Pull-ups', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 1],
                    ['name' => 'Dumbbell Curl', 'sets' => 3, 'reps' => 12, 'weight' => 12.00, 'rest' => 1],
                    ['name' => 'Overhead Tricep Extension', 'sets' => 3, 'reps' => 12, 'weight' => 15.00, 'rest' => 1],
                ],
            ],
            [
                'name' => 'Lower A - Squat Focus',
                'description' => 'Lower body workout with squat emphasis',
                'day_of_week' => 1, // Tuesday
                'exercises' => [
                    ['name' => 'Barbell Squat', 'sets' => 4, 'reps' => 8, 'weight' => 80.00, 'rest' => 1],
                    ['name' => 'Romanian Deadlift', 'sets' => 3, 'reps' => 10, 'weight' => 60.00, 'rest' => 1],
                    ['name' => 'Leg Press', 'sets' => 3, 'reps' => 12, 'weight' => 100.00, 'rest' => 1],
                    ['name' => 'Leg Curl', 'sets' => 3, 'reps' => 12, 'weight' => 40.00, 'rest' => 1],
                    ['name' => 'Plank', 'sets' => 3, 'reps' => 45, 'weight' => null, 'rest' => 1],
                ],
            ],
            [
                'name' => 'Upper B - Pull Focus',
                'description' => 'Upper body workout emphasizing pulling movements',
                'day_of_week' => 2, // Wednesday
                'exercises' => [
                    ['name' => 'Overhead Press', 'sets' => 4, 'reps' => 8, 'weight' => 40.00, 'rest' => 1],
                    ['name' => 'Lat Pulldown', 'sets' => 4, 'reps' => 8, 'weight' => 50.00, 'rest' => 1],
                    ['name' => 'Dumbbell Bench Press', 'sets' => 3, 'reps' => 10, 'weight' => 25.00, 'rest' => 1],
                    ['name' => 'Seated Cable Row', 'sets' => 3, 'reps' => 10, 'weight' => 45.00, 'rest' => 1],
                    ['name' => 'Lateral Raises', 'sets' => 3, 'reps' => 15, 'weight' => 10.00, 'rest' => 1],
                    ['name' => 'Dumbbell Curl', 'sets' => 3, 'reps' => 12, 'weight' => 12.00, 'rest' => 1],
                ],
            ],
            [
                'name' => 'Lower B - Deadlift Focus',
                'description' => 'Lower body workout with deadlift emphasis',
                'day_of_week' => 3, // Thursday
                'exercises' => [
                    ['name' => 'Deadlift', 'sets' => 3, 'reps' => 6, 'weight' => 100.00, 'rest' => 1],
                    ['name' => 'Front Squat', 'sets' => 3, 'reps' => 10, 'weight' => 60.00, 'rest' => 1],
                    ['name' => 'Leg Curl', 'sets' => 3, 'reps' => 12, 'weight' => 40.00, 'rest' => 1],
                    ['name' => 'Leg Extension', 'sets' => 3, 'reps' => 12, 'weight' => 50.00, 'rest' => 1],
                    ['name' => 'Hanging Leg Raises', 'sets' => 3, 'reps' => 15, 'weight' => null, 'rest' => 1],
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            // Create or find template
            $template = WorkoutTemplate::firstOrCreate(
                [
                    'user_id' => $demoUser->id,
                    'name' => $templateData['name'],
                ],
                [
                    'description' => $templateData['description'],
                    'day_of_week' => $templateData['day_of_week'],
                ]
            );

            // Clear existing exercises for this template
            $template->workoutTemplateExercises()->delete();

            // Add exercises
            foreach ($templateData['exercises'] as $index => $exerciseData) {
                $exercise = Exercise::where('name', $exerciseData['name'])->first();

                if (! $exercise) {
                    $this->command->warn("Exercise '{$exerciseData['name']}' not found. Skipping.");

                    continue;
                }

                WorkoutTemplateExercise::create([
                    'workout_template_id' => $template->id,
                    'exercise_id' => $exercise->id,
                    'order' => $index,
                    'target_sets' => $exerciseData['sets'],
                    'target_reps' => $exerciseData['reps'],
                    'target_weight' => $exerciseData['weight'],
                    'rest_seconds' => $exerciseData['rest'],
                ]);
            }

            $this->command->info("Created template: {$template->name}");
        }

        $this->command->info('Workout templates seeded successfully!');
    }
}
