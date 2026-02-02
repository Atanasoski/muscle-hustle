<?php

namespace App\Services\WorkoutGenerator;

use App\Enums\TrainingExperience;
use App\Enums\WorkoutSessionStatus;
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
            return $this->getDefaultTargets($experience ?? TrainingExperience::Beginner, $exercise, $user);
        }

        // Apply progressive overload with equipment-based rounding
        $targets = $this->applyProgressiveOverload($lastPerformance, $experience ?? TrainingExperience::Beginner, $exercise);

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
            ->where('status', WorkoutSessionStatus::Completed)
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
    public function applyProgressiveOverload(array $lastPerformance, TrainingExperience $experience, ?Exercise $exercise = null): array
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

        // Calculate new weight with progression
        $rawNewWeight = $weight * (1 + $progressionPercent);

        // Get the equipment increment
        $increment = $exercise ? $this->getWeightIncrement($exercise) : 2.5;

        // Ensure at least one increment increase (if weight > 0)
        if ($weight > 0 && $increment > 0) {
            $rawNewWeight = max($rawNewWeight, $weight + $increment);
        }

        // Round to realistic weight increments based on equipment type
        $newWeight = $this->roundToEquipmentIncrement($rawNewWeight, $exercise);

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
     * Round a weight to the nearest realistic equipment increment
     */
    private function roundToEquipmentIncrement(float $weight, ?Exercise $exercise): float
    {
        if (! $exercise) {
            // Fallback to 2.5kg if no exercise provided
            return round($weight / 2.5) * 2.5;
        }

        $increment = $this->getWeightIncrement($exercise);

        // If increment is 0 (bodyweight/band), return 0
        if ($increment <= 0) {
            return 0;
        }

        return max(0, round($weight / $increment) * $increment);
    }

    /**
     * Get default targets based on experience level
     */
    private function getDefaultTargets(TrainingExperience $experience, ?Exercise $exercise = null, ?User $user = null): array
    {
        $targetWeight = 0;

        if ($exercise && $user) {
            $targetWeight = $this->estimateStartingWeight($exercise, $user, $experience);
        }

        return match ($experience) {
            TrainingExperience::Beginner => [
                'target_sets' => 3,
                'target_reps' => 10,
                'target_weight' => $targetWeight,
                'rest_seconds' => 90,
            ],
            TrainingExperience::Intermediate => [
                'target_sets' => 4,
                'target_reps' => 8,
                'target_weight' => $targetWeight,
                'rest_seconds' => 90,
            ],
            TrainingExperience::Advanced => [
                'target_sets' => 4,
                'target_reps' => 6,
                'target_weight' => $targetWeight,
                'rest_seconds' => 120,
            ],
        };
    }

    /**
     * Estimate starting weight based on user profile and exercise characteristics
     */
    private function estimateStartingWeight(Exercise $exercise, User $user, TrainingExperience $experience): float
    {
        $profile = $user->profile;

        if (! $profile || ! $profile->weight) {
            return 0; // Can't estimate without body weight
        }

        $bodyWeight = (float) $profile->weight;
        $gender = $profile->gender ?? \App\Enums\Gender::Male;

        // Get multiplier based on movement pattern
        $movementCode = $exercise->movementPattern?->code ?? 'UNKNOWN';

        // Base multipliers by movement pattern (as fraction of body weight)
        $baseMultiplier = $this->getBaseMultiplier($movementCode);

        // Adjust for gender (females typically ~60-70% of male strength ratios)
        $genderModifier = match ($gender) {
            \App\Enums\Gender::Female => 0.65,
            \App\Enums\Gender::Other => 0.80,
            default => 1.0,
        };

        // Adjust for experience
        $experienceModifier = match ($experience) {
            TrainingExperience::Beginner => 0.6,      // Start conservative but realistic
            TrainingExperience::Intermediate => 1.0,
            TrainingExperience::Advanced => 1.3,
        };

        $estimatedWeight = $bodyWeight * $baseMultiplier * $genderModifier * $experienceModifier;

        // Round to realistic weight increments based on equipment type
        return $this->roundToEquipmentIncrement($estimatedWeight, $exercise);
    }

    /**
     * Get the appropriate weight increment based on equipment type
     *
     * Barbell: 2.5kg (smallest common plate pair = 1.25kg × 2)
     * Dumbbell: Fixed increments, typically 2kg or 2.5kg jumps
     * Cable/Machine: Usually 2.5kg or 5kg pin increments
     * Kettlebell: Fixed weights, typically 4kg jumps
     */
    private function getWeightIncrement(Exercise $exercise): float
    {
        $equipmentCode = $exercise->equipmentType?->code ?? $exercise->equipmentType?->name ?? 'UNKNOWN';

        return match (strtoupper($equipmentCode)) {
            'BARBELL' => 2.5,           // 1.25kg plates on each side
            'DUMBBELL' => 2.0,          // Fixed dumbbell increments (per hand)
            'CABLE' => 2.5,             // Cable stack increments
            'MACHINE' => 5.0,           // Machine weight stack increments
            'SMITH' => 2.5,             // Similar to barbell
            'KETTLEBELL' => 4.0,        // Standard KB jumps (8, 12, 16, 20, 24...)
            'BAND' => 0.0,              // Bands don't have weight
            'BODYWEIGHT' => 0.0,        // No external weight
            default => 2.5,             // Safe default
        };
    }

    /**
     * Base multipliers as fraction of body weight (for intermediate male)
     * These are realistic starting points based on fitness standards
     */
    private function getBaseMultiplier(string $movementCode): float
    {
        return match ($movementCode) {
            // Compound Lower Body (strongest)
            'SQUAT', 'LEG_PRESS' => 1.0,
            'HINGE' => 1.1,                           // Deadlift patterns
            'HIP_THRUST_BRIDGE' => 0.8,
            'LUNGE_SPLIT_SQUAT' => 0.4,               // Per leg
            'BACK_EXTENSION' => 0.5,

            // Compound Upper Body
            'PRESS' => 0.65,                          // Bench press type
            'ROW' => 0.55,
            'PULL_VERTICAL' => 0.5,                   // Lat pulldown
            'DIP', 'PUSHUP' => 0.0,                   // Bodyweight
            'PULLOVER_STRAIGHT_ARM' => 0.25,

            // Isolation
            'ELBOW_FLEXION' => 0.15,                  // Bicep curls
            'ELBOW_EXTENSION' => 0.18,                // Tricep exercises
            'FLY' => 0.20,                            // Chest flies
            'KNEE_EXTENSION' => 0.35,                 // Leg extension
            'KNEE_FLEXION' => 0.30,                   // Leg curl
            'CALF_RAISE' => 0.7,
            'REAR_DELT_FLY', 'FACE_PULL' => 0.12,
            'HIP_ABDUCTION' => 0.20,
            'CARRY' => 0.35,

            // Core (often bodyweight or light)
            'TRUNK_FLEXION', 'ROTATION', 'ANTI_ROTATION' => 0.0,

            default => 0.30,                          // Conservative fallback
        };
    }

    /**
     * Check if a movement is lower body focused
     */
    private function isLowerBodyMovement(string $movementCode): bool
    {
        return in_array($movementCode, [
            'SQUAT', 'HINGE', 'LUNGE_SPLIT_SQUAT', 'LEG_PRESS',
            'KNEE_EXTENSION', 'KNEE_FLEXION', 'HIP_THRUST_BRIDGE',
            'HIP_ABDUCTION', 'CALF_RAISE', 'BACK_EXTENSION',
        ]);
    }
}
