<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'exercises' => $this->formatExercises($this->resource['exercises']),
            'rationale' => $this->resource['rationale'],
            'estimated_duration_minutes' => $this->resource['estimated_duration_minutes'],
        ];
    }

    /**
     * Format exercises for the preview response
     */
    private function formatExercises(array $exercises): array
    {
        return array_map(function ($exerciseData) {
            return [
                'exercise_id' => $exerciseData['exercise_id'],
                'exercise' => $exerciseData['exercise'] ? new ExerciseResource($exerciseData['exercise']) : null,
                'order' => $exerciseData['order'],
                'target_sets' => $exerciseData['target_sets'],
                'target_reps' => $exerciseData['target_reps'],
                'target_weight' => $exerciseData['target_weight'],
                'rest_seconds' => $exerciseData['rest_seconds'],
            ];
        }, $exercises);
    }
}
