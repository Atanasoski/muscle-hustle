<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Light Mode Colors
    |--------------------------------------------------------------------------
    |
    | These are the default colors used for partner branding when no custom
    | colors are specified. All colors should be in hex format (#RRGGBB).
    |
    */
    'light' => [
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
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Dark Mode Colors
    |--------------------------------------------------------------------------
    |
    | These are the default dark mode colors used for partner branding when
    | no custom colors are specified.
    |
    */
    'dark' => [
        'primary' => '#e0572b',
        'secondary' => '#3db4ac',
        'background' => '#1a1a1a',
        'card_background' => '#2d2d2d',
        'text_primary' => '#ffffff',
        'text_secondary' => '#a0a0a0',
        'text_on_primary' => '#ffffff',
        'success' => '#0ec254',
        'warning' => '#e6b800',
        'danger' => '#d63939',
        'accent' => '#7ab03e',
        'border' => '#3f3f3f',
    ],

    /*
    |--------------------------------------------------------------------------
    | Identity Fields
    |--------------------------------------------------------------------------
    |
    | List of all identity fields that can be customized for partners.
    | Used for validation and data extraction.
    |
    */
    'identity_fields' => [
        'primary_color',
        'secondary_color',
        'font_family',
        'background_color',
        'card_background_color',
        'text_primary_color',
        'text_secondary_color',
        'text_on_primary_color',
        'success_color',
        'warning_color',
        'danger_color',
        'accent_color',
        'border_color',
        'background_pattern',
        'primary_color_dark',
        'secondary_color_dark',
        'background_color_dark',
        'card_background_color_dark',
        'text_primary_color_dark',
        'text_secondary_color_dark',
        'text_on_primary_color_dark',
        'success_color_dark',
        'warning_color_dark',
        'danger_color_dark',
        'accent_color_dark',
        'border_color_dark',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for logo and background pattern uploads.
    |
    */
    'uploads' => [
        'max_size' => 2048, // KB
        'allowed_mimes' => ['jpeg', 'jpg', 'png', 'gif', 'svg'],
        'storage_disk' => 'public',
        'storage_path' => 'partners',
    ],
];
