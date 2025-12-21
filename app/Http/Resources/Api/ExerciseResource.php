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
            'user_id' => $this->user_id,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'name' => $this->name,
            'image_url' => $this->image_url,
            'default_rest_sec' => $this->default_rest_sec,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
