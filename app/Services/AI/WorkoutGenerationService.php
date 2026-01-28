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
        private PromptBuilderService $promptBuilder,
        private ExerciseSelectorService $exerciseSelector,
        private ResponseParserService $responseParser
    ) {}

    /**
     * Generate a workout session using AI
     */
    public function generateSession(User $user, array $preferences = []): WorkoutSession
    {
        // Validate user profile
        $this->validateUserProfile($user);

        // Get available exercises
        $exercises = $this->exerciseSelector->getAvailableExercises([
            'focus_muscle_groups' => $preferences['focus_muscle_groups'] ?? null,
            'preferred_categories' => $preferences['preferred_categories'] ?? null,
            'limit' => 200,
        ]);

        if ($exercises->isEmpty()) {
            throw new \Exception('No exercises available for workout generation');
        }

        // Format exercises for AI
        $formattedExercises = $this->exerciseSelector->formatForAI($exercises);

        // Build prompt
        $prompt = $this->promptBuilder->buildSessionPrompt($user, $preferences, $formattedExercises);

        // Get AI service
        $aiService = WorkoutAIServiceFactory::make();

        // Generate workout
        try {
            $aiResponse = $aiService->generateSession($prompt);

            // Log the raw AI response for debugging
            Log::info('AI workout generation response', [
                'user_id' => $user->id,
                'raw_response' => $aiResponse,
            ]);

            // $aiResponse is already an array from OpenAIWorkoutService
            // We need to validate and filter the exercises
            $parsedResponse = $this->parseAndValidateResponse($aiResponse);

            // Log parsed response for debugging
            Log::info('Parsed AI response', [
                'user_id' => $user->id,
                'exercises_count' => count($parsedResponse['exercises'] ?? []),
                'exercise_ids' => array_column($parsedResponse['exercises'] ?? [], 'exercise_id'),
            ]);

            // Validate we have exercises
            if (empty($parsedResponse['exercises'])) {
                Log::warning('AI workout generation returned no exercises', [
                    'user_id' => $user->id,
                    'parsed_response' => $parsedResponse,
                ]);
                throw new \Exception('AI generated workout with no exercises. Please try again.');
            }

            // Create workout session
            return $this->createWorkoutSession($user, $parsedResponse);
        } catch (\Exception $e) {
            Log::error('AI workout generation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to generate workout session: '.$e->getMessage());
        }
    }

    /**
     * Build user context for AI
     */
    public function buildUserContext(User $user): array
    {
        return $this->promptBuilder->buildUserContext($user);
    }

    /**
     * Validate user has required profile data
     */
    public function validateUserProfile(User $user): void
    {
        $profile = $user->profile;

        if (! $profile) {
            throw new \Exception('User profile is required for AI workout generation');
        }

        if (! $profile->fitness_goal) {
            throw new \Exception('Fitness goal is required for AI workout generation');
        }

        if (! $profile->training_experience) {
            throw new \Exception('Training experience is required for AI workout generation');
        }
    }

    /**
     * Parse and validate AI response
     */
    private function parseAndValidateResponse(array $aiResponse): array
    {
        if (! isset($aiResponse['exercises']) || ! is_array($aiResponse['exercises'])) {
            throw new \Exception('AI response missing exercises array');
        }

        // Filter out exercises with invalid IDs
        $exerciseIds = array_column($aiResponse['exercises'], 'exercise_id');
        $validExerciseIds = \App\Models\Exercise::whereIn('id', $exerciseIds)->pluck('id')->toArray();

        // Filter exercises to only include valid ones
        $validExercises = array_filter($aiResponse['exercises'], function ($exercise) use ($validExerciseIds) {
            return isset($exercise['exercise_id']) && in_array($exercise['exercise_id'], $validExerciseIds);
        });

        // Re-index array
        $validExercises = array_values($validExercises);

        if (empty($validExercises)) {
            Log::warning('AI returned no valid exercises', [
                'requested_ids' => $exerciseIds,
                'valid_ids' => $validExerciseIds,
                'raw_exercises' => $aiResponse['exercises'],
            ]);
            throw new \Exception('AI returned no valid exercises. Please try again.');
        }

        // Log if some exercises were filtered out
        if (count($validExercises) < count($aiResponse['exercises'])) {
            $filteredIds = array_diff($exerciseIds, $validExerciseIds);
            Log::warning('Some AI exercise IDs were invalid and filtered out', [
                'filtered_ids' => $filteredIds,
                'kept_count' => count($validExercises),
                'total_count' => count($aiResponse['exercises']),
            ]);
        }

        return [
            'exercises' => $validExercises,
            'rationale' => $aiResponse['rationale'] ?? 'AI-generated workout session',
        ];
    }

    /**
     * Create workout session from AI response
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

            // Store rationale in notes (we can enhance this later with a dedicated field)
            if (! empty($parsedResponse['rationale'])) {
                $session->update([
                    'notes' => 'AI Generated: '.$parsedResponse['rationale'],
                ]);
            }

            return $session->fresh([
                'workoutSessionExercises.exercise.category',
                'setLogs',
            ]);
        });
    }
}
