<?php

namespace App\Console\Commands;

use App\Enums\CategoryType;
use App\Models\Angle;
use App\Models\Category;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MovementPattern;
use App\Models\MuscleGroup;
use App\Models\TargetRegion;
use App\Services\MuscleGroupImageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportExercises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exercises:import {--fetch-images : Fetch muscle group images for exercises} {--dry-run : Show what would be imported without actually importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import exercises from PHP array data structure';

    private array $lookups = [];

    /**
     * Execute the console command.
     */
    public function handle(MuscleGroupImageService $muscleGroupImageService): int
    {
        $this->info('Preparing lookup tables...');
        $this->prepareLookups();

        $this->info('Loading exercise data...');
        $exercises = $this->getExerciseData();

        if (empty($exercises)) {
            $this->warn('No exercises found in data array.');

            return Command::SUCCESS;
        }

        $this->info('Found '.count($exercises).' exercises to process.');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No changes will be made to the database.');
            $this->newLine();
        }

        $canFetchImages = $this->option('fetch-images') && $muscleGroupImageService->isConfigured();
        if ($this->option('fetch-images') && ! $muscleGroupImageService->isConfigured()) {
            $this->warn('MuscleGroupImageService is not configured. Skipping image fetching.');
        }

        $total = count($exercises);
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $failed = 0;
        $imagesFetched = 0;
        $imagesSkipped = 0;

        foreach ($exercises as $index => $exerciseData) {
            $current = $index + 1;
            $this->info("Processing exercise {$current}/{$total}: {$exerciseData['name']}");

            try {
                // Validate required fields
                if (! $this->validateExerciseData($exerciseData)) {
                    $this->warn('  ⚠ Skipped - Missing required fields');
                    $skipped++;
                    $this->newLine();

                    continue;
                }

                // Look up relationships
                $relationships = $this->lookupRelationships($exerciseData);
                if ($relationships === null) {
                    $this->warn('  ⚠ Skipped - Invalid relationships');
                    $skipped++;
                    $this->newLine();

                    continue;
                }

                // Handle muscle group image
                $muscleGroupImagePath = null;
                if ($canFetchImages && isset($exerciseData['primary']) && isset($exerciseData['secondary'])) {
                    $primaryMuscles = $exerciseData['primary'];
                    $secondaryMuscles = $exerciseData['secondary'] ?? [];

                    // Check if image already exists
                    if ($muscleGroupImageService->imageExists($primaryMuscles, $secondaryMuscles)) {
                        $muscleGroupImagePath = $muscleGroupImageService->getImagePath($primaryMuscles, $secondaryMuscles);
                        $imagesSkipped++;
                    } else {
                        $muscleGroupImagePath = $muscleGroupImageService->fetchAndStoreMuscleImage(
                            $primaryMuscles,
                            $secondaryMuscles
                        );
                        if ($muscleGroupImagePath !== null) {
                            $imagesFetched++;
                        }
                    }
                }

                if ($this->option('dry-run')) {
                    $this->line("  [DRY RUN] Would create/update: {$exerciseData['name']}");
                    $created++;
                } else {
                    // Create or update exercise
                    $exercise = Exercise::firstOrCreate(
                        ['name' => $exerciseData['name']],
                        [
                            'category_id' => $relationships['category_id'],
                            'movement_pattern_id' => $relationships['movement_pattern_id'],
                            'target_region_id' => $relationships['target_region_id'],
                            'equipment_type_id' => $relationships['equipment_type_id'],
                            'angle_id' => $relationships['angle_id'],
                            'description' => $exerciseData['description'] ?? null,
                            'default_rest_sec' => $exerciseData['default_rest_sec'] ?? 90,
                            'muscle_group_image' => $muscleGroupImagePath,
                        ]
                    );

                    // Update if exercise already existed
                    if (! $exercise->wasRecentlyCreated) {
                        $updateData = [
                            'category_id' => $relationships['category_id'],
                            'movement_pattern_id' => $relationships['movement_pattern_id'],
                            'target_region_id' => $relationships['target_region_id'],
                            'equipment_type_id' => $relationships['equipment_type_id'],
                            'angle_id' => $relationships['angle_id'],
                        ];

                        if (isset($exerciseData['description'])) {
                            $updateData['description'] = $exerciseData['description'];
                        }

                        if (isset($exerciseData['default_rest_sec'])) {
                            $updateData['default_rest_sec'] = $exerciseData['default_rest_sec'];
                        }

                        // Update image if exercise exists but has no image
                        if (empty($exercise->muscle_group_image) && $muscleGroupImagePath !== null) {
                            $updateData['muscle_group_image'] = $muscleGroupImagePath;
                        }

                        $exercise->update($updateData);
                        $updated++;
                        $this->line('  ✓ Updated existing exercise');
                    } else {
                        $created++;
                        $this->line('  ✓ Created new exercise');
                    }

                    // Sync muscle groups
                    if (isset($exerciseData['primary']) || isset($exerciseData['secondary'])) {
                        $muscleGroupAttachments = [];

                        // Add primary muscle groups
                        if (isset($exerciseData['primary']) && is_array($exerciseData['primary'])) {
                            foreach ($exerciseData['primary'] as $muscleName) {
                                if (isset($this->lookups['muscle_groups'][$muscleName])) {
                                    $muscleGroupAttachments[$this->lookups['muscle_groups'][$muscleName]] = ['is_primary' => true];
                                }
                            }
                        }

                        // Add secondary muscle groups
                        if (isset($exerciseData['secondary']) && is_array($exerciseData['secondary'])) {
                            foreach ($exerciseData['secondary'] as $muscleName) {
                                if (isset($this->lookups['muscle_groups'][$muscleName])) {
                                    // If already in array as primary, skip (can't be both)
                                    if (! isset($muscleGroupAttachments[$this->lookups['muscle_groups'][$muscleName]])) {
                                        $muscleGroupAttachments[$this->lookups['muscle_groups'][$muscleName]] = ['is_primary' => false];
                                    }
                                }
                            }
                        }

                        if (! empty($muscleGroupAttachments)) {
                            $exercise->muscleGroups()->sync($muscleGroupAttachments);
                            $this->line('  ✓ Synced muscle groups');
                        }
                    }
                }
            } catch (\Exception $e) {
                $failed++;
                Log::error('Error importing exercise', [
                    'exercise_data' => $exerciseData,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  ✗ Failed - {$e->getMessage()}");
            }

            $this->newLine();
        }

        // Output summary
        $this->outputSummary($created, $updated, $skipped, $failed, $imagesFetched, $imagesSkipped);

        return Command::SUCCESS;
    }

    /**
     * Prepare lookup tables for relationships
     */
    private function prepareLookups(): void
    {
        // Categories by slug
        $this->lookups['categories'] = Category::where('type', CategoryType::Workout)
            ->pluck('id', 'slug')
            ->toArray();

        // Muscle groups by name
        $this->lookups['muscle_groups'] = MuscleGroup::pluck('id', 'name')->toArray();

        // Movement patterns by code
        $this->lookups['movement_patterns'] = MovementPattern::pluck('id', 'code')->toArray();

        // Target regions by code
        $this->lookups['target_regions'] = TargetRegion::pluck('id', 'code')->toArray();

        // Equipment types by code
        $this->lookups['equipment_types'] = EquipmentType::pluck('id', 'code')->toArray();

        // Angles by code
        $this->lookups['angles'] = Angle::pluck('id', 'code')->toArray();
    }

    /**
     * Look up relationship IDs from exercise data
     */
    private function lookupRelationships(array $exerciseData): ?array
    {
        $relationships = [
            'category_id' => null,
            'movement_pattern_id' => null,
            'target_region_id' => null,
            'equipment_type_id' => null,
            'angle_id' => null,
        ];

        // Category (by slug)
        if (isset($exerciseData['category_slug'])) {
            if (! isset($this->lookups['categories'][$exerciseData['category_slug']])) {
                $this->warn("    Category not found: {$exerciseData['category_slug']}");

                return null;
            }
            $relationships['category_id'] = $this->lookups['categories'][$exerciseData['category_slug']];
        }

        // Movement pattern (by code)
        if (isset($exerciseData['movement_pattern'])) {
            if (! isset($this->lookups['movement_patterns'][$exerciseData['movement_pattern']])) {
                $this->warn("    Movement pattern not found: {$exerciseData['movement_pattern']}");

                return null;
            }
            $relationships['movement_pattern_id'] = $this->lookups['movement_patterns'][$exerciseData['movement_pattern']];
        }

        // Target region (by code)
        if (isset($exerciseData['target_region'])) {
            if (! isset($this->lookups['target_regions'][$exerciseData['target_region']])) {
                $this->warn("    Target region not found: {$exerciseData['target_region']}");

                return null;
            }
            $relationships['target_region_id'] = $this->lookups['target_regions'][$exerciseData['target_region']];
        }

        // Equipment type (by code)
        if (isset($exerciseData['equipment_type'])) {
            if (! isset($this->lookups['equipment_types'][$exerciseData['equipment_type']])) {
                $this->warn("    Equipment type not found: {$exerciseData['equipment_type']}");

                return null;
            }
            $relationships['equipment_type_id'] = $this->lookups['equipment_types'][$exerciseData['equipment_type']];
        }

        // Angle (by code, optional)
        if (isset($exerciseData['angle'])) {
            if (! isset($this->lookups['angles'][$exerciseData['angle']])) {
                $this->warn("    Angle not found: {$exerciseData['angle']}");

                return null;
            }
            $relationships['angle_id'] = $this->lookups['angles'][$exerciseData['angle']];
        }

        return $relationships;
    }

    /**
     * Validate exercise data has required fields
     */
    private function validateExerciseData(array $exerciseData): bool
    {
        if (empty($exerciseData['name'])) {
            return false;
        }

        // At least one of these should be present
        if (empty($exerciseData['primary']) && empty($exerciseData['secondary'])) {
            return false;
        }

        return true;
    }

    /**
     * Get exercise data array
     *
     * Modify this method to add your exercises. Each exercise should be an array with:
     *
     * Required fields:
     * - name: string (exercise name)
     * - primary: array (primary muscle group names, e.g., ['Chest', 'Triceps'])
     *
     * Optional fields:
     * - description: string
     * - default_rest_sec: int (default: 90)
     * - category_slug: string (e.g., 'strength-training', 'cardio')
     * - movement_pattern: string (code, e.g., 'PRESS', 'ROW', 'SQUAT')
     * - target_region: string (code, e.g., 'UPPER_PUSH', 'LOWER', 'CORE')
     * - equipment_type: string (code, e.g., 'BARBELL', 'DUMBBELL', 'BODYWEIGHT')
     * - angle: string (code, e.g., 'FLAT', 'INCLINE', 'DECLINE') - optional
     * - secondary: array (secondary muscle group names, e.g., ['Shoulders'])
     *
     * @return array<int, array<string, mixed>>
     */
    private function getExerciseData(): array
    {
        return [
            // Add your exercises here in this format

                [
                    'name' => 'TRX Squat',
                    'description' => 'Assisted squat using TRX straps for balance and control.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Quadriceps'],
                    'secondary' => ['Glutes', 'Hamstrings'],
                    'movement_pattern' => 'SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'TRX Reverse Lunge',
                    'description' => 'Assisted reverse lunge using TRX straps.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Quadriceps'],
                    'secondary' => ['Glutes', 'Hamstrings'],
                    'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'TRX Row',
                    'description' => 'Suspension row with neutral grip.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Upper Back'],
                    'secondary' => ['Lats', 'Biceps', 'Rear Delts'],
                    'movement_pattern' => 'ROW',
                    'target_region' => 'UPPER_PULL',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Wide Row',
                    'description' => 'Suspension row with wider elbow path to emphasize upper back.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Upper Back'],
                    'secondary' => ['Rear Delts', 'Biceps', 'Traps'],
                    'movement_pattern' => 'ROW',
                    'target_region' => 'UPPER_PULL',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Wide Fly',
                    'description' => 'Suspension chest fly emphasizing horizontal adduction.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Chest'],
                    'secondary' => ['Front Delts'],
                    'movement_pattern' => 'FLY',
                    'target_region' => 'UPPER_PUSH',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX T Fly',
                    'description' => 'Suspension rear delt fly in a T position.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Rear Delts'],
                    'secondary' => ['Upper Back', 'Traps'],
                    'movement_pattern' => 'REAR_DELT_FLY',
                    'target_region' => 'UPPER_PULL',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Biceps Curl',
                    'description' => 'Suspension curl emphasizing elbow flexion.',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Biceps'],
                    'secondary' => ['Forearms'],
                    'movement_pattern' => 'ELBOW_FLEXION',
                    'target_region' => 'ARMS',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Triceps Extension',
                    'description' => 'Suspension triceps extension (skull crusher style).',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Triceps'],
                    'secondary' => ['Front Delts'],
                    'movement_pattern' => 'ELBOW_EXTENSION',
                    'target_region' => 'ARMS',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Push-Up',
                    'description' => 'Push-up with hands or feet suspended in TRX straps.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Chest'],
                    'secondary' => ['Triceps', 'Front Delts', 'Abs'],
                    'movement_pattern' => 'PUSHUP',
                    'target_region' => 'UPPER_PUSH',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Pec Fly',
                    'description' => 'Suspension chest fly (pec fly).',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Chest'],
                    'secondary' => ['Front Delts'],
                    'movement_pattern' => 'FLY',
                    'target_region' => 'UPPER_PUSH',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Squat to Wide Fly',
                    'description' => 'Combination: assisted squat then wide fly for full-body control.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Quadriceps'],
                    'secondary' => ['Glutes', 'Chest', 'Front Delts', 'Abs'],
                    'movement_pattern' => 'SQUAT',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'TRX',
                    'angle' => 'VERTICAL',
                ],

                /* ---------------- CORE ---------------- */

                [
                    'name' => 'TRX Pike',
                    'description' => 'Feet suspended; pike hips up from plank.',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Obliques', 'Front Delts'],
                    'movement_pattern' => 'TRUNK_FLEXION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HIGH_TO_LOW',
                ],

                [
                    'name' => 'TRX Knee Tuck',
                    'description' => 'Feet suspended; tuck knees toward chest from plank.',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Front Delts'],
                    'movement_pattern' => 'TRUNK_FLEXION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HIGH_TO_LOW',
                ],

                [
                    'name' => 'TRX Mountain Climber',
                    'description' => 'Feet suspended; alternating knee drives.',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Quadriceps', 'Front Delts'],
                    'movement_pattern' => 'TRUNK_FLEXION',
                    'target_region' => 'CONDITIONING',
                    'equipment_type' => 'TRX',
                    'angle' => 'HIGH_TO_LOW',
                ],

                [
                    'name' => 'TRX Plank',
                    'description' => 'Forearm plank with feet suspended.',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Obliques', 'Front Delts'],
                    'movement_pattern' => 'ANTI_ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Body Saw',
                    'description' => 'Forearm plank with forward/back rocking motion.',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Front Delts', 'Lats'],
                    'movement_pattern' => 'ANTI_ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Atomic Push-Up',
                    'description' => 'TRX push-up combined with knee tuck.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Chest'],
                    'secondary' => ['Triceps', 'Abs', 'Front Delts'],
                    'movement_pattern' => 'PUSHUP',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Rollout',
                    'description' => 'Standing or kneeling rollout using TRX straps.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Lats', 'Front Delts'],
                    'movement_pattern' => 'ANTI_ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HIGH_TO_LOW',
                ],

                [
                    'name' => 'TRX Bulgarian Split Squat',
                    'description' => 'Assisted Bulgarian split squat using TRX straps.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Quadriceps'],
                    'secondary' => ['Glutes', 'Hamstrings'],
                    'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'TRX Hamstring Curl',
                    'description' => 'Supine; heels in straps, curl heels toward glutes.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Hamstrings'],
                    'secondary' => ['Glutes', 'Abs'],
                    'movement_pattern' => 'KNEE_FLEXION',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Hamstring Bridge',
                    'description' => 'Supine bridge with heels in straps.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Hamstrings', 'Lower Back'],
                    'movement_pattern' => 'HIP_THRUST_BRIDGE',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Fallout',
                    'description' => 'Anti-extension core drill leaning forward with straps.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Abs'],
                    'secondary' => ['Lats', 'Front Delts'],
                    'movement_pattern' => 'ANTI_ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HIGH_TO_LOW',
                ],

                [
                    'name' => 'TRX Reverse Lunge to Knee Drive',
                    'description' => 'Reverse lunge followed by explosive knee drive.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Quadriceps', 'Hamstrings', 'Abs'],
                    'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'TRX Side Plank',
                    'description' => 'Side plank variation using TRX straps (feet suspended).',
                    'default_rest_sec' => 45,
                    'category_slug' => 'functional-training',
                    'primary' => ['Obliques'],
                    'secondary' => ['Abs', 'Glutes'],
                    'movement_pattern' => 'ANTI_ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'TRX',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'TRX Pistol Squat',
                    'description' => 'Assisted single-leg squat using TRX straps.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Quadriceps'],
                    'secondary' => ['Glutes', 'Hamstrings', 'Abs'],
                    'movement_pattern' => 'SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'TRX',
                    'angle' => 'VERTICAL',
                ],


        ];
    }

    /**
     * Output summary statistics
     */
    private function outputSummary(int $created, int $updated, int $skipped, int $failed, int $imagesFetched, int $imagesSkipped): void
    {
        $this->newLine();
        $this->info('Summary:');
        $this->line("  Created: {$created}");
        $this->line("  Updated: {$updated}");
        $this->line("  Skipped: {$skipped}");
        $this->line("  Failed: {$failed}");

        if ($this->option('fetch-images')) {
            $this->line("  Images fetched: {$imagesFetched}");
            $this->line("  Images skipped (already exist): {$imagesSkipped}");
        }

        if ($this->option('dry-run')) {
            $this->warn('  Mode: DRY RUN (no changes made)');
        }
    }
}
