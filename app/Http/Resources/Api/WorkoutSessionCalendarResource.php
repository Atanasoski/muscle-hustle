<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutSessionCalendarResource extends JsonResource
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
            'date' => $this->performed_at->format('Y-m-d'),
            'completed' => $this->completed_at !== null,
            'workout_template_id' => $this->workout_template_id,
            'workout_name' => $this->workoutTemplate?->name,
            'duration_minutes' => $this->getDurationMinutes(),
        ];
    }

    /**
     * Calculate duration in minutes between performed_at and completed_at.
     */
    private function getDurationMinutes(): ?int
    {
        if ($this->completed_at === null || $this->performed_at === null) {
            return null;
        }

        return $this->performed_at->diffInMinutes($this->completed_at);
    }
}
