<?php

namespace App\Services\WorkoutGenerator;

use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutSession;
use App\Models\WorkoutSessionExercise;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkoutGenerationService
{
    public function __construct(
        private DeterministicWorkoutGenerator $workoutGenerator
    ) {}

    /**
     * Generate a workout preview without creating a session
     * Returns exercise data and rationale for user review
     */
    public function preview(User $user, array $preferences = []): array
    {
        // Validate user profile
        $this->validateUserProfile($user);

        try {
            // Generate workout using deterministic algorithm
            $generatedWorkout = $this->workoutGenerator->generate($user, $preferences);

            Log::info('Workout preview generated', [
                'user_id' => $user->id,
                'exercises_count' => count($generatedWorkout['exercises'] ?? []),
                'exercise_ids' => array_column($generatedWorkout['exercises'] ?? [], 'exercise_id'),
            ]);

            // Validate we have exercises
            if (empty($generatedWorkout['exercises'])) {
                throw new \Exception('No exercises could be selected for this workout. Please try different criteria.');
            }

            // Load full exercise data for preview display
            $exerciseIds = array_column($generatedWorkout['exercises'], 'exercise_id');
            $exercisesById = Exercise::with(['category', 'muscleGroups', 'movementPattern', 'targetRegion', 'equipmentType', 'angle'])
                ->whereIn('id', $exerciseIds)
                ->get()
                ->keyBy('id');

            // Enrich exercises with full exercise data
            $enrichedExercises = array_map(function ($exerciseData) use ($exercisesById) {
                $exercise = $exercisesById->get($exerciseData['exercise_id']);

                return [
                    'exercise_id' => $exerciseData['exercise_id'],
                    'exercise' => $exercise,
                    'order' => $exerciseData['order'],
                    'target_sets' => $exerciseData['target_sets'],
                    'target_reps' => $exerciseData['target_reps'],
                    'target_weight' => $exerciseData['target_weight'],
                    'rest_seconds' => $exerciseData['rest_seconds'],
                ];
            }, $generatedWorkout['exercises']);

            // Calculate estimated duration
            $estimatedDuration = $this->calculateEstimatedDuration($generatedWorkout['exercises']);

            return [
                'exercises' => $enrichedExercises,
                'rationale' => $generatedWorkout['rationale'],
                'estimated_duration_minutes' => $estimatedDuration,
            ];
        } catch (\Exception $e) {
            Log::error('Workout preview generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Create a workout session from preview data
     */
    public function createFromPreview(User $user, array $exercises, ?string $rationale = null): WorkoutSession
    {
        // Validate exercise IDs exist
        $exerciseIds = array_column($exercises, 'exercise_id');
        $validIds = Exercise::whereIn('id', $exerciseIds)->pluck('id')->toArray();

        $invalidIds = array_diff($exerciseIds, $validIds);
        if (! empty($invalidIds)) {
            throw new \Exception('Invalid exercise IDs: '.implode(', ', $invalidIds));
        }

        return DB::transaction(function () use ($user, $exercises, $rationale) {
            $session = WorkoutSession::create([
                'user_id' => $user->id,
                'workout_template_id' => null,
                'performed_at' => Carbon::now(),
                'is_auto_generated' => true,
                'notes' => $rationale,
            ]);

            // Create workout session exercises
            $now = now();
            $exercisesToInsert = [];

            foreach ($exercises as $exerciseData) {
                $exercisesToInsert[] = [
                    'workout_session_id' => $session->id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'order' => $exerciseData['order'] ?? 0,
                    'target_sets' => $exerciseData['target_sets'] ?? 3,
                    'target_reps' => $exerciseData['target_reps'] ?? 10,
                    'target_weight' => $exerciseData['target_weight'] ?? 0,
                    'rest_seconds' => $exerciseData['rest_seconds'] ?? 90,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($exercisesToInsert)) {
                WorkoutSessionExercise::insert($exercisesToInsert);
            }

            Log::info('Workout session created from preview', [
                'user_id' => $user->id,
                'session_id' => $session->id,
                'exercises_count' => count($exercisesToInsert),
            ]);

            return $session->fresh([
                'workoutSessionExercises.exercise.category',
                'setLogs',
            ]);
        });
    }

    /**
     * Validate user has required profile data
     */
    public function validateUserProfile(User $user): void
    {
        $profile = $user->profile;

        if (! $profile) {
            throw new \Exception('User profile is required for workout generation');
        }

        if (! $profile->fitness_goal) {
            throw new \Exception('Fitness goal is required for workout generation');
        }

        if (! $profile->training_experience) {
            throw new \Exception('Training experience is required for workout generation');
        }
    }

    /**
     * Calculate estimated duration based on exercises
     */
    private function calculateEstimatedDuration(array $exercises): int
    {
        $setDuration = config('workout_generator.set_duration_seconds', 45);
        $totalSeconds = 0;

        foreach ($exercises as $exercise) {
            $sets = $exercise['target_sets'] ?? 3;
            $restSeconds = $exercise['rest_seconds'] ?? 90;

            // Time = sets × set_duration + (sets - 1) × rest
            $totalSeconds += ($sets * $setDuration) + (($sets - 1) * $restSeconds);
        }

        return (int) ceil($totalSeconds / 60);
    }
}
