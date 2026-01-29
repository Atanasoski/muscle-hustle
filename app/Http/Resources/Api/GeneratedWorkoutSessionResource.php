<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneratedWorkoutSessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use the base WorkoutSessionResource for the main data
        $baseResource = new WorkoutSessionResource($this->resource);
        $baseData = $baseResource->toArray($request);

        // Add auto-generation specific fields
        return array_merge($baseData, [
            'is_auto_generated' => $this->is_auto_generated ?? false,
            'status' => $this->status?->value,
            'replaced_session_id' => $this->replaced_session_id,
            'rationale' => $this->notes,
        ]);
    }
}
