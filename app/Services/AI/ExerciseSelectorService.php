<?php

namespace App\Services\AI;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Collection;

class ExerciseSelectorService
{
    /**
     * Get available exercises based on filters
     */
    public function getAvailableExercises(array $filters = []): Collection
    {
        $query = Exercise::with(['muscleGroups', 'category']);

        // Filter by muscle groups if provided
        if (! empty($filters['focus_muscle_groups'])) {
            $query->whereHas('muscleGroups', function ($q) use ($filters) {
                $q->whereIn('name', $filters['focus_muscle_groups']);
            });
        }

        // Filter by preferred categories (slugs) if provided
        if (! empty($filters['preferred_categories'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->whereIn('slug', $filters['preferred_categories']);
            });
        }

        // Limit to reasonable number for AI context (avoid token limits)
        $limit = $filters['limit'] ?? 200;

        return $query->limit($limit)->get();
    }

    /**
     * Filter exercises by muscle groups
     */
    public function filterByMuscleGroups(Collection $exercises, array $muscleGroups): Collection
    {
        if (empty($muscleGroups)) {
            return $exercises;
        }

        return $exercises->filter(function ($exercise) use ($muscleGroups) {
            $exerciseMuscleGroups = $exercise->muscleGroups->pluck('name')->toArray();

            return ! empty(array_intersect($muscleGroups, $exerciseMuscleGroups));
        });
    }

    /**
     * Format exercises for AI context
     */
    public function formatForAI(Collection $exercises): array
    {
        return $exercises->map(function ($exercise) {
            $primaryMuscleGroups = $exercise->primaryMuscleGroups->pluck('name')->toArray();
            $secondaryMuscleGroups = $exercise->secondaryMuscleGroups->pluck('name')->toArray();

            return [
                'id' => $exercise->id,
                'name' => $exercise->name,
                'description' => $exercise->description,
                'category' => $exercise->category?->name,
                'primary_muscle_groups' => $primaryMuscleGroups,
                'secondary_muscle_groups' => $secondaryMuscleGroups,
                'default_rest_sec' => $exercise->default_rest_sec,
            ];
        })->toArray();
    }
}
