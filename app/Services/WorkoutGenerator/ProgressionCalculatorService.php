<?php

namespace App\Services\WorkoutGenerator;

use App\Enums\TrainingExperience;
use App\Models\Exercise;
use App\Models\User;

class ProgressionCalculatorService
{
    /**
     * Calculate target sets, reps, and weight for an exercise based on user history
     */
    public function calculateTargets(Exercise $exercise, User $user, ?TrainingExperience $experience = null): array
    {
        $lastPerformance = $this->getLastPerformance($exercise, $user);

        if (! $lastPerformance) {
            // No history - return defaults based on experience level
            return $this->getDefaultTargets($experience ?? TrainingExperience::Beginner);
        }

        // Apply progressive overload
        $targets = $this->applyProgressiveOverload($lastPerformance, $experience ?? TrainingExperience::Beginner);

        return [
            'target_sets' => $targets['sets'],
            'target_reps' => $targets['reps'],
            'target_weight' => $targets['weight'],
            'rest_seconds' => $lastPerformance['rest_seconds'] ?? $exercise->default_rest_sec ?? 90,
        ];
    }

    /**
     * Get last performance for an exercise
     */
    public function getLastPerformance(Exercise $exercise, User $user): ?array
    {
        $lastSetLog = $user->workoutSessions()
            ->whereNotNull('completed_at')
            ->whereHas('setLogs', function ($q) use ($exercise) {
                $q->where('exercise_id', $exercise->id);
            })
            ->with(['setLogs' => function ($q) use ($exercise) {
                $q->where('exercise_id', $exercise->id)
                    ->orderBy('set_number', 'desc');
            }])
            ->orderBy('completed_at', 'desc')
            ->first();

        if (! $lastSetLog || $lastSetLog->setLogs->isEmpty()) {
            return null;
        }

        // Get the best set (highest weight × reps)
        $bestSet = $lastSetLog->setLogs->max(function ($set) {
            return $set->weight * $set->reps;
        });

        $bestSet = $lastSetLog->setLogs->first(function ($set) use ($bestSet) {
            return ($set->weight * $set->reps) === $bestSet;
        });

        return [
            'weight' => $bestSet->weight,
            'reps' => $bestSet->reps,
            'sets' => $lastSetLog->setLogs->count(),
            'rest_seconds' => $bestSet->rest_seconds ?? $exercise->default_rest_sec ?? 90,
        ];
    }

    /**
     * Estimate one rep max using Epley formula
     */
    public function estimateOneRepMax(float $weight, int $reps): float
    {
        if ($reps <= 0) {
            return $weight;
        }

        if ($reps === 1) {
            return $weight;
        }

        // Epley formula: 1RM = weight × (1 + reps/30)
        return $weight * (1 + ($reps / 30));
    }

    /**
     * Apply progressive overload based on last performance
     */
    public function applyProgressiveOverload(array $lastPerformance, TrainingExperience $experience): array
    {
        $weight = $lastPerformance['weight'];
        $reps = $lastPerformance['reps'];
        $sets = $lastPerformance['sets'] ?? 3;

        // Calculate progression percentage based on experience
        $progressionPercent = match ($experience) {
            TrainingExperience::Beginner => 0.05, // 5% increase
            TrainingExperience::Intermediate => 0.025, // 2.5% increase
            TrainingExperience::Advanced => 0.02, // 2% increase
        };

        // Increase weight
        $newWeight = round($weight * (1 + $progressionPercent), 1);

        // Maintain or slightly increase reps
        $newReps = $reps;

        // Maintain sets
        $newSets = $sets;

        return [
            'weight' => $newWeight,
            'reps' => $newReps,
            'sets' => $newSets,
        ];
    }

    /**
     * Get default targets based on experience level
     */
    private function getDefaultTargets(TrainingExperience $experience): array
    {
        return match ($experience) {
            TrainingExperience::Beginner => [
                'target_sets' => 3,
                'target_reps' => 10,
                'target_weight' => 0,
                'rest_seconds' => 90,
            ],
            TrainingExperience::Intermediate => [
                'target_sets' => 4,
                'target_reps' => 8,
                'target_weight' => 0,
                'rest_seconds' => 90,
            ],
            TrainingExperience::Advanced => [
                'target_sets' => 4,
                'target_reps' => 6,
                'target_weight' => 0,
                'rest_seconds' => 120,
            ],
        };
    }
}
