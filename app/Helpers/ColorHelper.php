<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Convert RGB to hex (for backward compatibility with existing data)
     */
    public static function rgbToHex(?string $rgb, string $default = '#000000'): string
    {
        if (! $rgb) {
            return $default;
        }

        // Check if already hex format
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $rgb)) {
            return $rgb;
        }

        // Convert RGB format to hex
        $parts = explode(',', $rgb);
        if (count($parts) !== 3) {
            return $default;
        }

        $r = (int) trim($parts[0]);
        $g = (int) trim($parts[1]);
        $b = (int) trim($parts[2]);

        return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT).
               str_pad(dechex($g), 2, '0', STR_PAD_LEFT).
               str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Process partner identity colors and return hex values
     */
    public static function processPartnerColors($identity): array
    {
        if (! $identity) {
            return [
                'primary' => '#ff6b35',
                'secondary' => '#4ecdc4',
                'background' => '#ffffff',
                'card_background' => '#ffffff',
                'text_primary' => '#000000',
                'text_secondary' => '#6b7280',
                'text_on_primary' => '#ffffff',
                'success' => '#10dc60',
                'warning' => '#ffce00',
                'danger' => '#f04141',
                'accent' => '#8ac34a',
                'border' => '#dee2e6',
            ];
        }

        return [
            'primary' => self::rgbToHex($identity->primary_color, '#ff6b35'),
            'secondary' => self::rgbToHex($identity->secondary_color, '#4ecdc4'),
            'background' => self::rgbToHex($identity->background_color, '#ffffff'),
            'card_background' => self::rgbToHex($identity->card_background_color, '#ffffff'),
            'text_primary' => self::rgbToHex($identity->text_primary_color, '#000000'),
            'text_secondary' => self::rgbToHex($identity->text_secondary_color, '#6b7280'),
            'text_on_primary' => self::rgbToHex($identity->text_on_primary_color, '#ffffff'),
            'success' => self::rgbToHex($identity->success_color, '#10dc60'),
            'warning' => self::rgbToHex($identity->warning_color, '#ffce00'),
            'danger' => self::rgbToHex($identity->danger_color, '#f04141'),
            'accent' => self::rgbToHex($identity->accent_color, '#8ac34a'),
            'border' => self::rgbToHex($identity->border_color, '#dee2e6'),
        ];
    }

    /**
     * Get light mode color palette array for display
     */
    public static function getColorPalette($identity): array
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
     * Get dark mode color palette array for display
     */
    public static function getDarkColorPalette($identity): array
    {
        if (! $identity) {
            return [
                ['name' => 'Primary', 'value' => '#e0572b'],
                ['name' => 'Secondary', 'value' => '#3db4ac'],
                ['name' => 'Background', 'value' => '#1a1a1a'],
                ['name' => 'Card Background', 'value' => '#2d2d2d'],
                ['name' => 'Text Primary', 'value' => '#ffffff'],
                ['name' => 'Text Secondary', 'value' => '#a0a0a0'],
                ['name' => 'Text On Primary', 'value' => '#ffffff'],
                ['name' => 'Success', 'value' => '#0ec254'],
                ['name' => 'Warning', 'value' => '#e6b800'],
                ['name' => 'Danger', 'value' => '#d63939'],
                ['name' => 'Accent', 'value' => '#7ab03e'],
                ['name' => 'Border', 'value' => '#3f3f3f'],
            ];
        }

        return [
            ['name' => 'Primary', 'value' => self::rgbToHex($identity->primary_color_dark, '#e0572b')],
            ['name' => 'Secondary', 'value' => self::rgbToHex($identity->secondary_color_dark, '#3db4ac')],
            ['name' => 'Background', 'value' => self::rgbToHex($identity->background_color_dark, '#1a1a1a')],
            ['name' => 'Card Background', 'value' => self::rgbToHex($identity->card_background_color_dark, '#2d2d2d')],
            ['name' => 'Text Primary', 'value' => self::rgbToHex($identity->text_primary_color_dark, '#ffffff')],
            ['name' => 'Text Secondary', 'value' => self::rgbToHex($identity->text_secondary_color_dark, '#a0a0a0')],
            ['name' => 'Text On Primary', 'value' => self::rgbToHex($identity->text_on_primary_color_dark, '#ffffff')],
            ['name' => 'Success', 'value' => self::rgbToHex($identity->success_color_dark, '#0ec254')],
            ['name' => 'Warning', 'value' => self::rgbToHex($identity->warning_color_dark, '#e6b800')],
            ['name' => 'Danger', 'value' => self::rgbToHex($identity->danger_color_dark, '#d63939')],
            ['name' => 'Accent', 'value' => self::rgbToHex($identity->accent_color_dark, '#7ab03e')],
            ['name' => 'Border', 'value' => self::rgbToHex($identity->border_color_dark, '#3f3f3f')],
        ];
    }
}
