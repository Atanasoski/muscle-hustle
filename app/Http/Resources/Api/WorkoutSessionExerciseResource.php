<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Concerns\FormatsWeights;
use App\Services\WorkoutGenerator\ProgressionCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutSessionExerciseResource extends JsonResource
{
    use FormatsWeights;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $progressionCalculator = new ProgressionCalculatorService();
        $targets = $progressionCalculator->calculateTargets($this->exercise, auth()->user(), auth()->user()->profile?->training_experience);

        $targetSets = $this->target_sets;
        $targetReps = $this->target_reps;
        $targetWeight = $this->formatWeight($this->target_weight);
        $restSeconds = $this->rest_seconds;

        //We want the default targets to have presidence over the progressive targets
        //in order to allow for Personal Trainers to overide the progressive targets

        if(!$targetSets) {
            $targetSets = $targets['target_sets'];
        }
        if(!$targetReps) {
            $targetReps = $targets['target_reps'];
        }
        if(!$targetWeight) {
            $targetWeight = $targets['target_weight'];
        }
        if(!$restSeconds) {
            $restSeconds = $targets['rest_seconds'];
        }

        return [
            'id' => $this->id,
            'workout_session_id' => $this->workout_session_id,
            'exercise_id' => $this->exercise_id,
            'exercise' => $this->whenLoaded('exercise', function () {
                return new ExerciseResource($this->exercise->load('partners', 'muscleGroups', 'primaryMuscleGroups', 'secondaryMuscleGroups'));
            }),
            'order' => $this->order,
            'target_sets' => $targetSets,
            'target_reps' => $targetReps,
            'target_weight' => $this->formatWeight($targetWeight),
            'rest_seconds' => $restSeconds,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
