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
            // Core branding
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'logo' => $this->logo,
            'font_family' => $this->font_family,

            // Essential colors
            'background_color' => $this->background_color,
            'card_background_color' => $this->card_background_color,
            'text_primary_color' => $this->text_primary_color,
            'text_secondary_color' => $this->text_secondary_color,
            'text_on_primary_color' => $this->text_on_primary_color,

            // Semantic colors
            'success_color' => $this->success_color,
            'warning_color' => $this->warning_color,
            'danger_color' => $this->danger_color,

            // Optional styling
            'accent_color' => $this->accent_color,
            'border_color' => $this->border_color,
            'background_pattern' => $this->background_pattern,
        ];
    }
}
