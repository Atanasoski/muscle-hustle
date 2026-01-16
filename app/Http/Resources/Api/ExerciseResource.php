<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
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
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'muscle_groups' => $this->whenLoaded('muscleGroups', function () {
                return MuscleGroupResource::collection($this->muscleGroups);
            }),
            'primary_muscle_groups' => $this->whenLoaded('primaryMuscleGroups', function () {
                return MuscleGroupResource::collection($this->primaryMuscleGroups);
            }),
            'secondary_muscle_groups' => $this->whenLoaded('secondaryMuscleGroups', function () {
                return MuscleGroupResource::collection($this->secondaryMuscleGroups);
            }),
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'default_rest_sec' => $this->default_rest_sec,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
