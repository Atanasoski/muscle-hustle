<?php

namespace App\Services\AI;

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
     * Generate a workout session using deterministic rules
     */
    public function generateSession(User $user, array $preferences = []): WorkoutSession
    {
        // Validate user profile
        $this->validateUserProfile($user);

        try {
            // Generate workout using deterministic algorithm
            $generatedWorkout = $this->workoutGenerator->generate($user, $preferences);

            Log::info('Workout generated', [
                'user_id' => $user->id,
                'exercises_count' => count($generatedWorkout['exercises'] ?? []),
                'exercise_ids' => array_column($generatedWorkout['exercises'] ?? [], 'exercise_id'),
            ]);

            // Validate we have exercises
            if (empty($generatedWorkout['exercises'])) {
                throw new \Exception('Generated workout has no exercises. Please try again.');
            }

            // Create workout session
            return $this->createWorkoutSession($user, $generatedWorkout);
        } catch (\Exception $e) {
            Log::error('Workout generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to generate workout session: '.$e->getMessage());
        }
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
     * Create workout session from generated data
     */
    private function createWorkoutSession(User $user, array $parsedResponse): WorkoutSession
    {
        return DB::transaction(function () use ($user, $parsedResponse) {
            $session = WorkoutSession::create([
                'user_id' => $user->id,
                'workout_template_id' => null,
                'performed_at' => Carbon::now(),
                'is_ai_generated' => true,
                'ai_generated_at' => Carbon::now(),
            ]);

            // Create workout session exercises
            $now = now();
            $exercisesToInsert = [];

            Log::info('Creating workout session exercises', [
                'session_id' => $session->id,
                'exercises_count' => count($parsedResponse['exercises']),
                'exercise_data' => $parsedResponse['exercises'],
            ]);

            foreach ($parsedResponse['exercises'] as $exerciseData) {
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

            Log::info('Exercises to insert', [
                'session_id' => $session->id,
                'count' => count($exercisesToInsert),
                'data' => $exercisesToInsert,
            ]);

            if (! empty($exercisesToInsert)) {
                WorkoutSessionExercise::insert($exercisesToInsert);
                Log::info('Exercises inserted successfully', [
                    'session_id' => $session->id,
                    'count' => count($exercisesToInsert),
                ]);
            } else {
                Log::warning('No exercises to insert', [
                    'session_id' => $session->id,
                    'parsed_response' => $parsedResponse,
                ]);
            }

            // Store rationale in notes
            if (! empty($parsedResponse['rationale'])) {
                $session->update([
                    'notes' => $parsedResponse['rationale'],
                ]);
            }

            return $session->fresh([
                'workoutSessionExercises.exercise.category',
                'setLogs',
            ]);
        });
    }
}
