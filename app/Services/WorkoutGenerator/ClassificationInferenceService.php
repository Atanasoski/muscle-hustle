<?php

namespace App\Services\WorkoutGenerator;

use App\Models\EquipmentType;
use App\Models\TargetRegion;
use App\Models\User;

class ClassificationInferenceService
{
    /**
     * Normalize preferences by inferring missing values from user profile and muscle groups
     */
    public function normalizePreferences(User $user, array $preferences): array
    {
        $normalized = $preferences;

        // Infer target_regions from focus_muscle_groups if not provided
        if (empty($normalized['target_regions']) && ! empty($normalized['focus_muscle_groups'])) {
            $normalized['target_regions'] = $this->inferTargetRegions($normalized['focus_muscle_groups']);
        }

        // If no target_regions and no focus_muscle_groups, use all target regions (full body)
        if (empty($normalized['target_regions'])) {
            $normalized['target_regions'] = $this->getAllTargetRegionCodes();
        }

        // Default equipment types to all if not provided
        if (empty($normalized['equipment_types'])) {
            $normalized['equipment_types'] = $this->getDefaultEquipmentTypes();
        }

        // Use profile defaults for duration and difficulty if not provided
        $profile = $user->profile;
        if (empty($normalized['duration_minutes']) && $profile && $profile->workout_duration_minutes) {
            $normalized['duration_minutes'] = $profile->workout_duration_minutes;
        }

        if (empty($normalized['difficulty']) && $profile && $profile->training_experience) {
            $normalized['difficulty'] = $profile->training_experience->value;
        }

        return $normalized;
    }

    /**
     * Infer target regions from muscle group names
     */
    public function inferTargetRegions(array $muscleGroups): array
    {
        $targetRegions = [];

        foreach ($muscleGroups as $muscleGroup) {
            $muscleGroupLower = strtolower($muscleGroup);

            // Upper push muscles
            if (in_array($muscleGroupLower, ['chest', 'triceps', 'front delts', 'front deltoids', 'side delts', 'side deltoids', 'shoulders'])) {
                if (! in_array('UPPER_PUSH', $targetRegions)) {
                    $targetRegions[] = 'UPPER_PUSH';
                }
            }

            // Upper pull muscles
            if (in_array($muscleGroupLower, ['lats', 'biceps', 'rear delts', 'rear deltoids', 'traps', 'trapezius', 'upper back', 'lower back', 'back'])) {
                if (! in_array('UPPER_PULL', $targetRegions)) {
                    $targetRegions[] = 'UPPER_PULL';
                }
            }

            // Lower body muscles
            if (in_array($muscleGroupLower, ['quads', 'quadriceps', 'hamstrings', 'glutes', 'calves', 'legs'])) {
                if (! in_array('LOWER', $targetRegions)) {
                    $targetRegions[] = 'LOWER';
                }
            }

            // Arm muscles (can overlap with upper push/pull)
            if (in_array($muscleGroupLower, ['biceps', 'triceps', 'forearms', 'arms'])) {
                if (! in_array('ARMS', $targetRegions)) {
                    $targetRegions[] = 'ARMS';
                }
            }

            // Core muscles
            if (in_array($muscleGroupLower, ['abs', 'abdominals', 'obliques', 'core'])) {
                if (! in_array('CORE', $targetRegions)) {
                    $targetRegions[] = 'CORE';
                }
            }
        }

        // If no target regions inferred, return all
        if (empty($targetRegions)) {
            return $this->getAllTargetRegionCodes();
        }

        return $targetRegions;
    }

    /**
     * Get all available target region codes
     */
    public function getAllTargetRegionCodes(): array
    {
        return TargetRegion::orderBy('display_order')->pluck('code')->toArray();
    }

    /**
     * Get default equipment types (all available)
     */
    public function getDefaultEquipmentTypes(): array
    {
        return EquipmentType::orderBy('display_order')->pluck('code')->toArray();
    }
}
