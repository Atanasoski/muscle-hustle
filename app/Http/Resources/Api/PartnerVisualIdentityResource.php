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
            'primary_color' => $this->hexToRgb($this->primary_color),
            'secondary_color' => $this->hexToRgb($this->secondary_color),
            'logo' => $this->logo,
            'font_family' => $this->font_family,
        ];
    }

    /**
     * Convert hex color to RGB string format (e.g., "255,107,53").
     */
    private function hexToRgb(?string $hex): ?string
    {
        if (! $hex) {
            return null;
        }

        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert hex to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "{$r},{$g},{$b}";
    }
}
