<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutTemplateResource extends JsonResource
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
            'plan_id' => $this->plan_id,
            'name' => $this->name,
            'description' => $this->description,
            'day_of_week' => $this->day_of_week,
            'plan' => $this->whenLoaded('plan', function () {
                return new PlanResource($this->plan);
            }),
            'exercises' => $this->whenLoaded('exercises', function () {
                return $this->exercises->map(function ($exercise) {
                    return [
                        'id' => $exercise->id,
                        'name' => $exercise->name,
                        'image' => $exercise->image ? asset('storage/'.$exercise->image) : null,
                        'default_rest_sec' => $exercise->default_rest_sec,
                        'category' => $exercise->category ? new CategoryResource($exercise->category) : null,
                        'muscle_groups' => $exercise->relationLoaded('muscleGroups')
                            ? MuscleGroupResource::collection($exercise->muscleGroups)
                            : [],
                        'pivot' => [
                            'id' => $exercise->pivot->id,
                            'order' => $exercise->pivot->order,
                            'target_sets' => $exercise->pivot->target_sets,
                            'target_reps' => $exercise->pivot->target_reps,
                            'target_weight' => $exercise->pivot->target_weight,
                            'rest_seconds' => $exercise->pivot->rest_seconds,
                        ],
                    ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
