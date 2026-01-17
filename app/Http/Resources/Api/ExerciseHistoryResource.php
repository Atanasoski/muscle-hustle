<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'exercise_id' => $this->resource['exercise_id'],
            'exercise_name' => $this->resource['exercise_name'],
            'stats' => $this->resource['stats'],
            'performance_data' => $this->resource['performance_data'],
        ];
    }
}
