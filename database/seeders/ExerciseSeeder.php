<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use App\Services\MuscleGroupImageService;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function __construct(
        private MuscleGroupImageService $muscleImageService
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories indexed by slug for easy lookup
        $categories = Category::pluck('id', 'slug');

        // Get all muscle groups indexed by name for easy lookup
        $muscleGroups = MuscleGroup::pluck('id', 'name');

        // Exercise data with category and muscle group mappings
        // Format: name, category_slug, default_rest_sec, primary_muscles[], secondary_muscles[]
        $exercises = [
            // Chest exercises
            [
                'name' => 'Barbell Bench Press',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
            ],
            [
                'name' => 'Dumbbell Bench Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
            ],
            [
                'name' => 'Incline Barbell Bench Press',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
            ],
            [
                'name' => 'Incline Dumbbell Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Chest', 'Front Delts'],
                'secondary' => ['Triceps'],
            ],
            [
                'name' => 'Decline Bench Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps'],
            ],
            [
                'name' => 'Dumbbell Flyes',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
            ],
            [
                'name' => 'Cable Flyes',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
            ],
            [
                'name' => 'Cable Chest Flyes',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => [],
            ],
            [
                'name' => 'Push-ups',
                'category' => 'compound',
                'default_rest_sec' => 60,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts', 'Abs'],
            ],
            [
                'name' => 'Dips (Chest)',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Chest'],
                'secondary' => ['Triceps', 'Front Delts'],
            ],

            // Back exercises
            [
                'name' => 'Deadlift',
                'category' => 'compound',
                'default_rest_sec' => 180,
                'primary' => ['Lower Back', 'Glutes', 'Hamstrings'],
                'secondary' => ['Lats', 'Traps', 'Quadriceps', 'Forearms'],
            ],
            [
                'name' => 'Barbell Row',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts', 'Lower Back'],
            ],
            [
                'name' => 'Dumbbell Row',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts'],
            ],
            [
                'name' => 'Chest-Supported Dumbbell Row',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts'],
            ],
            [
                'name' => 'Pull-ups',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back', 'Abs'],
            ],
            [
                'name' => 'Lat Pulldown',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Lats'],
                'secondary' => ['Biceps', 'Upper Back'],
            ],
            [
                'name' => 'Seated Cable Row',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Rear Delts'],
            ],
            [
                'name' => 'T-Bar Row',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Lower Back'],
            ],
            [
                'name' => 'Face Pulls',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Rear Delts', 'Upper Back'],
                'secondary' => ['Traps'],
            ],
            [
                'name' => 'Reverse Cable Flyes',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
            ],
            [
                'name' => 'Hyperextensions',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Lower Back'],
                'secondary' => ['Glutes', 'Hamstrings'],
            ],

            // Leg exercises
            [
                'name' => 'Barbell Squat',
                'category' => 'compound',
                'default_rest_sec' => 180,
                'primary' => ['Quadriceps', 'Glutes'],
                'secondary' => ['Hamstrings', 'Lower Back', 'Abs'],
            ],
            [
                'name' => 'Front Squat',
                'category' => 'compound',
                'default_rest_sec' => 150,
                'primary' => ['Quadriceps'],
                'secondary' => ['Glutes', 'Abs'],
            ],
            [
                'name' => 'Leg Press',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Quadriceps', 'Glutes'],
                'secondary' => ['Hamstrings'],
            ],
            [
                'name' => 'Romanian Deadlift',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Hamstrings', 'Glutes'],
                'secondary' => ['Lower Back'],
            ],
            [
                'name' => 'Leg Curl',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Hamstrings'],
                'secondary' => [],
            ],
            [
                'name' => 'Leg Extension',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Quadriceps'],
                'secondary' => [],
            ],
            [
                'name' => 'Walking Lunges',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Quadriceps', 'Glutes'],
                'secondary' => ['Hamstrings', 'Abs'],
            ],
            [
                'name' => 'Bulgarian Split Squat',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Quadriceps', 'Glutes'],
                'secondary' => ['Hamstrings'],
            ],
            [
                'name' => 'Barbell Hip Thrust',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings'],
            ],
            [
                'name' => 'Calf Raises',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Calves'],
                'secondary' => [],
            ],
            [
                'name' => 'Standing Calf Raises',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Calves'],
                'secondary' => [],
            ],

            // Shoulder exercises
            [
                'name' => 'Overhead Press',
                'category' => 'compound',
                'default_rest_sec' => 120,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps', 'Upper Back'],
            ],
            [
                'name' => 'Dumbbell Shoulder Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps'],
            ],
            [
                'name' => 'Seated Dumbbell Shoulder Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps'],
            ],
            [
                'name' => 'Lateral Raises',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => [],
            ],
            [
                'name' => 'Cable Lateral Raises',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Side Delts'],
                'secondary' => [],
            ],
            [
                'name' => 'Front Raises',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Front Delts'],
                'secondary' => [],
            ],
            [
                'name' => 'Rear Delt Flyes',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Rear Delts'],
                'secondary' => ['Upper Back'],
            ],
            [
                'name' => 'Arnold Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Front Delts', 'Side Delts'],
                'secondary' => ['Triceps'],
            ],
            [
                'name' => 'Upright Row',
                'category' => 'compound',
                'default_rest_sec' => 60,
                'primary' => ['Side Delts', 'Traps'],
                'secondary' => ['Biceps'],
            ],
            [
                'name' => 'Shrugs',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Traps'],
                'secondary' => [],
            ],

            // Arm exercises
            [
                'name' => 'Barbell Curl',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
            ],
            [
                'name' => 'Dumbbell Curl',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
            ],
            [
                'name' => 'Incline Dumbbell Curls',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => [],
            ],
            [
                'name' => 'Hammer Curl',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Biceps', 'Forearms'],
                'secondary' => [],
            ],
            [
                'name' => 'Preacher Curl',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => [],
            ],
            [
                'name' => 'Cable Curl',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Biceps'],
                'secondary' => [],
            ],
            [
                'name' => 'Close-Grip Bench Press',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Triceps'],
                'secondary' => ['Chest', 'Front Delts'],
            ],
            [
                'name' => 'Tricep Dips',
                'category' => 'compound',
                'default_rest_sec' => 90,
                'primary' => ['Triceps'],
                'secondary' => ['Chest', 'Front Delts'],
            ],
            [
                'name' => 'Overhead Tricep Extension',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
            ],
            [
                'name' => 'Tricep Pushdown',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
            ],
            [
                'name' => 'Skull Crushers',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Triceps'],
                'secondary' => [],
            ],

            // Core exercises
            [
                'name' => 'Plank',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Abs'],
                'secondary' => ['Obliques'],
            ],
            [
                'name' => 'Crunches',
                'category' => 'isolation',
                'default_rest_sec' => 45,
                'primary' => ['Abs'],
                'secondary' => [],
            ],
            [
                'name' => 'Reverse Crunches',
                'category' => 'isolation',
                'default_rest_sec' => 45,
                'primary' => ['Abs'],
                'secondary' => [],
            ],
            [
                'name' => 'Hanging Leg Raises',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Abs'],
                'secondary' => ['Obliques'],
            ],
            [
                'name' => 'Russian Twists',
                'category' => 'isolation',
                'default_rest_sec' => 45,
                'primary' => ['Obliques'],
                'secondary' => ['Abs'],
            ],
            [
                'name' => 'Cable Crunches',
                'category' => 'isolation',
                'default_rest_sec' => 60,
                'primary' => ['Abs'],
                'secondary' => [],
            ],
            [
                'name' => 'Ab Wheel Rollout',
                'category' => 'compound',
                'default_rest_sec' => 60,
                'primary' => ['Abs'],
                'secondary' => ['Lats', 'Front Delts'],
            ],

            // Cardio exercises
            [
                'name' => 'Treadmill Running',
                'category' => 'cardio',
                'default_rest_sec' => null,
                'primary' => ['Quadriceps', 'Hamstrings', 'Calves'],
                'secondary' => ['Glutes', 'Abs'],
            ],
            [
                'name' => 'Cycling',
                'category' => 'cardio',
                'default_rest_sec' => null,
                'primary' => ['Quadriceps'],
                'secondary' => ['Hamstrings', 'Glutes', 'Calves'],
            ],
            [
                'name' => 'Rowing Machine',
                'category' => 'cardio',
                'default_rest_sec' => null,
                'primary' => ['Lats', 'Upper Back'],
                'secondary' => ['Biceps', 'Quadriceps', 'Abs'],
            ],
            [
                'name' => 'Jump Rope',
                'category' => 'cardio',
                'default_rest_sec' => 60,
                'primary' => ['Calves'],
                'secondary' => ['Quadriceps', 'Abs'],
            ],
            [
                'name' => 'Burpees',
                'category' => 'plyometrics',
                'default_rest_sec' => 60,
                'primary' => ['Quadriceps', 'Chest'],
                'secondary' => ['Abs', 'Triceps', 'Front Delts'],
            ],
        ];

        // Check if muscle image service is configured
        $canFetchImages = $this->muscleImageService->isConfigured();

        if (! $canFetchImages) {
            $this->command->warn('RAPIDAPI_KEY not configured - muscle group images will not be fetched.');
        }

        $imagesFetched = 0;
        $imagesSkipped = 0;

        foreach ($exercises as $exerciseData) {
            $imageUrl = null;

            // Fetch muscle group image if service is configured
            if ($canFetchImages) {
                $primaryMuscles = $exerciseData['primary'];
                $secondaryMuscles = $exerciseData['secondary'];

                // Check if we already have this image (to avoid duplicate API calls)
                if ($this->muscleImageService->imageExists($primaryMuscles, $secondaryMuscles)) {
                    $imagePath = $this->muscleImageService->getImagePath($primaryMuscles, $secondaryMuscles);
                    $imageUrl = $this->muscleImageService->getImageUrl($imagePath);
                    $imagesSkipped++;
                } else {
                    $imagePath = $this->muscleImageService->fetchAndStoreMuscleImage(
                        $primaryMuscles,
                        $secondaryMuscles
                    );

                    if ($imagePath !== null) {
                        $imageUrl = $this->muscleImageService->getImageUrl($imagePath);
                        $imagesFetched++;
                        $this->command->info("Fetched image for: {$exerciseData['name']}");
                    }
                }
            }

            $exercise = Exercise::firstOrCreate(
                [
                    'name' => $exerciseData['name'],
                    'user_id' => null,
                ],
                [
                    'category_id' => $categories[$exerciseData['category']] ?? null,
                    'default_rest_sec' => $exerciseData['default_rest_sec'],
                    'image_url' => $imageUrl,
                ]
            );

            // Update image_url if exercise exists but has no image
            if (! $exercise->wasRecentlyCreated && empty($exercise->image_url) && $imageUrl !== null) {
                $exercise->update(['image_url' => $imageUrl]);
            }

            // Attach muscle groups if the exercise was newly created or has no muscle groups
            if ($exercise->wasRecentlyCreated || $exercise->muscleGroups()->count() === 0) {
                $muscleGroupAttachments = [];

                // Add primary muscle groups
                foreach ($exerciseData['primary'] as $muscleName) {
                    if (isset($muscleGroups[$muscleName])) {
                        $muscleGroupAttachments[$muscleGroups[$muscleName]] = ['is_primary' => true];
                    }
                }

                // Add secondary muscle groups
                foreach ($exerciseData['secondary'] as $muscleName) {
                    if (isset($muscleGroups[$muscleName])) {
                        $muscleGroupAttachments[$muscleGroups[$muscleName]] = ['is_primary' => false];
                    }
                }

                $exercise->muscleGroups()->sync($muscleGroupAttachments);
            }
        }

        $this->command->info('Global exercises seeded successfully with muscle groups!');

        if ($canFetchImages) {
            $this->command->info("Muscle images: {$imagesFetched} fetched, {$imagesSkipped} already existed.");
        }
    }
}
