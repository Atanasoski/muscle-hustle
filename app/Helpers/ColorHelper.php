<?php

namespace App\Helpers;

use App\Models\PartnerIdentity;

class ColorHelper
{
    /**
     * Process partner identity colors and return hex values with defaults from config.
     */
    public static function processPartnerColors(?PartnerIdentity $identity): array
    {
        $defaults = config('branding.light');

        if (! $identity) {
            return $defaults;
        }

        return [
            'primary' => $identity->primary_color ?? $defaults['primary'],
            'secondary' => $identity->secondary_color ?? $defaults['secondary'],
            'background' => $identity->background_color ?? $defaults['background'],
            'card_background' => $identity->card_background_color ?? $defaults['card_background'],
            'text_primary' => $identity->text_primary_color ?? $defaults['text_primary'],
            'text_secondary' => $identity->text_secondary_color ?? $defaults['text_secondary'],
            'text_on_primary' => $identity->text_on_primary_color ?? $defaults['text_on_primary'],
            'success' => $identity->success_color ?? $defaults['success'],
            'warning' => $identity->warning_color ?? $defaults['warning'],
            'danger' => $identity->danger_color ?? $defaults['danger'],
            'accent' => $identity->accent_color ?? $defaults['accent'],
            'border' => $identity->border_color ?? $defaults['border'],
        ];
    }

    /**
     * Get light mode color palette array for display.
     */
    public static function getColorPalette(?PartnerIdentity $identity): array
    {
        $colors = self::processPartnerColors($identity);

        return [
            ['name' => 'Primary', 'value' => $colors['primary']],
            ['name' => 'Secondary', 'value' => $colors['secondary']],
            ['name' => 'Background', 'value' => $colors['background']],
            ['name' => 'Card Background', 'value' => $colors['card_background']],
            ['name' => 'Text Primary', 'value' => $colors['text_primary']],
            ['name' => 'Text Secondary', 'value' => $colors['text_secondary']],
            ['name' => 'Text On Primary', 'value' => $colors['text_on_primary']],
            ['name' => 'Success', 'value' => $colors['success']],
            ['name' => 'Warning', 'value' => $colors['warning']],
            ['name' => 'Danger', 'value' => $colors['danger']],
            ['name' => 'Accent', 'value' => $colors['accent']],
            ['name' => 'Border', 'value' => $colors['border']],
        ];
    }

    /**
     * Get dark mode color palette array for display.
     */
    public static function getDarkColorPalette(?PartnerIdentity $identity): array
    {
        $defaults = config('branding.dark');

        if (! $identity) {
            return array_map(fn ($key, $value) => ['name' => ucwords(str_replace('_', ' ', $key)), 'value' => $value], array_keys($defaults), $defaults);
        }

        return [
            ['name' => 'Primary', 'value' => $identity->primary_color_dark ?? $defaults['primary']],
            ['name' => 'Secondary', 'value' => $identity->secondary_color_dark ?? $defaults['secondary']],
            ['name' => 'Background', 'value' => $identity->background_color_dark ?? $defaults['background']],
            ['name' => 'Card Background', 'value' => $identity->card_background_color_dark ?? $defaults['card_background']],
            ['name' => 'Text Primary', 'value' => $identity->text_primary_color_dark ?? $defaults['text_primary']],
            ['name' => 'Text Secondary', 'value' => $identity->text_secondary_color_dark ?? $defaults['text_secondary']],
            ['name' => 'Text On Primary', 'value' => $identity->text_on_primary_color_dark ?? $defaults['text_on_primary']],
            ['name' => 'Success', 'value' => $identity->success_color_dark ?? $defaults['success']],
            ['name' => 'Warning', 'value' => $identity->warning_color_dark ?? $defaults['warning']],
            ['name' => 'Danger', 'value' => $identity->danger_color_dark ?? $defaults['danger']],
            ['name' => 'Accent', 'value' => $identity->accent_color_dark ?? $defaults['accent']],
            ['name' => 'Border', 'value' => $identity->border_color_dark ?? $defaults['border']],
        ];
    }
}
