<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'profile_photo' => $this->profile_photo,
            'profile' => $this->whenLoaded('profile', function () {
                return [
                    'fitness_goal' => $this->profile->fitness_goal?->value,
                    'age' => $this->profile->age,
                    'gender' => $this->profile->gender?->value,
                    'height' => $this->profile->height,
                    'weight' => $this->profile->weight,
                    'training_experience' => $this->profile->training_experience?->value,
                    'training_days_per_week' => $this->profile->training_days_per_week,
                    'workout_duration_minutes' => $this->profile->workout_duration_minutes,
                ];
            }),
            'partner' => $this->whenLoaded('partner', function () {
                return [
                    'id' => $this->partner->id,
                    'name' => $this->partner->name,
                    'slug' => $this->partner->slug,
                    'visual_identity' => $this->partner->identity
                        ? new PartnerVisualIdentityResource($this->partner->identity)
                        : null,
                ];
            }),
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
