<?php

namespace App\Services\WorkoutGenerator;

use App\Enums\FitnessGoal;
use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DeterministicWorkoutGenerator
{
    public function __construct(
        private ExerciseSelectorService $exerciseSelector,
        private ProgressionCalculatorService $progressionCalculator,
        private ClassificationInferenceService $inferenceService
    ) {}

    /**
     * Generate a workout based on user preferences using deterministic rules
     */
    public function generate(User $user, array $preferences = []): array
    {
        // Normalize preferences (infer target_regions, equipment_types, etc.)
        $normalizedPreferences = $this->inferenceService->normalizePreferences($user, $preferences);

        // Get available exercises matching filters
        $exercises = $this->exerciseSelector->getAvailableExercises([
            'focus_muscle_groups' => $normalizedPreferences['focus_muscle_groups'] ?? null,
            'target_regions' => $normalizedPreferences['target_regions'] ?? null,
            'equipment_types' => $normalizedPreferences['equipment_types'] ?? null,
            'movement_patterns' => $normalizedPreferences['movement_patterns'] ?? null,
            'angles' => $normalizedPreferences['angles'] ?? null,
            'limit' => 200,
        ], $user->partner);

        if ($exercises->isEmpty()) {
            throw new \Exception('No exercises available matching the specified criteria');
        }

        // Select diverse exercises respecting duration constraint
        $selectedExercises = $this->selectDiverseExercises($exercises, $normalizedPreferences, $user);

        if ($selectedExercises->isEmpty()) {
            throw new \Exception('Could not select any exercises for the workout');
        }

        // Order exercises: compound first, isolation last
        $orderedExercises = $this->orderByCompoundFirst($selectedExercises);

        // Apply progression targets for each exercise
        $exercisesWithTargets = $this->applyTargets($orderedExercises, $user, $normalizedPreferences);

        Log::info('Deterministic workout generated', [
            'user_id' => $user->id,
            'exercises_count' => count($exercisesWithTargets),
            'exercise_ids' => array_column($exercisesWithTargets, 'exercise_id'),
        ]);

        return [
            'exercises' => $exercisesWithTargets,
            'rationale' => $this->buildRationale($orderedExercises, $normalizedPreferences),
        ];
    }

    /**
     * Select diverse exercises ensuring no duplicate movement_pattern + angle combinations
     */
    private function selectDiverseExercises(EloquentCollection $exercises, array $preferences, User $user): Collection
    {
        $selected = collect();
        $seen = []; // Track movement_pattern|angle combinations
        $durationMinutes = $preferences['duration_minutes'] ?? 60;
        $timeRemainingSeconds = $durationMinutes * 60;

        $maxTotal = config('workout_generator.max_total_exercises', 10);
        $maxPerRegion = config('workout_generator.max_exercises_per_region', 4);

        // Group exercises by target region
        $byRegion = $exercises->groupBy(fn ($e) => $e->targetRegion?->code ?? 'UNKNOWN');

        // Track exercises selected per region
        $countByRegion = [];

        // Sort regions by the order in preferences (if provided)
        $preferredRegions = $preferences['target_regions'] ?? array_keys($byRegion->toArray());

        foreach ($preferredRegions as $region) {
            if (! $byRegion->has($region)) {
                continue;
            }

            $regionExercises = $byRegion->get($region);
            $countByRegion[$region] = 0;

            // Shuffle first for variety on regenerate, then sort by compound-first priority
            $shuffled = $regionExercises->shuffle();
            $sorted = $this->sortByCompoundPriority($shuffled);

            foreach ($sorted as $exercise) {
                // Check total limit
                if ($selected->count() >= $maxTotal) {
                    break 2;
                }

                // Check region limit
                if ($countByRegion[$region] >= $maxPerRegion) {
                    break;
                }

                // Create diversity key
                $movementPattern = $exercise->movementPattern?->code ?? 'UNKNOWN';
                $angle = $exercise->angle?->code ?? 'NO_ANGLE';
                $key = "{$movementPattern}|{$angle}";

                // Skip if we already have this pattern+angle combination
                if (isset($seen[$key])) {
                    continue;
                }

                // Estimate time for this exercise
                $exerciseTime = $this->estimateExerciseTime($exercise, $user, $preferences);

                // Skip if not enough time remaining
                if ($timeRemainingSeconds < $exerciseTime) {
                    continue;
                }

                $selected->push($exercise);
                $seen[$key] = true;
                $countByRegion[$region]++;
                $timeRemainingSeconds -= $exerciseTime;
            }
        }

        return $selected;
    }

    /**
     * Sort exercises by compound-first priority while preserving shuffle order within priority groups
     */
    private function sortByCompoundPriority(Collection $exercises): Collection
    {
        $compoundPatterns = config('workout_generator.compound_patterns', []);

        // Use stable sort to preserve shuffle order within same priority
        return $exercises->sortBy(function ($exercise) use ($compoundPatterns) {
            $pattern = $exercise->movementPattern?->code;

            // Compound movements get priority 0, isolation gets 1
            return in_array($pattern, $compoundPatterns) ? 0 : 1;
        })->values();
    }

    /**
     * Order all selected exercises: compound first, isolation last
     */
    private function orderByCompoundFirst(Collection $exercises): Collection
    {
        $compoundPatterns = config('workout_generator.compound_patterns', []);

        return $exercises->sortBy(function ($exercise) use ($compoundPatterns) {
            $pattern = $exercise->movementPattern?->code;

            return in_array($pattern, $compoundPatterns) ? 0 : 1;
        })->values();
    }

    /**
     * Estimate time in seconds for an exercise
     */
    private function estimateExerciseTime(Exercise $exercise, User $user, array $preferences): int
    {
        $setDuration = config('workout_generator.set_duration_seconds', 45);
        $fitnessGoal = $user->profile?->fitness_goal ?? FitnessGoal::GeneralFitness;
        $defaults = $this->getGoalDefaults($fitnessGoal);

        $sets = $defaults['sets'];
        $restSeconds = $defaults['rest_seconds'];

        // Total time = sets × (set duration + rest)
        // Don't count rest after the last set
        return ($sets * $setDuration) + (($sets - 1) * $restSeconds);
    }

    /**
     * Apply progression targets to selected exercises
     */
    private function applyTargets(Collection $exercises, User $user, array $preferences): array
    {
        $fitnessGoal = $user->profile?->fitness_goal ?? FitnessGoal::GeneralFitness;
        $trainingExperience = $user->profile?->training_experience;
        $defaults = $this->getGoalDefaults($fitnessGoal);

        $result = [];
        $order = 1;

        foreach ($exercises as $exercise) {
            // Try to get progression-based targets from user history
            $targets = $this->progressionCalculator->calculateTargets($exercise, $user, $trainingExperience);

            // If no history (weight = 0), use fitness goal defaults for sets/reps
            if ($targets['target_weight'] == 0) {
                $targets['target_sets'] = $defaults['sets'];
                $targets['target_reps'] = $defaults['reps'];
                $targets['rest_seconds'] = $defaults['rest_seconds'];
            }

            $result[] = [
                'exercise_id' => $exercise->id,
                'order' => $order++,
                'target_sets' => $targets['target_sets'],
                'target_reps' => $targets['target_reps'],
                'target_weight' => $targets['target_weight'],
                'rest_seconds' => $targets['rest_seconds'],
            ];
        }

        return $result;
    }

    /**
     * Get default sets/reps/rest based on fitness goal
     */
    private function getGoalDefaults(FitnessGoal $goal): array
    {
        $defaults = config('workout_generator.fitness_goal_defaults', []);

        return $defaults[$goal->value] ?? [
            'sets' => 3,
            'reps' => 10,
            'rest_seconds' => 90,
        ];
    }

    /**
     * Build a rationale explaining the workout selection
     */
    private function buildRationale(Collection $exercises, array $preferences): string
    {
        $regions = $exercises->pluck('targetRegion.name')->unique()->filter()->implode(', ');
        $patterns = $exercises->pluck('movementPattern.name')->unique()->filter()->implode(', ');
        $equipmentTypes = $exercises->pluck('equipmentType.name')->unique()->filter()->implode(', ');

        $parts = [];

        if ($regions) {
            $parts[] = "targeting {$regions}";
        }

        if ($patterns) {
            $parts[] = "including {$patterns} movements";
        }

        if ($equipmentTypes) {
            $parts[] = "using {$equipmentTypes}";
        }

        if (! empty($preferences['duration_minutes'])) {
            $parts[] = "designed for approximately {$preferences['duration_minutes']} minutes";
        }

        $description = implode(', ', $parts);

        return "Generated workout {$description}. Exercises ordered from compound to isolation for optimal performance.";
    }
}
