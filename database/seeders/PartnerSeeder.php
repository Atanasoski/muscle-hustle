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
            'primary_color' => '251,54,64', // strawberry-red
            'secondary_color' => '18,148,144', // dark-cyan
            'logo' => '/images/partner-logo.png',
            'font_family' => 'Inter',
            'background_color' => '251,255,254', // white
            'card_background_color' => '251,255,254', // white
            'text_primary_color' => '0,0,0', // black
            'text_secondary_color' => '18,148,144', // dark-cyan
            'text_on_primary_color' => '251,255,254', // white
            'success_color' => '112,183,126', // emerald
            'warning_color' => '255,180,0', // warm orange (strawberry-red + yellow mix)
            'danger_color' => '251,54,64', // strawberry-red
            'accent_color' => '112,183,126', // emerald
            'border_color' => '200,220,218', // light dark-cyan variant
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
            'primary_color' => '59,130,246', // Blue
            'secondary_color' => '16,185,129', // Green
            'logo' => '/images/partners/fitlife-logo.png',
            'font_family' => 'Poppins',
            'background_color' => '255,255,255',
            'card_background_color' => '249,250,251',
            'text_primary_color' => '17,24,39',
            'text_secondary_color' => '107,114,128',
            'text_on_primary_color' => '255,255,255',
            'success_color' => '16,185,129',
            'warning_color' => '245,158,11',
            'danger_color' => '239,68,68',
            'accent_color' => '139,92,246',
            'border_color' => '229,231,235',
        ]);

        // Example Partner 2 - PowerGym Elite
        $powerGym = Partner::create([
            'name' => 'PowerGym Elite',
            'slug' => 'powergym-elite',
            'domain' => 'powergym.example.com',
            'is_active' => true,
        ]);

        $powerGym->identity()->create([
            'primary_color' => '139,92,246', // Purple
            'secondary_color' => '236,72,153', // Pink
            'logo' => '/images/partners/powergym-logo.png',
            'font_family' => 'Montserrat',
            'background_color' => '255,255,255',
            'card_background_color' => '250,250,250',
            'text_primary_color' => '15,23,42',
            'text_secondary_color' => '100,116,139',
            'text_on_primary_color' => '255,255,255',
            'success_color' => '34,197,94',
            'warning_color' => '251,191,36',
            'danger_color' => '248,113,113',
            'accent_color' => '168,85,247',
            'border_color' => '226,232,240',
        ]);
    }
}
