<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AIGeneratedSessionResource extends JsonResource
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

        // Add AI-specific fields
        return array_merge($baseData, [
            'is_ai_generated' => $this->is_ai_generated ?? false,
            'ai_generated_at' => $this->ai_generated_at,
            'rationale' => $this->extractRationaleFromNotes(),
        ]);
    }

    /**
     * Extract rationale from notes if it exists
     */
    private function extractRationaleFromNotes(): ?string
    {
        if (! $this->notes) {
            return null;
        }

        // Check if notes start with "AI Generated: "
        if (str_starts_with($this->notes, 'AI Generated: ')) {
            return substr($this->notes, 15); // Remove "AI Generated: " prefix
        }

        return null;
    }
}
