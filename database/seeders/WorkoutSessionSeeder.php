<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkoutSession;
use App\Models\WorkoutTemplate;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkoutSessionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏋️ Creating workout session test data...');

        $user = User::where('email', 'atanasoski992@gmail.com')->first();
        $templates = WorkoutTemplate::whereHas('plan', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        if ($templates->isEmpty()) {
            $this->command->warn('⚠️ No workout templates found for the demo user. Run WorkoutTemplateSeeder first.');

            return;
        }

        $this->createCurrentWeekSessions($user, $templates);
        $this->createPreviousWeekSessions($user, $templates);
        $this->createNextWeekSessions($user, $templates);
        $this->createRandomHistoricalSessions($user, $templates);

        $sessionCount = WorkoutSession::where('user_id', $user->id)->count();
        $this->command->info("✅ Created {$sessionCount} workout sessions for testing the calendar endpoint");
    }

    /**
     * Create sessions for the current week.
     */
    private function createCurrentWeekSessions(User $user, $templates): void
    {
        $startOfWeek = Carbon::now()->startOfWeek(); // Monday

        // Monday - Completed Upper Body
        WorkoutSession::create([
            'user_id' => $user->id,
            'workout_template_id' => $templates->random()->id,
            'performed_at' => $startOfWeek->copy()->setTime(8, 30),
            'completed_at' => $startOfWeek->copy()->setTime(9, 15),
            'notes' => 'Great morning workout! Felt strong today.',
        ]);

        // Wednesday - Incomplete session (started but not finished)
        WorkoutSession::create([
            'user_id' => $user->id,
            'workout_template_id' => $templates->random()->id,
            'performed_at' => $startOfWeek->copy()->addDays(2)->setTime(18, 0),
            'completed_at' => null,
            'notes' => 'Had to cut this short due to meeting.',
        ]);

        // Friday - Completed Leg Day
        WorkoutSession::create([
            'user_id' => $user->id,
            'workout_template_id' => $templates->random()->id,
            'performed_at' => $startOfWeek->copy()->addDays(4)->setTime(7, 0),
            'completed_at' => $startOfWeek->copy()->addDays(4)->setTime(8, 30),
            'notes' => 'Intense leg session. Squats felt heavy but manageable.',
        ]);
    }

    /**
     * Create sessions for the previous week.
     */
    private function createPreviousWeekSessions(User $user, $templates): void
    {
        $previousWeek = Carbon::now()->startOfWeek()->subWeek();

        // Create 4 sessions throughout the previous week
        $days = [0, 1, 3, 5]; // Mon, Tue, Thu, Sat

        foreach ($days as $day) {
            $performedAt = $previousWeek->copy()->addDays($day)->setTime(
                fake()->numberBetween(6, 20),
                fake()->randomElement([0, 15, 30, 45])
            );

            $duration = fake()->numberBetween(30, 90); // 30-90 minutes
            $completedAt = fake()->boolean(85) // 85% chance of completion
                ? $performedAt->copy()->addMinutes($duration)
                : null;

            WorkoutSession::create([
                'user_id' => $user->id,
                'workout_template_id' => $templates->random()->id,
                'performed_at' => $performedAt,
                'completed_at' => $completedAt,
                'notes' => $completedAt
                    ? fake()->randomElement([
                        'Solid workout session.',
                        'Pushed hard today!',
                        'Feeling stronger every day.',
                        'Good form throughout.',
                        null,
                    ])
                    : fake()->randomElement([
                        'Interrupted by phone call.',
                        'Ran out of time.',
                        'Will finish this tomorrow.',
                        null,
                    ]),
            ]);
        }
    }

    /**
     * Create sessions for next week.
     */
    private function createNextWeekSessions(User $user, $templates): void
    {
        $nextWeek = Carbon::now()->startOfWeek()->addWeek();

        // Create 2 planned/scheduled sessions for next week
        WorkoutSession::create([
            'user_id' => $user->id,
            'workout_template_id' => $templates->random()->id,
            'performed_at' => $nextWeek->copy()->setTime(9, 0), // Monday
            'completed_at' => $nextWeek->copy()->setTime(10, 15),
            'notes' => 'Early morning session planned.',
        ]);

        WorkoutSession::create([
            'user_id' => $user->id,
            'workout_template_id' => $templates->random()->id,
            'performed_at' => $nextWeek->copy()->addDays(3)->setTime(17, 30), // Thursday
            'completed_at' => null, // Not completed yet (future session)
            'notes' => null,
        ]);
    }

    /**
     * Create some historical sessions for variety.
     */
    private function createRandomHistoricalSessions(User $user, $templates): void
    {
        // Create 10 historical sessions over the past 2 months
        for ($i = 0; $i < 10; $i++) {
            $performedAt = Carbon::now()
                ->subDays(fake()->numberBetween(14, 60))
                ->setTime(
                    fake()->numberBetween(6, 21),
                    fake()->randomElement([0, 15, 30, 45])
                );

            $duration = fake()->numberBetween(25, 120);
            $completedAt = fake()->boolean(80) // 80% completion rate
                ? $performedAt->copy()->addMinutes($duration)
                : null;

            WorkoutSession::create([
                'user_id' => $user->id,
                'workout_template_id' => $templates->random()->id,
                'performed_at' => $performedAt,
                'completed_at' => $completedAt,
                'notes' => fake()->boolean(40) ? fake()->sentence() : null,
            ]);
        }
    }
}
