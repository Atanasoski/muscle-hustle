<?php

namespace App\Services\WorkoutGenerator;

use App\Enums\FitnessGoal;
use App\Enums\TrainingExperience;
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
            'training_styles' => $preferences['training_styles'] ?? ['BODYBUILDING'],
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
     * Duration is the primary constraint; safety rails prevent edge cases.
     */
    private function selectDiverseExercises(EloquentCollection $exercises, array $preferences, User $user): Collection
    {
        $selected = collect();
        $seen = []; // Track movement_pattern|angle combinations
        $durationMinutes = $preferences['duration_minutes'] ?? 60;

        // Apply time buffer for warm-up/transitions
        $buffer = config('workout_generator.session_time_buffer', 0.10);
        $timeRemainingSeconds = (int) ($durationMinutes * 60 * (1 - $buffer));

        // Get safety rails and compound ratio
        $constraints = $this->getWorkoutConstraints($user);
        $safetyMin = $constraints['min'];
        $safetyMax = $constraints['max'];
        $targetCompoundRatio = $constraints['compound_ratio'];

        $maxPerRegion = config('workout_generator.max_exercises_per_region', 4);
        $maxPerPattern = config('workout_generator.max_exercises_per_pattern', 4);

        // Group exercises by target region
        $byRegion = $exercises->groupBy(fn ($e) => $e->targetRegion?->code ?? 'UNKNOWN');

        // Track exercises selected per region, per pattern, and compound/isolation
        $countByRegion = [];
        $countByPattern = [];
        $compoundCount = 0;
        $isolationCount = 0;
        $compoundPatterns = config('workout_generator.compound_patterns', []);

        // Sort regions by the order in preferences (if provided)
        $preferredRegions = $preferences['target_regions'] ?? array_keys($byRegion->toArray());

        // First pass: strict diversity (pattern|angle uniqueness)
        foreach ($preferredRegions as $region) {
            if (! $byRegion->has($region)) {
                continue;
            }

            $regionExercises = $byRegion->get($region);
            $countByRegion[$region] = 0;

            // Shuffle first for variety on regenerate, then sort by compound-first priority
            $shuffled = $regionExercises->shuffle();
            $sorted = $this->sortByCompoundPriority($shuffled, $user);

            foreach ($sorted as $exercise) {
                // Check safety ceiling (hard stop)
                if ($selected->count() >= $safetyMax) {
                    break 2;
                }

                // Check time remaining (primary constraint)
                if ($timeRemainingSeconds <= 0) {
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

                // Check pattern limit
                $patternCount = $countByPattern[$movementPattern] ?? 0;
                if ($patternCount >= $maxPerPattern) {
                    continue;
                }

                // Check compound/isolation ratio steering
                $isCompound = in_array($movementPattern, $compoundPatterns);
                $currentTotal = $compoundCount + $isolationCount;
                if ($currentTotal > 0 && $selected->count() >= $safetyMin) {
                    $currentCompoundRatio = $compoundCount / $currentTotal;
                    // If we're above target ratio, prefer isolation; if below, prefer compound
                    // Only enforce ratio when we're at or above safety min to allow flexibility below min
                    if ($isCompound && $currentCompoundRatio >= $targetCompoundRatio) {
                        // We have enough compounds, skip this one
                        continue;
                    } elseif (! $isCompound && $currentCompoundRatio < $targetCompoundRatio) {
                        // We need more compounds, skip isolation
                        continue;
                    }
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
                $countByPattern[$movementPattern] = ($countByPattern[$movementPattern] ?? 0) + 1;
                if ($isCompound) {
                    $compoundCount++;
                } else {
                    $isolationCount++;
                }
                $timeRemainingSeconds -= $exerciseTime;
            }
        }

        // Second pass: relaxed diversity (if we're below safety minimum and have time remaining)
        // Drop pattern|angle uniqueness constraint but still prevent exact duplicate exercises
        if ($selected->count() < $safetyMin && $timeRemainingSeconds > 0) {
            $selectedIds = $selected->pluck('id')->toArray();

            foreach ($preferredRegions as $region) {
                if (! $byRegion->has($region)) {
                    continue;
                }

                $regionExercises = $byRegion->get($region)->shuffle();
                $sorted = $this->sortByCompoundPriority($regionExercises, $user);

                foreach ($sorted as $exercise) {
                    // Stop if we've reached safety minimum or run out of time
                    if ($selected->count() >= $safetyMin || $timeRemainingSeconds <= 0) {
                        break 2;
                    }

                    // Skip already selected exercises
                    if (in_array($exercise->id, $selectedIds)) {
                        continue;
                    }

                    // Still respect per-pattern cap
                    $movementPattern = $exercise->movementPattern?->code ?? 'UNKNOWN';
                    $patternCount = $countByPattern[$movementPattern] ?? 0;
                    if ($patternCount >= $maxPerPattern) {
                        continue;
                    }

                    // Check compound/isolation ratio steering
                    $isCompound = in_array($movementPattern, $compoundPatterns);
                    $currentTotal = $compoundCount + $isolationCount;
                    if ($currentTotal > 0 && $selected->count() >= $safetyMin) {
                        $currentCompoundRatio = $compoundCount / $currentTotal;
                        if ($isCompound && $currentCompoundRatio >= $targetCompoundRatio) {
                            // We have enough compounds, skip this one
                            continue;
                        } elseif (! $isCompound && $currentCompoundRatio < $targetCompoundRatio) {
                            // We need more compounds, skip isolation
                            continue;
                        }
                    }

                    // Check time
                    $exerciseTime = $this->estimateExerciseTime($exercise, $user, $preferences);
                    if ($timeRemainingSeconds < $exerciseTime) {
                        continue;
                    }

                    $selected->push($exercise);
                    $selectedIds[] = $exercise->id;
                    $countByRegion[$region] = ($countByRegion[$region] ?? 0) + 1;
                    $countByPattern[$movementPattern] = ($countByPattern[$movementPattern] ?? 0) + 1;
                    if ($isCompound) {
                        $compoundCount++;
                    } else {
                        $isolationCount++;
                    }
                    $timeRemainingSeconds -= $exerciseTime;
                }
            }
        }

        return $selected;
    }

    /**
     * Sort exercises by compound-first priority while preserving shuffle order within priority groups
     * For beginners, deprioritize complex compound patterns
     */
    private function sortByCompoundPriority(Collection $exercises, User $user): Collection
    {
        $compoundPatterns = config('workout_generator.compound_patterns', []);
        $experience = $user->profile?->training_experience ?? TrainingExperience::Beginner;

        // Complex patterns that beginners should deprioritize
        $complexPatterns = ['HINGE', 'PULL_VERTICAL'];
        $isBeginner = $experience === TrainingExperience::Beginner;

        // Use stable sort to preserve shuffle order within same priority
        return $exercises->sortBy(function ($exercise) use ($compoundPatterns, $complexPatterns, $isBeginner) {
            $pattern = $exercise->movementPattern?->code;

            // Compound movements get priority 0, isolation gets 1
            $basePriority = in_array($pattern, $compoundPatterns) ? 0 : 1;

            // For beginners, deprioritize complex patterns (add 0.5 to push them after simple compounds)
            if ($isBeginner && in_array($pattern, $complexPatterns)) {
                $basePriority += 0.5;
            }

            return $basePriority;
        })->values();
    }

    /**
     * Muscle group ordering priority for bodybuilding-style workouts.
     * Lower number = earlier in workout (bigger muscles first).
     */
    private const MUSCLE_GROUP_PRIORITY = [
        // UPPER_PUSH (Push day order)
        'Chest' => 10,
        'Front Delts' => 20,
        'Side Delts' => 25,
        'Triceps' => 30,

        // UPPER_PULL (Pull day order)
        'Lats' => 10,
        'Upper Back' => 15,
        'Rear Delts' => 20,
        'Traps' => 25,
        'Biceps' => 30,
        'Forearms' => 35,

        // LOWER (Leg day order)
        'Quadriceps' => 10,
        'Glutes' => 10,
        'Hamstrings' => 20,
        'Calves' => 30,

        // CORE
        'Abs' => 10,
        'Obliques' => 20,
        'Lower Back' => 25,
    ];

    /**
     * Order all selected exercises: by muscle group priority, then compound first
     */
    private function orderByCompoundFirst(Collection $exercises): Collection
    {
        $compoundPatterns = config('workout_generator.compound_patterns', []);

        return $exercises->sortBy(function ($exercise) use ($compoundPatterns) {
            // Get primary muscle group priority (use the first/highest priority primary muscle)
            $primaryMuscles = $exercise->muscleGroups->filter(function ($muscle) {
                return $muscle->pivot->is_primary ?? false;
            });

            $muscleGroupPriority = 100; // Default for unknown muscles

            foreach ($primaryMuscles as $muscle) {
                $priority = self::MUSCLE_GROUP_PRIORITY[$muscle->name] ?? 50;
                $muscleGroupPriority = min($muscleGroupPriority, $priority);
            }

            // Compound vs isolation (0 for compound, 1 for isolation)
            $pattern = $exercise->movementPattern?->code;
            $compoundPriority = in_array($pattern, $compoundPatterns) ? 0 : 1;

            // Combine: muscle group (0-100) * 10 + compound priority (0-1)
            // This ensures muscle group order takes precedence, then compound/isolation within each group
            return ($muscleGroupPriority * 10) + $compoundPriority;
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

        $isCompound = in_array(
            $exercise->movementPattern?->code,
            config('workout_generator.compound_patterns', [])
        );

        // Compound exercises use full goal defaults
        // Isolation exercises use fewer sets and shorter rest
        if ($isCompound) {
            $sets = $defaults['sets'];
            $restSeconds = $defaults['rest_seconds'];
        } else {
            $sets = max(2, $defaults['sets'] - 1);
            $restSeconds = (int) ($defaults['rest_seconds'] * 0.5);
        }

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
     * Get workout constraints: safety rails and compound ratio based on fitness goal
     */
    private function getWorkoutConstraints(User $user): array
    {
        $fitnessGoal = $user->profile?->fitness_goal ?? FitnessGoal::GeneralFitness;

        $safety = config('workout_generator.exercise_count_safety', ['min' => 3, 'max' => 12]);
        $compoundRatios = config('workout_generator.compound_ratios', []);

        $goalKey = $fitnessGoal->value;
        $compoundRatio = $compoundRatios[$goalKey] ?? 0.65;

        return [
            'min' => $safety['min'],
            'max' => $safety['max'],
            'compound_ratio' => $compoundRatio,
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
