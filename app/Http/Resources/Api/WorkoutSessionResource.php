<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutSessionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'workout_template_id' => $this->workout_template_id,
            'performed_at' => $this->performed_at,
            'completed_at' => $this->completed_at,
            'notes' => $this->notes,
            'exercises' => WorkoutSessionExerciseResource::collection($this->whenLoaded('workoutSessionExercises')),
            'set_logs' => SetLogResource::collection($this->whenLoaded('setLogs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
