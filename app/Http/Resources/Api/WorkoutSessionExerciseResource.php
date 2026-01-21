<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutSessionExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'workout_session_id' => $this->workout_session_id,
            'exercise_id' => $this->exercise_id,
            'exercise' => $this->whenLoaded('exercise', function () {
                return new ExerciseResource($this->exercise->load('partners', 'muscleGroups', 'primaryMuscleGroups', 'secondaryMuscleGroups'));
            }),
            'order' => $this->order,
            'target_sets' => $this->target_sets,
            'target_reps' => $this->target_reps,
            'target_weight' => $this->target_weight,
            'rest_seconds' => $this->rest_seconds,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
