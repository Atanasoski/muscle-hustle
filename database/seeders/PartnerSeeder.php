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
            'primary_color' => '#ff6b35',
            'secondary_color' => '#4ecdc4',
            'logo' => '/images/muscle-hustle-logo.png',
            'font_family' => 'Inter',
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
        ]);
    }
}
