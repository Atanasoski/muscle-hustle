<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all partners
        $partners = Partner::all();

        // Get all exercises
        $defaultExercises = Exercise::all();

        // Link all default exercises to all partners
        foreach ($partners as $partner) {
            $pivotData = [];
            foreach ($defaultExercises as $exercise) {
                $pivotData[$exercise->id] = [
                    'description' => null,
                    'image' => null,
                    'video' => null,
                ];
            }

            // Sync without detaching to avoid removing existing customizations
            $partner->exercises()->syncWithoutDetaching($pivotData);
        }

        $this->command->info('Linked '.$defaultExercises->count().' exercises to '.$partners->count().' partners.');
    }
}
