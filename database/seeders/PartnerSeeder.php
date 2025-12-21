<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Partner - Muscle Hustle
        $muscleHustle = Partner::create([
            'name' => 'Muscle Hustle',
            'slug' => 'muscle-hustle',
            'domain' => null, // Default domain
            'is_active' => true,
        ]);

        $muscleHustle->identity()->create([
            'primary_color' => '#fb363e', // strawberry-red
            'secondary_color' => '#129490', // dark-cyan
            'logo' => '/images/partner-logo.png',
            'font_family' => 'Inter',
            'background_color' => '#fbfffe', // white
            'card_background_color' => '#fbfffe', // white
            'text_primary_color' => '#000000', // black
            'text_secondary_color' => '#129490', // dark-cyan
            'text_on_primary_color' => '#fbfffe', // white
            'success_color' => '#70b77e', // emerald
            'warning_color' => '#ffb400', // warm orange (strawberry-red + yellow mix)
            'danger_color' => '#fb363e', // strawberry-red
            'accent_color' => '#70b77e', // emerald
            'border_color' => '#c8dcda', // light dark-cyan variant
            'background_pattern' => '/images/pattern.png',
        ]);

        // Example Partner 1 - FitLife Pro
        $fitLife = Partner::create([
            'name' => 'FitLife Pro',
            'slug' => 'fitlife-pro',
            'domain' => 'fitlife.example.com',
            'is_active' => true,
        ]);

        $fitLife->identity()->create([
            'primary_color' => '#3b82f6', // Blue
            'secondary_color' => '#10b981', // Green
            'logo' => '/images/partners/fitlife-logo.png',
            'font_family' => 'Poppins',
            'background_color' => '#ffffff',
            'card_background_color' => '#f9fafb',
            'text_primary_color' => '#111827',
            'text_secondary_color' => '#6b7280',
            'text_on_primary_color' => '#ffffff',
            'success_color' => '#10b981',
            'warning_color' => '#f59e0b',
            'danger_color' => '#ef4444',
            'accent_color' => '#8b5cf6',
            'border_color' => '#e5e7eb',
        ]);

        // Example Partner 2 - PowerGym Elite
        $powerGym = Partner::create([
            'name' => 'PowerGym Elite',
            'slug' => 'powergym-elite',
            'domain' => 'powergym.example.com',
            'is_active' => true,
        ]);

        $powerGym->identity()->create([
            'primary_color' => '#8b5cf6', // Purple
            'secondary_color' => '#ec4899', // Pink
            'logo' => '/images/partners/powergym-logo.png',
            'font_family' => 'Montserrat',
            'background_color' => '#ffffff',
            'card_background_color' => '#fafafa',
            'text_primary_color' => '#0f172a',
            'text_secondary_color' => '#64748b',
            'text_on_primary_color' => '#ffffff',
            'success_color' => '#22c55e',
            'warning_color' => '#fbbf24',
            'danger_color' => '#f87171',
            'accent_color' => '#a855f7',
            'border_color' => '#e2e8f0',
        ]);
    }
}
