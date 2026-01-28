<?php

namespace App\Services\AI;

use App\Models\Exercise;

class ResponseParserService
{
    /**
     * Parse AI session response
     */
    public function parseSessionResponse(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON response from AI: '.json_last_error_msg());
        }

        if (! isset($data['exercises']) || ! is_array($data['exercises'])) {
            throw new \InvalidArgumentException('AI response missing exercises array');
        }

        // Filter out exercises with invalid IDs
        $exerciseIds = array_column($data['exercises'], 'exercise_id');
        $validExerciseIds = $this->getValidExerciseIds($exerciseIds);

        // Filter exercises to only include valid ones
        $validExercises = array_filter($data['exercises'], function ($exercise) use ($validExerciseIds) {
            return isset($exercise['exercise_id']) && in_array($exercise['exercise_id'], $validExerciseIds);
        });

        // Re-index array
        $validExercises = array_values($validExercises);

        if (empty($validExercises)) {
            \Illuminate\Support\Facades\Log::warning('AI returned no valid exercises', [
                'requested_ids' => $exerciseIds,
                'valid_ids' => $validExerciseIds,
                'raw_exercises' => $data['exercises'],
            ]);
            throw new \InvalidArgumentException('AI returned no valid exercises. Please try again.');
        }

        // Log if some exercises were filtered out
        if (count($validExercises) < count($data['exercises'])) {
            $filteredIds = array_diff($exerciseIds, $validExerciseIds);
            \Illuminate\Support\Facades\Log::warning('Some AI exercise IDs were invalid and filtered out', [
                'filtered_ids' => $filteredIds,
                'kept_count' => count($validExercises),
                'total_count' => count($data['exercises']),
            ]);
        }

        return [
            'exercises' => $validExercises,
            'rationale' => $data['rationale'] ?? 'AI-generated workout session',
        ];
    }

    /**
     * Get valid exercise IDs that exist in database
     */
    public function getValidExerciseIds(array $exerciseIds): array
    {
        if (empty($exerciseIds)) {
            return [];
        }

        return Exercise::whereIn('id', $exerciseIds)->pluck('id')->toArray();
    }
}
