<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Angle;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MovementPattern;
use App\Models\TargetRegion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExerciseClassificationSeeder extends Seeder
{
    /**
     * Lookup arrays populated from database.
     *
     * @var array<string, int>
     */
    private array $movementPatterns = [];

    private array $targetRegions = [];

    private array $equipmentTypes = [];

    private array $angles = [];

    /**
     * Movement pattern to target region mapping.
     *
     * @var array<string, string>
     */
    private const MOVEMENT_TO_TARGET = [
        'PRESS' => 'UPPER_PUSH',
        'FLY' => 'UPPER_PUSH',
        'PUSHUP' => 'UPPER_PUSH',
        'DIP' => 'UPPER_PUSH',
        'ROW' => 'UPPER_PULL',
        'PULL_VERTICAL' => 'UPPER_PULL',
        'PULLOVER_STRAIGHT_ARM' => 'UPPER_PULL',
        'FACE_PULL' => 'UPPER_PULL',
        'REAR_DELT_FLY' => 'UPPER_PULL',
        'SQUAT' => 'LOWER',
        'HINGE' => 'LOWER',
        'LUNGE_SPLIT_SQUAT' => 'LOWER',
        'LEG_PRESS' => 'LOWER',
        'KNEE_EXTENSION' => 'LOWER',
        'KNEE_FLEXION' => 'LOWER',
        'HIP_THRUST_BRIDGE' => 'LOWER',
        'HIP_ABDUCTION' => 'LOWER',
        'CALF_RAISE' => 'LOWER',
        'BACK_EXTENSION' => 'LOWER',
        'ELBOW_FLEXION' => 'ARMS',
        'ELBOW_EXTENSION' => 'ARMS',
        'CARRY' => 'ARMS',
        'TRUNK_FLEXION' => 'CORE',
        'ROTATION' => 'CORE',
        'ANTI_ROTATION' => 'CORE',
    ];

    /**
     * Category slug to equipment type mapping.
     *
     * @var array<string, string>
     */
    private const CATEGORY_TO_EQUIPMENT = [
        'barbell' => 'BARBELL',
        'dumbbell' => 'DUMBBELL',
        'cable' => 'CABLE',
        'machine-cable' => 'MACHINE',
        'machine-plate-loaded' => 'MACHINE',
        'bodyweight' => 'BODYWEIGHT',
        'bands' => 'BAND',
        'trx' => 'BODYWEIGHT',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->loadLookupTables();

        $processed = 0;
        $skipped = 0;

        Exercise::query()
            ->with(['category', 'primaryMuscleGroups'])
            ->chunkById(100, function ($exercises) use (&$processed, &$skipped) {
                foreach ($exercises as $exercise) {
                    $classification = $this->classifyExercise($exercise);

                    if ($classification['movement_pattern_id'] === null) {
                        $this->command->warn("Could not classify: {$exercise->name}");
                        $skipped++;

                        continue;
                    }

                    $exercise->update($classification);
                    $processed++;
                }
            });

        $this->command->info("Exercise classification complete: {$processed} classified, {$skipped} skipped.");
    }

    /**
     * Load all lookup tables into memory for fast access.
     */
    private function loadLookupTables(): void
    {
        $this->movementPatterns = MovementPattern::pluck('id', 'code')->toArray();
        $this->targetRegions = TargetRegion::pluck('id', 'code')->toArray();
        $this->equipmentTypes = EquipmentType::pluck('id', 'code')->toArray();
        $this->angles = Angle::pluck('id', 'code')->toArray();
    }

    /**
     * Classify an exercise based on name, category, and primary muscle groups.
     *
     * @return array{movement_pattern_id: ?int, target_region_id: ?int, equipment_type_id: ?int, angle_id: ?int}
     */
    private function classifyExercise(Exercise $exercise): array
    {
        $name = $exercise->name;
        $categorySlug = $exercise->category?->slug;
        $primaryMuscles = $exercise->primaryMuscleGroups->pluck('name')->toArray();

        // Determine equipment type
        $equipmentCode = $this->inferEquipmentType($name, $categorySlug);
        $equipmentTypeId = $this->equipmentTypes[$equipmentCode] ?? $this->equipmentTypes['MACHINE'];

        // Determine movement pattern
        $movementCode = $this->inferMovementPattern($name, $primaryMuscles);
        $movementPatternId = $this->movementPatterns[$movementCode] ?? null;

        // Determine target region based on movement pattern
        $targetCode = self::MOVEMENT_TO_TARGET[$movementCode] ?? 'UPPER_PUSH';
        $targetRegionId = $this->targetRegions[$targetCode] ?? $this->targetRegions['UPPER_PUSH'];

        // Determine angle (can be null)
        $angleCode = $this->inferAngle($name, $movementCode);
        $angleId = $angleCode ? ($this->angles[$angleCode] ?? null) : null;

        return [
            'movement_pattern_id' => $movementPatternId,
            'target_region_id' => $targetRegionId,
            'equipment_type_id' => $equipmentTypeId,
            'angle_id' => $angleId,
        ];
    }

    /**
     * Infer equipment type from exercise name and category slug.
     */
    private function inferEquipmentType(string $name, ?string $categorySlug): string
    {
        // Check for Smith Machine in name first (overrides category)
        if (Str::contains($name, 'Smith', true)) {
            return 'SMITH';
        }

        // Use category slug mapping
        if ($categorySlug && isset(self::CATEGORY_TO_EQUIPMENT[$categorySlug])) {
            return self::CATEGORY_TO_EQUIPMENT[$categorySlug];
        }

        // Fallback to MACHINE
        return 'MACHINE';
    }

    /**
     * Infer movement pattern from exercise name.
     *
     * @param  array<string>  $primaryMuscles
     */
    private function inferMovementPattern(string $name, array $primaryMuscles): string
    {
        $nameLower = Str::lower($name);

        // Press patterns (chest press, shoulder press, overhead press, bench press)
        if (Str::contains($nameLower, ['bench press', 'chest press', 'shoulder press', 'overhead press', 'military press'])) {
            return 'PRESS';
        }

        // Fly patterns
        if (Str::contains($nameLower, ['fly', 'pec deck', 'cable crossover'])) {
            return 'FLY';
        }

        // Push-up patterns
        if (Str::contains($nameLower, ['push-up', 'pushup', 'push up'])) {
            return 'PUSHUP';
        }

        // Dip patterns
        if (Str::contains($nameLower, ['dip'])) {
            return 'DIP';
        }

        // Row patterns
        if (Str::contains($nameLower, ['row']) && ! Str::contains($nameLower, ['narrow'])) {
            return 'ROW';
        }

        // Vertical pull patterns (pull-up, chin-up, pulldown, lat pulldown)
        if (Str::contains($nameLower, ['pull-up', 'pullup', 'chin-up', 'chinup', 'chin up', 'pulldown', 'pull-down', 'pull down'])) {
            return 'PULL_VERTICAL';
        }

        // Straight-arm pullover patterns
        if (Str::contains($nameLower, ['straight-arm', 'straight arm', 'pullover'])) {
            return 'PULLOVER_STRAIGHT_ARM';
        }

        // Face pull patterns
        if (Str::contains($nameLower, ['face pull'])) {
            return 'FACE_PULL';
        }

        // Rear delt fly patterns
        if (Str::contains($nameLower, ['reverse fly', 'rear delt fly', 'rear delt raise'])) {
            return 'REAR_DELT_FLY';
        }

        // Squat patterns
        if (Str::contains($nameLower, ['squat']) && ! Str::contains($nameLower, ['split squat'])) {
            return 'SQUAT';
        }

        // Hip hinge patterns (deadlift, rdl, rack pull)
        if (Str::contains($nameLower, ['deadlift', 'romanian', 'rdl', 'rack pull', 'good morning'])) {
            return 'HINGE';
        }

        // Lunge / Split squat patterns
        if (Str::contains($nameLower, ['lunge', 'split squat', 'bulgarian', 'step-up', 'step up'])) {
            return 'LUNGE_SPLIT_SQUAT';
        }

        // Leg press patterns
        if (Str::contains($nameLower, ['leg press'])) {
            return 'LEG_PRESS';
        }

        // Knee extension patterns
        if (Str::contains($nameLower, ['leg extension'])) {
            return 'KNEE_EXTENSION';
        }

        // Knee flexion patterns (leg curl, nordic)
        if (Str::contains($nameLower, ['leg curl', 'hamstring curl', 'nordic'])) {
            return 'KNEE_FLEXION';
        }

        // Hip thrust / bridge patterns
        if (Str::contains($nameLower, ['hip thrust', 'glute bridge', 'bridge'])) {
            return 'HIP_THRUST_BRIDGE';
        }

        // Hip abduction patterns
        if (Str::contains($nameLower, ['hip abduction', 'abductor'])) {
            return 'HIP_ABDUCTION';
        }

        // Calf raise patterns
        if (Str::contains($nameLower, ['calf raise', 'calf press'])) {
            return 'CALF_RAISE';
        }

        // Back extension patterns
        if (Str::contains($nameLower, ['back extension', 'hyperextension', 'hyper extension'])) {
            return 'BACK_EXTENSION';
        }

        // Elbow flexion patterns (biceps curls)
        if (Str::contains($nameLower, ['curl']) && ! Str::contains($nameLower, ['leg curl', 'hamstring curl'])) {
            return 'ELBOW_FLEXION';
        }

        // Elbow extension patterns (triceps)
        if (Str::contains($nameLower, ['pushdown', 'push down', 'skull crusher', 'triceps extension', 'tricep extension', 'kickback', 'overhead extension'])) {
            return 'ELBOW_EXTENSION';
        }

        // Carry patterns
        if (Str::contains($nameLower, ["farmer's walk", 'farmers walk', 'farmer walk', 'carry', 'suitcase'])) {
            return 'CARRY';
        }

        // Trunk flexion patterns (crunches, sit-ups)
        if (Str::contains($nameLower, ['crunch', 'sit-up', 'situp', 'leg raise', 'hanging knee'])) {
            return 'TRUNK_FLEXION';
        }

        // Rotation patterns
        if (Str::contains($nameLower, ['woodchopper', 'wood chop', 'russian twist', 'rotation', 'twist'])) {
            return 'ROTATION';
        }

        // Anti-rotation patterns
        if (Str::contains($nameLower, ['pallof', 'anti-rotation', 'anti rotation'])) {
            return 'ANTI_ROTATION';
        }

        // Fallback based on primary muscle groups
        return $this->inferFromMuscleGroups($primaryMuscles);
    }

    /**
     * Fallback movement pattern inference based on primary muscle groups.
     *
     * @param  array<string>  $primaryMuscles
     */
    private function inferFromMuscleGroups(array $primaryMuscles): string
    {
        $musclesLower = array_map(fn ($m) => Str::lower($m), $primaryMuscles);

        // Check for chest/shoulders/triceps -> PRESS
        if (array_intersect($musclesLower, ['chest', 'front delts', 'triceps'])) {
            return 'PRESS';
        }

        // Check for lats/upper back -> ROW
        if (array_intersect($musclesLower, ['lats', 'upper back'])) {
            return 'ROW';
        }

        // Check for quads/glutes -> SQUAT
        if (array_intersect($musclesLower, ['quadriceps', 'glutes'])) {
            return 'SQUAT';
        }

        // Check for biceps -> ELBOW_FLEXION
        if (in_array('biceps', $musclesLower, true)) {
            return 'ELBOW_FLEXION';
        }

        // Check for abs -> TRUNK_FLEXION
        if (in_array('abs', $musclesLower, true)) {
            return 'TRUNK_FLEXION';
        }

        // Check for hamstrings -> HINGE
        if (in_array('hamstrings', $musclesLower, true)) {
            return 'HINGE';
        }

        // Check for calves -> CALF_RAISE
        if (in_array('calves', $musclesLower, true)) {
            return 'CALF_RAISE';
        }

        // Check for rear delts -> REAR_DELT_FLY
        if (in_array('rear delts', $musclesLower, true)) {
            return 'REAR_DELT_FLY';
        }

        // Default fallback
        return 'PRESS';
    }

    /**
     * Infer angle from exercise name.
     */
    private function inferAngle(string $name, string $movementCode): ?string
    {
        $nameLower = Str::lower($name);

        // Incline angle
        if (Str::contains($nameLower, 'incline')) {
            return 'INCLINE';
        }

        // Decline angle
        if (Str::contains($nameLower, 'decline')) {
            return 'DECLINE';
        }

        // Low-to-high angle
        if (Str::contains($nameLower, ['low-to-high', 'low to high'])) {
            return 'LOW_TO_HIGH';
        }

        // High-to-low angle
        if (Str::contains($nameLower, ['high-to-low', 'high to low'])) {
            return 'HIGH_TO_LOW';
        }

        // Vertical angle (overhead or vertical pull movements)
        if (Str::contains($nameLower, 'overhead')) {
            return 'VERTICAL';
        }

        // Vertical pulls are inherently vertical
        if ($movementCode === 'PULL_VERTICAL') {
            return 'VERTICAL';
        }

        // Don't force any angle - return null
        return null;
    }
}
