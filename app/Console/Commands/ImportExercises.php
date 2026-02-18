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
                'name' => 'Cossack Lunge',
                'description' => 'Side-to-side deep lateral lunge.',
                'default_rest_sec' => 60,
                'category_slug' => 'functional-training',
                'primary' => ['Glutes'],
                'secondary' => ['Quadriceps', 'Adductors'],
                'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                'target_region' => 'LOWER',
                'equipment_type' => 'BODYWEIGHT',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Lunge to March',
                'description' => 'Forward or reverse lunge into knee drive.',
                'default_rest_sec' => 60,
                'category_slug' => 'functional-training',
                'primary' => ['Glutes'],
                'secondary' => ['Quadriceps', 'Abs'],
                'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                'target_region' => 'LOWER',
                'equipment_type' => 'BODYWEIGHT',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Cross Lunge',
                'description' => 'Diagonal lunge targeting glutes.',
                'default_rest_sec' => 60,
                'category_slug' => 'functional-training',
                'primary' => ['Glutes'],
                'secondary' => ['Quadriceps'],
                'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                'target_region' => 'LOWER',
                'equipment_type' => 'BODYWEIGHT',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Overhead Dumbbell March',
                'description' => 'Walking march holding dumbbells overhead.',
                'default_rest_sec' => 60,
                'category_slug' => 'functional-training',
                'primary' => ['Front Delts'],
                'secondary' => ['Abs', 'Traps'],
                'movement_pattern' => 'CARRY',
                'target_region' => 'FULL_BODY',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Renegade Row',
                'description' => 'Plank row using dumbbells.',
                'default_rest_sec' => 75,
                'category_slug' => 'functional-training',
                'primary' => ['Lats'],
                'secondary' => ['Abs', 'Biceps'],
                'movement_pattern' => 'ROW',
                'target_region' => 'FULL_BODY',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'HORIZONTAL',
            ],

            [
                'name' => 'Power Snatch',
                'description' => 'Explosive lift from floor to overhead.',
                'default_rest_sec' => 180,
                'category_slug' => 'power-olympic-lifting',
                'primary' => ['Glutes'],
                'secondary' => ['Traps', 'Delts'],
                'movement_pattern' => 'HINGE',
                'target_region' => 'FULL_BODY',
                'equipment_type' => 'BARBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Dumbbell Thruster',
                'description' => 'Squat into overhead press.',
                'default_rest_sec' => 90,
                'category_slug' => 'hybrid-crossfit',
                'primary' => ['Quadriceps'],
                'secondary' => ['Delts', 'Triceps'],
                'movement_pattern' => 'SQUAT',
                'target_region' => 'FULL_BODY',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Dumbbell Step-Up',
                'description' => 'Step onto elevated surface holding dumbbells.',
                'default_rest_sec' => 60,
                'category_slug' => 'strength-training',
                'primary' => ['Quadriceps'],
                'secondary' => ['Glutes'],
                'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                'target_region' => 'LOWER',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Weighted Squat Jump',
                'description' => 'Explosive squat jump holding weight.',
                'default_rest_sec' => 90,
                'category_slug' => 'power-olympic-lifting',
                'primary' => ['Quadriceps'],
                'secondary' => ['Glutes', 'Calves'],
                'movement_pattern' => 'SQUAT',
                'target_region' => 'LOWER',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Dumbbell Overhead Triceps Extension',
                'description' => 'Overhead elbow extension.',
                'default_rest_sec' => 45,
                'category_slug' => 'strength-training',
                'primary' => ['Triceps'],
                'secondary' => [],
                'movement_pattern' => 'ELBOW_EXTENSION',
                'target_region' => 'ARMS',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Dumbbell Triceps Kickback',
                'description' => 'Hip hinge triceps extension.',
                'default_rest_sec' => 45,
                'category_slug' => 'strength-training',
                'primary' => ['Triceps'],
                'secondary' => [],
                'movement_pattern' => 'ELBOW_EXTENSION',
                'target_region' => 'ARMS',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'HORIZONTAL',
            ],

            [
                'name' => 'Dumbbell Biceps Curl',
                'description' => 'Elbow flexion exercise.',
                'default_rest_sec' => 45,
                'category_slug' => 'strength-training',
                'primary' => ['Biceps'],
                'secondary' => [],
                'movement_pattern' => 'ELBOW_FLEXION',
                'target_region' => 'ARMS',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Hammer Curl',
                'description' => 'Neutral grip dumbbell curl.',
                'default_rest_sec' => 45,
                'category_slug' => 'strength-training',
                'primary' => ['Biceps'],
                'secondary' => ['Forearms'],
                'movement_pattern' => 'ELBOW_FLEXION',
                'target_region' => 'ARMS',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Narrow Squat',
                'description' => 'Close stance squat.',
                'default_rest_sec' => 60,
                'category_slug' => 'strength-training',
                'primary' => ['Quadriceps'],
                'secondary' => ['Glutes'],
                'movement_pattern' => 'SQUAT',
                'target_region' => 'LOWER',
                'equipment_type' => 'BODYWEIGHT',
                'angle' => 'VERTICAL',
            ],

            [
                'name' => 'Single Leg Hip Thrust',
                'description' => 'Single leg hip bridge/thrust.',
                'default_rest_sec' => 60,
                'category_slug' => 'strength-training',
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings'],
                'movement_pattern' => 'HIP_THRUST_BRIDGE',
                'target_region' => 'LOWER',
                'equipment_type' => 'BODYWEIGHT',
                'angle' => 'HORIZONTAL',
            ],

            [
                'name' => 'Single Leg Deadlift',
                'description' => 'Unilateral hinge movement.',
                'default_rest_sec' => 60,
                'category_slug' => 'strength-training',
                'primary' => ['Glutes'],
                'secondary' => ['Hamstrings'],
                'movement_pattern' => 'HINGE',
                'target_region' => 'LOWER',
                'equipment_type' => 'DUMBBELL',
                'angle' => 'HORIZONTAL',
            ],

                [
                    'name' => 'Kettlebell Swing',
                    'description' => 'Explosive hip hinge swinging kettlebell to chest height.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'power-olympic-lifting',
                    'primary' => ['Glutes'],
                    'secondary' => ['Hamstrings', 'Lower Back'],
                    'movement_pattern' => 'HINGE',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Single Arm Kettlebell Swing',
                    'description' => 'One-arm explosive kettlebell swing.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'power-olympic-lifting',
                    'primary' => ['Glutes'],
                    'secondary' => ['Obliques', 'Hamstrings'],
                    'movement_pattern' => 'HINGE',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Goblet Squat',
                    'description' => 'Squat holding kettlebell at chest.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'strength-training',
                    'primary' => ['Quadriceps'],
                    'secondary' => ['Glutes', 'Abs'],
                    'movement_pattern' => 'SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'Kettlebell Deadlift',
                    'description' => 'Hip hinge lifting kettlebell from floor.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'strength-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Hamstrings', 'Lower Back'],
                    'movement_pattern' => 'HINGE',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Single Leg Kettlebell Deadlift',
                    'description' => 'Unilateral kettlebell hinge.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'strength-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Hamstrings', 'Abs'],
                    'movement_pattern' => 'HINGE',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Kettlebell Windmill',
                    'description' => 'Rotational hinge with kettlebell overhead.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Obliques'],
                    'secondary' => ['Shoulders', 'Glutes'],
                    'movement_pattern' => 'ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'Kettlebell Lateral Lunge',
                    'description' => 'Side lunge holding kettlebell.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Quadriceps', 'Adductors'],
                    'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'Kettlebell Reverse Lunge',
                    'description' => 'Reverse lunge holding kettlebell.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Quadriceps'],
                    'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'Kettlebell Swing to Lateral Lunge',
                    'description' => 'Swing followed by lateral lunge.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'hybrid-crossfit',
                    'primary' => ['Glutes'],
                    'secondary' => ['Quadriceps', 'Obliques'],
                    'movement_pattern' => 'HINGE',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Sumo Squat (Kettlebell)',
                    'description' => 'Wide stance squat holding kettlebell.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'strength-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Quadriceps'],
                    'movement_pattern' => 'SQUAT',
                    'target_region' => 'LOWER',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'Kettlebell Halo to Reverse Lunge',
                    'description' => 'Halo around head followed by reverse lunge.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Shoulders', 'Abs'],
                    'movement_pattern' => 'LUNGE_SPLIT_SQUAT',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ],

                [
                    'name' => 'Kettlebell Figure 8',
                    'description' => 'Passing kettlebell between legs in figure 8 pattern.',
                    'default_rest_sec' => 60,
                    'category_slug' => 'functional-training',
                    'primary' => ['Obliques'],
                    'secondary' => ['Glutes', 'Abs'],
                    'movement_pattern' => 'ROTATION',
                    'target_region' => 'CORE',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Kettlebell Gorilla Row',
                    'description' => 'Bent-over alternating kettlebell rows.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'strength-training',
                    'primary' => ['Lats'],
                    'secondary' => ['Biceps', 'Upper Back'],
                    'movement_pattern' => 'ROW',
                    'target_region' => 'UPPER_PULL',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Single Leg Bridge Pullover',
                    'description' => 'Bridge combined with pullover movement.',
                    'default_rest_sec' => 75,
                    'category_slug' => 'functional-training',
                    'primary' => ['Glutes'],
                    'secondary' => ['Lats', 'Abs'],
                    'movement_pattern' => 'HIP_THRUST_BRIDGE',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'HORIZONTAL',
                ],

                [
                    'name' => 'Kettlebell Clean and Press',
                    'description' => 'Clean kettlebell to shoulder then press overhead.',
                    'default_rest_sec' => 90,
                    'category_slug' => 'power-olympic-lifting',
                    'primary' => ['Shoulders'],
                    'secondary' => ['Glutes', 'Triceps'],
                    'movement_pattern' => 'PRESS',
                    'target_region' => 'FULL_BODY',
                    'equipment_type' => 'KETTLEBELL',
                    'angle' => 'VERTICAL',
                ]


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
