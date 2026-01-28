<?php

namespace App\Services\AI;

use App\Models\User;

class PromptBuilderService
{
    /**
     * Build AI prompt for session generation
     */
    public function buildSessionPrompt(User $user, array $preferences, array $exercises): string
    {
        $userContext = $this->buildUserContext($user);
        $exerciseDatabase = $this->formatExerciseDatabase($exercises);

        $prompt = "Generate a personalized workout session for this user:\n\n";
        $prompt .= "USER PROFILE:\n";
        $prompt .= json_encode($userContext, JSON_PRETTY_PRINT)."\n\n";

        if (! empty($preferences['focus_muscle_groups'])) {
            $prompt .= 'FOCUS MUSCLE GROUPS: '.implode(', ', $preferences['focus_muscle_groups'])."\n\n";
        }

        if (! empty($preferences['duration_minutes'])) {
            $prompt .= "TARGET DURATION: {$preferences['duration_minutes']} minutes\n\n";
        }

        if (! empty($preferences['preferred_categories'])) {
            $prompt .= 'PREFERRED EXERCISE CATEGORIES: '.implode(', ', $preferences['preferred_categories'])."\n\n";
        }

        if (! empty($preferences['difficulty'])) {
            $prompt .= "DIFFICULTY LEVEL: {$preferences['difficulty']}\n\n";
        }

        $prompt .= "AVAILABLE EXERCISES:\n";
        $prompt .= json_encode($exerciseDatabase, JSON_PRETTY_PRINT)."\n\n";

        $prompt .= "Generate a workout session that:\n";
        $prompt .= "1. Matches the user's fitness goal and experience level\n";
        $prompt .= "2. Focuses on the specified muscle groups (if provided)\n";
        $prompt .= "3. Uses exercises from preferred categories (if provided)\n";
        $prompt .= "4. Fits within the target duration (if provided)\n";
        $prompt .= "5. Uses only exercises from the available exercises list\n";
        $prompt .= "6. Applies progressive overload based on user's workout history\n";
        $prompt .= "7. Orders exercises logically (compound movements first)\n";

        return $prompt;
    }

    /**
     * Build user context from profile and history
     */
    public function buildUserContext(User $user): array
    {
        $profile = $user->profile;
        $context = [];

        if ($profile) {
            $context['fitness_goal'] = $profile->fitness_goal?->value;
            $context['training_experience'] = $profile->training_experience?->value;
            $context['age'] = $profile->age;
            $context['gender'] = $profile->gender?->value;
            $context['height'] = $profile->height;
            $context['weight'] = $profile->weight;
            $context['training_days_per_week'] = $profile->training_days_per_week;
            $context['workout_duration_minutes'] = $profile->workout_duration_minutes;
        }

        // Get recent workout history summary
        $recentSessions = $user->workoutSessions()
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->with('workoutSessionExercises.exercise')
            ->get();

        $context['recent_workouts'] = $recentSessions->map(function ($session) {
            return [
                'date' => $session->completed_at->toDateString(),
                'exercises' => $session->workoutSessionExercises->pluck('exercise.name')->toArray(),
            ];
        })->toArray();

        return $context;
    }

    /**
     * Format exercise database for AI context
     */
    public function formatExerciseDatabase(array $exercises): array
    {
        return array_map(function ($exercise) {
            return [
                'id' => $exercise['id'],
                'name' => $exercise['name'],
                'description' => $exercise['description'] ?? '',
                'category' => $exercise['category'] ?? '',
                'primary_muscle_groups' => $exercise['primary_muscle_groups'] ?? [],
                'secondary_muscle_groups' => $exercise['secondary_muscle_groups'] ?? [],
                'default_rest_sec' => $exercise['default_rest_sec'] ?? 90,
            ];
        }, $exercises);
    }
}
