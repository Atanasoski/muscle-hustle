<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Plan;
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

        // Get the active plan for this user
        $plan = Plan::where('user_id', $demoUser->id)
            ->where('is_active', true)
            ->first();

        if (! $plan) {
            $this->command->error('No active plan found. Run PlanSeeder first.');

            return;
        }

        // Define workout templates structure
        $templates = [
            [
                'name' => 'Day 1 - Monday',
                'description' => 'Push focus with quads',
                'day_of_week' => 0, // Monday
                'exercises' => [
                    ['name' => 'Incline Dumbbell Press', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 90],
                    ['name' => 'Chest-Supported Dumbbell Row', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 90],
                    ['name' => 'Barbell Squat', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 180],
                    ['name' => 'Incline Dumbbell Curls', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 60],
                ],
            ],
            [
                'name' => 'Day 2 - Tuesday',
                'description' => 'Shoulders, back, and hamstrings',
                'day_of_week' => 1, // Tuesday
                'exercises' => [
                    ['name' => 'Seated Dumbbell Shoulder Press', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 90],
                    ['name' => 'Lat Pulldown', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 90],
                    ['name' => 'Romanian Deadlift', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 120],
                    ['name' => 'Incline Dumbbell Press', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 90],
                ],
            ],
            [
                'name' => 'Day 3 - Wednesday',
                'description' => 'Chest, back, quads, and shoulders',
                'day_of_week' => 2, // Wednesday
                'exercises' => [
                    ['name' => 'Barbell Bench Press', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 120],
                    ['name' => 'Dumbbell Row', 'sets' => 3, 'reps' => 10, 'weight' => null, 'rest' => 90],
                    ['name' => 'Leg Extension', 'sets' => 3, 'reps' => 12, 'weight' => null, 'rest' => 60],
                    ['name' => 'Cable Lateral Raises', 'sets' => 3, 'reps' => 12, 'weight' => null, 'rest' => 60],
                ],
            ],
            [
                'name' => 'Day 4 - Thursday',
                'description' => 'Glutes, chest, rear delts, and biceps',
                'day_of_week' => 3, // Thursday
                'exercises' => [
                    ['name' => 'Barbell Hip Thrust', 'sets' => 3, 'reps' => 12, 'weight' => 60.00, 'rest' => 90],
                    ['name' => 'Cable Chest Flyes', 'sets' => 3, 'reps' => 10, 'weight' => 12.50, 'rest' => 60],
                    ['name' => 'Reverse Cable Flyes', 'sets' => 3, 'reps' => 12, 'weight' => 10.00, 'rest' => 60],
                    ['name' => 'Hammer Curl', 'sets' => 3, 'reps' => 12, 'weight' => 16.00, 'rest' => 60],
                ],
            ],
            [
                'name' => 'Day 5 - Friday',
                'description' => 'Quads, glutes, core, calves, and triceps',
                'day_of_week' => 4, // Friday
                'exercises' => [
                    ['name' => 'Walking Lunges', 'sets' => 3, 'reps' => 12, 'weight' => null, 'rest' => 90],
                    ['name' => 'Reverse Crunches', 'sets' => 3, 'reps' => 15, 'weight' => null, 'rest' => 45],
                    ['name' => 'Standing Calf Raises', 'sets' => 3, 'reps' => 15, 'weight' => null, 'rest' => 60],
                    ['name' => 'Tricep Pushdown', 'sets' => 3, 'reps' => 12, 'weight' => null, 'rest' => 60],
                ],
            ],
        ];

        foreach ($templates as $templateData) {
            // Create or find template
            $template = WorkoutTemplate::firstOrCreate(
                [
                    'plan_id' => $plan->id,
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
