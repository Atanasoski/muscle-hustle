<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
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
            'slug' => $this->slug,
            'domain' => $this->domain,
            'is_active' => $this->is_active,
            'identity' => $this->whenLoaded('identity', function () {
                return [
                    'primary_color' => $this->identity->primary_color,
                    'secondary_color' => $this->identity->secondary_color,
                    'logo' => $this->identity->logo,
                    'font_family' => $this->identity->font_family,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
