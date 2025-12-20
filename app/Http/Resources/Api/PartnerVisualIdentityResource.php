<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerVisualIdentityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'logo' => $this->logo,
            'font_family' => $this->font_family,
        ];
    }
}
