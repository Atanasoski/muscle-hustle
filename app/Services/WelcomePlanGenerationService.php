<?php

namespace App\Services;

use App\Enums\PlanType;
use App\Models\Plan;
use App\Models\User;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;
use App\Services\WorkoutGenerator\DeterministicWorkoutGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WelcomePlanGenerationService
{
    public function __construct(
        private DeterministicWorkoutGenerator $workoutGenerator
    ) {}

    /**
     * Generate a welcome plan for a user based on their profile
     */
    public function generateWelcomePlan(User $user, ?string $planName = null): Plan
    {
        // Validate user profile
        $this->validateUserProfile($user);

        // Check if onboarding already completed
        if ($user->onboarding_completed_at !== null) {
            throw new \Exception('Onboarding has already been completed for this user');
        }

        return DB::transaction(function () use ($user, $planName) {
            // Determine workout split based on training days
            $split = $this->determineSplit($user->profile->training_days_per_week);

            // Create the plan
            $plan = Plan::create([
                'user_id' => $user->id,
                'partner_id' => $user->partner_id,
                'name' => $planName ?? 'Your Personalized Plan',
                'description' => 'Auto-generated welcome plan based on your profile',
                'type' => PlanType::Custom,
                'is_active' => true,
            ]);

            Log::info('Welcome plan created', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'training_days' => $user->profile->training_days_per_week,
            ]);

            // Generate workout templates for each day
            $dayIndex = 0;
            foreach ($split as $targetRegions) {
                $this->createWorkoutTemplate($plan, $dayIndex, $targetRegions, $user);
                $dayIndex++;
            }

            // Mark onboarding as complete
            $user->update([
                'onboarding_completed_at' => now(),
            ]);

            Log::info('Welcome plan generation completed', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'templates_count' => $plan->workoutTemplates()->count(),
            ]);

            return $plan->load(['workoutTemplates.exercises']);
        });
    }

    /**
     * Determine workout split based on training days per week
     */
    private function determineSplit(int $daysPerWeek): array
    {
        return match ($daysPerWeek) {
            1 => [
                ['UPPER_PUSH', 'UPPER_PULL', 'LOWER'], // Full Body (Push focus)
            ],
            2 => [
                ['UPPER_PUSH', 'UPPER_PULL', 'LOWER'], // Full Body (Push focus)
                ['UPPER_PULL', 'UPPER_PUSH', 'LOWER'], // Full Body (Pull focus)
            ],
            3 => [
                ['UPPER_PUSH', 'UPPER_PULL', 'LOWER'], // Full Body (Push focus)
                ['UPPER_PULL', 'UPPER_PUSH', 'LOWER'], // Full Body (Pull focus)
                ['LOWER', 'UPPER_PUSH', 'UPPER_PULL'], // Full Body (Lower focus)
            ],
            4 => [
                ['UPPER_PUSH', 'ARMS'], // Push + Triceps
                ['LOWER', 'CORE'], // Legs + Core
                ['UPPER_PULL', 'ARMS'], // Pull + Biceps
                ['LOWER', 'CORE'], // Lower Body
            ],
            5 => [
                ['UPPER_PUSH', 'ARMS'], // Push + Triceps
                ['LOWER', 'CORE'], // Legs + Core
                ['UPPER_PULL', 'ARMS'], // Pull + Biceps
                ['LOWER', 'CORE'], // Lower Body
                ['UPPER_PUSH', 'UPPER_PULL'], // Upper Body
            ],
            6 => [
                ['UPPER_PUSH', 'ARMS'], // Push + Triceps
                ['UPPER_PULL', 'ARMS'], // Pull + Biceps
                ['LOWER', 'CORE'], // Legs + Core
                ['UPPER_PUSH', 'ARMS'], // Push + Triceps
                ['UPPER_PULL', 'ARMS'], // Pull + Biceps
                ['LOWER', 'CORE'], // Legs + Core
            ],
            7 => [
                ['UPPER_PUSH', 'ARMS'], // Push + Triceps
                ['UPPER_PULL', 'ARMS'], // Pull + Biceps
                ['LOWER', 'CORE'], // Legs + Core
                ['UPPER_PUSH', 'ARMS'], // Push + Triceps
                ['UPPER_PULL', 'ARMS'], // Pull + Biceps
                ['LOWER', 'CORE'], // Legs + Core
                ['UPPER_PUSH', 'UPPER_PULL', 'LOWER'], // Full Body (Push focus)
            ],
            default => [['UPPER_PUSH', 'UPPER_PULL', 'LOWER']], // Full Body
        };
    }

    /**
     * Create a workout template for a specific day
     */
    private function createWorkoutTemplate(Plan $plan, int $dayIndex, array $targetRegions, User $user): WorkoutTemplate
    {
        $workoutName = $this->getWorkoutName($targetRegions, $dayIndex);

        // Generate workout using existing generator
        $generatedWorkout = $this->workoutGenerator->generate($user, [
            'target_regions' => $targetRegions,
            'duration_minutes' => $user->profile->workout_duration_minutes,
        ]);

        // Create workout template
        $template = WorkoutTemplate::create([
            'plan_id' => $plan->id,
            'name' => $workoutName,
            'description' => $generatedWorkout['rationale'] ?? null,
            'day_of_week' => $dayIndex,
            'week_number' => 1,
            'order_index' => $dayIndex,
        ]);

        // Attach exercises to template
        $order = 1;
        foreach ($generatedWorkout['exercises'] as $exerciseData) {
            WorkoutTemplateExercise::create([
                'workout_template_id' => $template->id,
                'exercise_id' => $exerciseData['exercise_id'],
                'order' => $order++,
                'target_sets' => $exerciseData['target_sets'],
                'target_reps' => $exerciseData['target_reps'],
                'target_weight' => $exerciseData['target_weight'],
                'rest_seconds' => $exerciseData['rest_seconds'],
            ]);
        }

        Log::info('Workout template created', [
            'template_id' => $template->id,
            'plan_id' => $plan->id,
            'exercises_count' => count($generatedWorkout['exercises']),
        ]);

        return $template;
    }

    /**
     * Generate a workout name based on target regions
     */
    private function getWorkoutName(array $targetRegions, int $dayIndex): string
    {
        // Check for special multi-region combinations first
        $regionSet = array_unique($targetRegions);
        sort($regionSet);
        $regionKey = implode('|', $regionSet);

        // Check if it's a full body workout (all three main regions)
        $isFullBody = count($regionSet) === 3
            && in_array('UPPER_PUSH', $regionSet)
            && in_array('UPPER_PULL', $regionSet)
            && in_array('LOWER', $regionSet);

        if ($isFullBody) {
            // Determine focus based on first region in the array (order matters for exercise selection)
            $firstRegion = $targetRegions[0];
            $focus = match ($firstRegion) {
                'UPPER_PUSH' => 'Push',
                'UPPER_PULL' => 'Pull',
                'LOWER' => 'Legs',
                default => '',
            };

            return $focus ? "Full Body ({$focus})" : 'Full Body';
        }

        $specialNames = [
            'CORE|LOWER' => 'Legs Day',
            'UPPER_PULL|UPPER_PUSH' => 'Upper Body Day',
            'ARMS|UPPER_PUSH' => 'Push Day',
            'ARMS|UPPER_PULL' => 'Pull Day',
        ];

        if (isset($specialNames[$regionKey])) {
            return $specialNames[$regionKey];
        }

        // Single region or other combinations
        $regionNames = [
            'UPPER_PUSH' => 'Push',
            'UPPER_PULL' => 'Pull',
            'LOWER' => 'Legs',
            'ARMS' => 'Arms',
            'CORE' => 'Core',
        ];

        $names = [];
        foreach ($targetRegions as $region) {
            if (isset($regionNames[$region])) {
                $names[] = $regionNames[$region];
            }
        }

        if (empty($names)) {
            return 'Workout Day '.($dayIndex + 1);
        }

        if (count($names) === 1) {
            return $names[0].' Day';
        }

        return implode(' & ', $names).' Day';
    }

    /**
     * Validate user has required profile data
     */
    private function validateUserProfile(User $user): void
    {
        $profile = $user->profile;

        if (! $profile) {
            throw new \Exception('User profile is required for welcome plan generation');
        }

        if (! $profile->fitness_goal) {
            throw new \Exception('Fitness goal is required for welcome plan generation');
        }

        if (! $profile->training_experience) {
            throw new \Exception('Training experience is required for welcome plan generation');
        }

        if (! $profile->training_days_per_week) {
            throw new \Exception('Training days per week is required for welcome plan generation');
        }

        if (! $profile->workout_duration_minutes) {
            throw new \Exception('Workout duration is required for welcome plan generation');
        }
    }
}
