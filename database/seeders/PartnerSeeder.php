<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'primary_color_dark' => '#fa812d',
            'secondary_color_dark' => '#292a2c',
            'background_color_dark' => '#121212',
            'card_background_color_dark' => '#1e1e1e',
            'text_primary_color_dark' => '#ffffff',
            'text_secondary_color_dark' => '#b0b0b0',
            'text_on_primary_color_dark' => '#ffffff',
            'success_color_dark' => '#4ade80',
            'warning_color_dark' => '#fff94f',
            'danger_color_dark' => '#ff6b6b',
            'accent_color_dark' => '#fff94f',
            'border_color_dark' => '#3a3a3a',
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
            'primary_color_dark' => '#60a5fa',
            'secondary_color_dark' => '#34d399',
            'background_color_dark' => '#0f172a',
            'card_background_color_dark' => '#1e293b',
            'text_primary_color_dark' => '#f1f5f9',
            'text_secondary_color_dark' => '#94a3b8',
            'text_on_primary_color_dark' => '#ffffff',
            'success_color_dark' => '#4ade80',
            'warning_color_dark' => '#fbbf24',
            'danger_color_dark' => '#f87171',
            'accent_color_dark' => '#a78bfa',
            'border_color_dark' => '#334155',
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
            'primary_color_dark' => '#a78bfa',
            'secondary_color_dark' => '#f472b6',
            'background_color_dark' => '#0f172a',
            'card_background_color_dark' => '#1e293b',
            'text_primary_color_dark' => '#f1f5f9',
            'text_secondary_color_dark' => '#94a3b8',
            'text_on_primary_color_dark' => '#ffffff',
            'success_color_dark' => '#4ade80',
            'warning_color_dark' => '#fbbf24',
            'danger_color_dark' => '#f87171',
            'accent_color_dark' => '#c084fc',
            'border_color_dark' => '#334155',
        ]);

        // Create partner admin users
        $partnerAdminRole = Role::where('slug', 'partner_admin')->first();

        $muscleHustleAdmin = User::firstOrCreate(
            ['email' => 'admin@musclehustle.gym'],
            [
                'name' => 'Muscle Hustle Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'partner_id' => $muscleHustle->id,
            ]
        );
        $muscleHustleAdmin->roles()->syncWithoutDetaching($partnerAdminRole);

        $fitLifeAdmin = User::firstOrCreate(
            ['email' => 'admin@fitlife.gym'],
            [
                'name' => 'FitLife Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'partner_id' => $fitLife->id,
            ]
        );
        $fitLifeAdmin->roles()->syncWithoutDetaching($partnerAdminRole);

        $powerGymAdmin = User::firstOrCreate(
            ['email' => 'admin@powergym.gym'],
            [
                'name' => 'PowerGym Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'partner_id' => $powerGym->id,
            ]
        );
        $powerGymAdmin->roles()->syncWithoutDetaching($partnerAdminRole);

        // Synergy Fitness Center
        $synergyFitness = Partner::create([
            'name' => 'Synergy Fitness Center',
            'slug' => 'synergy-fitness-center',
            'domain' => 'synergy.example.com',
            'is_active' => true,
        ]);

        $synergyFitness->identity()->create([
            'primary_color' => '#089e5a', // green
            'secondary_color' => '#efcd3f', // yellow/gold
            'logo' => '/images/partners/synergy-logo.png',
            'font_family' => 'Inter',
            'background_color' => '#ffffff',
            'card_background_color' => '#f9fafb',
            'text_primary_color' => '#111827',
            'text_secondary_color' => '#089e5a', // green
            'text_on_primary_color' => '#ffffff',
            'success_color' => '#089e5a', // green
            'warning_color' => '#efcd3f', // yellow/gold
            'danger_color' => '#ef4444',
            'accent_color' => '#089e5a', // green
            'border_color' => '#d1fae5', // light green variant
            'background_pattern' => '/images/pattern.png',
            'primary_color_dark' => '#10b981', // lighter green for dark mode
            'secondary_color_dark' => '#fbbf24', // lighter yellow for dark mode
            'background_color_dark' => '#0f172a',
            'card_background_color_dark' => '#1e293b',
            'text_primary_color_dark' => '#f1f5f9',
            'text_secondary_color_dark' => '#6ee7b7', // light green
            'text_on_primary_color_dark' => '#ffffff',
            'success_color_dark' => '#34d399',
            'warning_color_dark' => '#fbbf24',
            'danger_color_dark' => '#f87171',
            'accent_color_dark' => '#34d399',
            'border_color_dark' => '#1e3a2f', // dark green variant
        ]);

        // Premium Sport Center
        $premiumSport = Partner::create([
            'name' => 'Premium Sport Center',
            'slug' => 'premium-sport-center',
            'domain' => 'premium.example.com',
            'is_active' => true,
        ]);

        $premiumSport->identity()->create([
            'primary_color' => '#053a7b', // dark blue
            'secondary_color' => '#fbf004', // bright yellow
            'logo' => '/images/partners/premium-logo.png',
            'font_family' => 'Poppins',
            'background_color' => '#ffffff',
            'card_background_color' => '#f8fafc',
            'text_primary_color' => '#0f172a',
            'text_secondary_color' => '#053a7b', // dark blue
            'text_on_primary_color' => '#ffffff',
            'success_color' => '#10b981',
            'warning_color' => '#fbf004', // bright yellow
            'danger_color' => '#ef4444',
            'accent_color' => '#3b82f6', // lighter blue variant
            'border_color' => '#bfdbfe', // light blue variant
            'background_pattern' => '/images/pattern.png',
            'primary_color_dark' => '#3b82f6', // lighter blue for dark mode
            'secondary_color_dark' => '#fbbf24', // lighter yellow for dark mode
            'background_color_dark' => '#0f172a',
            'card_background_color_dark' => '#1e293b',
            'text_primary_color_dark' => '#f1f5f9',
            'text_secondary_color_dark' => '#93c5fd', // light blue
            'text_on_primary_color_dark' => '#ffffff',
            'success_color_dark' => '#4ade80',
            'warning_color_dark' => '#fbbf24',
            'danger_color_dark' => '#f87171',
            'accent_color_dark' => '#60a5fa',
            'border_color_dark' => '#1e3a5f', // dark blue variant
        ]);

        $synergyFitnessAdmin = User::firstOrCreate(
            ['email' => 'admin@synergy.gym'],
            [
                'name' => 'Synergy Fitness Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'partner_id' => $synergyFitness->id,
            ]
        );
        $synergyFitnessAdmin->roles()->syncWithoutDetaching($partnerAdminRole);

        $premiumSportAdmin = User::firstOrCreate(
            ['email' => 'admin@premium.gym'],
            [
                'name' => 'Premium Sport Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'partner_id' => $premiumSport->id,
            ]
        );
        $premiumSportAdmin->roles()->syncWithoutDetaching($partnerAdminRole);

        $this->command->info('Partner admin users created:');
        $this->command->info('  - admin@musclehustle.gym (password: password)');
        $this->command->info('  - admin@fitlife.gym (password: password)');
        $this->command->info('  - admin@powergym.gym (password: password)');
        $this->command->info('  - admin@synergy.gym (password: password)');
        $this->command->info('  - admin@premium.gym (password: password)');
    }
}
