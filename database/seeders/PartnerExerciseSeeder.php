<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Partner;
use App\Services\PartnerExerciseFileService;
use Illuminate\Database\Seeder;

class PartnerExerciseSeeder extends Seeder
{
    public function __construct(
        private PartnerExerciseFileService $fileService
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all partners
        $partners = Partner::all();

        // Get all exercises
        $defaultExercises = Exercise::all();

        // Map user's exercise names to actual exercise names in database for Premium Sport Center
        $premiumExerciseNames = [
            'Wide-Grip Lat Pulldown',
            'Underhand Close-Grip Lat Pulldown',
            'Behind-the-Head Lat Pulldown',
            'Close-Grip Seated Cable Row',
            'Cable Fly',
            'Reverse Cable Flyes',
            'Reverse-Grip Triceps Pushdown',
            'Triceps Pushdown (Cable)',
            'Rope Triceps Pushdown',
            'Smith Machine Squat',
            'Smith Machine Split Squat',
            'Dumbbell Shoulder Press',
            'Bent-Over Cable Rear Delt Fly',
            'Cable Curl',
            'Face Pulls',
            'Straight-Arm Cable Pulldown',
            'Leg Extensions',
            'Lying Leg Curl',
            'Knee Raises',
            'Straight Leg Raises',
            'Dips (Chest)',
            'Wide-Grip Pull-ups',
            'Close-Grip Pull-ups',
            'Hyperextensions',
            'Trap Bar Deadlift',
            'Barbell Bench Press',
            'Close-Grip Bench Press (Chest Focus)',
            'Skull Crushers (EZ-Bar)',
            'Dumbbell Skull Crushers',
            'Alternating Dumbbell Curl',
            'Incline Barbell Bench Press',
            'Dumbbell Bench Press',
            'Incline Dumbbell Bench Press',
            'Dumbbell Rear Delt Flyes',
            'Dumbbell Fly',
            'Dumbbell Row',
        ];

        // Link exercises to partners
        foreach ($partners as $partner) {
            $exercisesToLink = [];

            if ($partner->slug === 'premium-sport-center') {
                // For Premium Sport Center, only add specific exercises
                $exercisesToLink = Exercise::whereIn('name', $premiumExerciseNames)->get();
                $this->command->info('Linking '.$exercisesToLink->count().' specific exercises to Premium Sport Center.');
            } else {
                // For all other partners, link all default exercises
                $exercisesToLink = $defaultExercises;
            }

            $pivotData = [];
            foreach ($exercisesToLink as $exercise) {
                $imagePath = null;
                $videoPath = null;

                // Check if files exist from previous runs (preserved after migrate fresh)
                if ($this->fileService->imageExists($partner, $exercise)) {
                    // Find the actual file path (handles different extensions)
                    $imagePath = $this->fileService->getExistingImagePath($partner, $exercise);
                }

                if ($this->fileService->videoExists($partner, $exercise)) {
                    // Find the actual file path (handles different extensions)
                    $videoPath = $this->fileService->getExistingVideoPath($partner, $exercise);
                }

                $pivotData[$exercise->id] = [
                    'description' => null,
                    'image' => $imagePath,
                    'video' => $videoPath,
                ];
            }

            // Sync without detaching to avoid removing existing customizations
            $partner->exercises()->syncWithoutDetaching($pivotData);
        }

        $this->command->info('Linked exercises to all partners.');
    }
}
