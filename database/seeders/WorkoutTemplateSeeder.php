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

        // Define workout templates structure (day_of_week: 0=Monday, 1=Tuesday, ..., 6=Sunday)
        $templates = [
            [
                'name' => 'Day 1 – Push Focus',
                'description' => 'Push focus with leg press, chest press, shoulder press, and triceps',
                'day_of_week' => 0, // Monday
                'exercises' => [
                    ['name' => 'Leg Press',  'weight' => null], // 3×10–12
                    ['name' => 'Machine Chest Press',  'weight' => null], // 3×8–12
                    ['name' => 'Machine Shoulder Press',  'weight' => null], // 3×8–12
                    ['name' => 'Triceps Pushdown (Cable)',  'weight' => null], // 3×12
                ],
            ],
            [
                'name' => 'Day 2 – Pull Focus',
                'description' => 'Pull focus with lower back, lat pulldown, seated row, and biceps',
                'day_of_week' => 1, // Tuesday
                'exercises' => [
                    ['name' => 'Hyperextensions',  'weight' => null], // 3×10–12 (Lower Back Extension Machine)
                    ['name' => 'Lat Pulldown',  'weight' => null], // 3×8–12
                    ['name' => 'Seated Cable Row',  'weight' => null], // 3×8–12
                    ['name' => 'Cable Curl',  'weight' => null], // 3×12 (Biceps Curl Machine / Cable Curl)
                ],
            ],
            [
                'name' => 'Day 3 – Legs + Core',
                'description' => 'Legs and core focus with leg press, leg curl, and ab crunch',
                'day_of_week' => 2, // Wednesday
                'exercises' => [
                    ['name' => 'Leg Press',  'weight' => null], // 3×10 (Narrow or Medium Stance, using Leg Press instead of Single-Leg Press)
                    ['name' => 'Seated Leg Curl',  'weight' => null], // 3×12 (Leg Curl Seated or Lying)
                    ['name' => 'Ab Crunch Machine',  'weight' => null], // 3×12–15
                    ['name' => 'Cable Woodchoppers',  'weight' => null], // 3×12
                ],
            ],
            [
                'name' => 'Day 4 – Upper Strength',
                'description' => 'Upper body strength with incline chest press, close-grip lat pulldown, lateral raises, and face pulls',
                'day_of_week' => 3, // Thursday
                'exercises' => [
                    ['name' => 'Incline Chest Press Machine',  'weight' => null], // 3×8–10
                    ['name' => 'Close-Grip Lat Pulldown',  'weight' => null], // 3×8–12
                    ['name' => 'Dumbbell Lateral Raises',  'weight' => null], // 3×12–15
                    ['name' => 'Face Pulls',  'weight' => null], // 3×15
                ],
            ],
            [
                'name' => 'Day 5 – Conditioning + Balance',
                'description' => 'Conditioning and balance with hack squat, hip thrust, chest press AMRAP, and cable chest fly',
                'day_of_week' => 4, // Friday
                'exercises' => [
                    ['name' => 'Hack Squat Machine',  'weight' => null], // 3×10–12
                    ['name' => 'Hip Thrust Machine',  'weight' => null], // 3×12
                    ['name' => 'Machine Chest Press',  'weight' => null], // 3×AMRAP (target_reps = null for AMRAP)
                    ['name' => 'Single-Arm Cable Chest Fly',  'weight' => null], // 3×12–15
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
                    // 'target_sets' => $exerciseData['sets'],
                    // 'target_reps' => $exerciseData['reps'],
                    // 'target_weight' => $exerciseData['weight'],
                    // 'rest_seconds' => $exerciseData['rest'],
                ]);
            }

            $this->command->info("Created template: {$template->name}");
        }

        $this->command->info('Workout templates seeded successfully!');
    }
}
