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
            'muscle_group_image' => $this->muscle_group_image ? asset('storage/'.$this->muscle_group_image) : null,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'video' => $this->video ? asset('storage/'.$this->video) : null,
            'default_rest_sec' => $this->default_rest_sec,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
