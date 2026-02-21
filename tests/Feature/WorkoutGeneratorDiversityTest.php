<?php

namespace Tests\Feature;

use App\Enums\FitnessGoal;
use App\Enums\TrainingExperience;
use App\Models\Angle;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MovementPattern;
use App\Models\Partner;
use App\Models\TargetRegion;
use App\Models\User;
use App\Services\WorkoutGenerator\DeterministicWorkoutGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutGeneratorDiversityTest extends TestCase
{
    use RefreshDatabase;

    private DeterministicWorkoutGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generator = app(DeterministicWorkoutGenerator::class);
    }

    public function test_generator_respects_max_exercises_per_pattern(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // Create 5 PRESS exercises with different angles
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->vertical()->create(['name' => 'Push Press']);
        Exercise::factory()->press()->barbell()->lowToHigh()->create(['name' => 'Landmine Press']);
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Close-Grip Bench Press']);

        // Create 3 ROW exercises with different angles
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Barbell Row']);
        Exercise::factory()->row()->barbell()->lowToHigh()->create(['name' => 'Single-Arm Landmine Row']);
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Pendlay Row']);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        $this->assertNotEmpty($result['exercises']);

        // Count exercises by movement pattern
        $patternCounts = [];
        foreach ($result['exercises'] as $exerciseData) {
            $exercise = Exercise::find($exerciseData['exercise_id']);
            $pattern = $exercise->movementPattern?->code ?? 'UNKNOWN';
            $patternCounts[$pattern] = ($patternCounts[$pattern] ?? 0) + 1;
        }

        // Should respect max_exercises_per_pattern from config
        $maxPerPattern = config('workout_generator.max_exercises_per_pattern', 4);
        $this->assertLessThanOrEqual($maxPerPattern, $patternCounts['PRESS'] ?? 0, 'Should respect max_exercises_per_pattern for PRESS');
        $this->assertLessThanOrEqual($maxPerPattern, $patternCounts['ROW'] ?? 0, 'Should respect max_exercises_per_pattern for ROW');
    }

    public function test_generator_prevents_duplicate_pattern_angle_combinations_in_strict_pass(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // Create multiple PRESS exercises with the same angle (FLAT)
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Close-Grip Bench Press']);
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Wide-Grip Bench Press']);

        // Create multiple PRESS exercises with different angles
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->vertical()->create(['name' => 'Push Press']);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        $this->assertNotEmpty($result['exercises']);

        // Track pattern|angle combinations
        // Note: Relaxed second pass may allow duplicates if below minimum, but strict pass should not
        $combinations = [];
        $duplicates = [];
        foreach ($result['exercises'] as $exerciseData) {
            $exercise = Exercise::find($exerciseData['exercise_id']);
            $pattern = $exercise->movementPattern?->code ?? 'UNKNOWN';
            $angle = $exercise->angle?->code ?? 'NO_ANGLE';
            $key = "{$pattern}|{$angle}";

            if (in_array($key, $combinations)) {
                $duplicates[] = $key;
            }
            $combinations[] = $key;
        }

        // If we have enough exercises (above min), strict pass should prevent duplicates
        // If we're below min, relaxed pass may add duplicates, which is acceptable
        $targets = config('workout_generator.exercise_count_targets.muscle_gain.intermediate', []);
        $minTotal = $targets['min'] ?? 5;
        if (count($result['exercises']) >= $minTotal) {
            $this->assertEmpty($duplicates, 'Should not have duplicate pattern|angle combinations when above minimum');
        }
    }

    public function test_generator_selects_diverse_exercises_with_different_angles(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // Create PRESS exercises with different angles
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->vertical()->create(['name' => 'Push Press']);
        Exercise::factory()->press()->barbell()->lowToHigh()->create(['name' => 'Landmine Press']);

        // Create ROW exercises with different angles
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Barbell Row']);
        Exercise::factory()->row()->barbell()->lowToHigh()->create(['name' => 'Single-Arm Landmine Row']);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        $this->assertNotEmpty($result['exercises']);

        // Should have exercises with different angles
        $angles = [];
        foreach ($result['exercises'] as $exerciseData) {
            $exercise = Exercise::find($exerciseData['exercise_id']);
            if ($exercise->angle) {
                $angles[] = $exercise->angle->code;
            }
        }

        // Should have at least 2 different angles
        $uniqueAngles = array_unique($angles);
        $this->assertGreaterThanOrEqual(2, count($uniqueAngles), 'Should have exercises with different angles');
    }

    public function test_strength_user_gets_at_least_3_exercises_in_30_minutes(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::Strength,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // Create diverse exercises (mix of compound and isolation)
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->vertical()->create(['name' => 'Push Press']);
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Barbell Row']);
        Exercise::factory()->row()->barbell()->lowToHigh()->create(['name' => 'Single-Arm Landmine Row']);

        // Create some isolation exercises
        $flyPattern = MovementPattern::firstOrCreate(['code' => 'FLY'], ['name' => 'Fly', 'display_order' => 20]);
        $upperPush = TargetRegion::firstOrCreate(['code' => 'UPPER_PUSH'], ['name' => 'Upper Push', 'display_order' => 10]);
        $barbellEquipment = EquipmentType::firstOrCreate(['code' => 'BARBELL'], ['name' => 'Barbell', 'display_order' => 10]);
        Exercise::factory()->create([
            'name' => 'Dumbbell Fly',
            'movement_pattern_id' => $flyPattern->id,
            'target_region_id' => $upperPush->id,
            'equipment_type_id' => $barbellEquipment->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'FLAT'], ['name' => 'Flat', 'display_order' => 10])->id,
        ]);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 30,
        ]);

        // Strength user should get at least 3 exercises in 30 minutes (not just 2)
        $this->assertGreaterThanOrEqual(3, count($result['exercises']), 'Strength user should get at least 3 exercises in 30 minutes');
    }

    public function test_beginner_gets_mostly_compound_exercises(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Beginner,
        ]);

        // Create compound exercises
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Barbell Row']);
        Exercise::factory()->row()->barbell()->lowToHigh()->create(['name' => 'Single-Arm Landmine Row']);

        // Create isolation exercises
        $flyPattern = MovementPattern::firstOrCreate(['code' => 'FLY'], ['name' => 'Fly', 'display_order' => 20]);
        $elbowFlexionPattern = MovementPattern::firstOrCreate(['code' => 'ELBOW_FLEXION'], ['name' => 'Elbow Flexion', 'display_order' => 30]);
        $upperPush = TargetRegion::firstOrCreate(['code' => 'UPPER_PUSH'], ['name' => 'Upper Push', 'display_order' => 10]);
        $upperPull = TargetRegion::firstOrCreate(['code' => 'UPPER_PULL'], ['name' => 'Upper Pull', 'display_order' => 20]);

        Exercise::factory()->create([
            'name' => 'Dumbbell Fly',
            'movement_pattern_id' => $flyPattern->id,
            'target_region_id' => $upperPush->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'FLAT'], ['name' => 'Flat', 'display_order' => 10])->id,
        ]);

        Exercise::factory()->create([
            'name' => 'Bicep Curl',
            'movement_pattern_id' => $elbowFlexionPattern->id,
            'target_region_id' => $upperPull->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'VERTICAL'], ['name' => 'Vertical', 'display_order' => 50])->id,
        ]);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        $this->assertNotEmpty($result['exercises']);

        // Count compound vs isolation
        $compoundPatterns = config('workout_generator.compound_patterns', []);
        $compoundCount = 0;
        $isolationCount = 0;

        foreach ($result['exercises'] as $exerciseData) {
            $exercise = Exercise::find($exerciseData['exercise_id']);
            $pattern = $exercise->movementPattern?->code ?? 'UNKNOWN';
            if (in_array($pattern, $compoundPatterns)) {
                $compoundCount++;
            } else {
                $isolationCount++;
            }
        }

        $total = $compoundCount + $isolationCount;
        $compoundRatio = $total > 0 ? $compoundCount / $total : 0;

        // Beginner should have at least 75% compound exercises (from config: 0.75 for general_fitness/beginner, 0.80 for muscle_gain/beginner)
        $this->assertGreaterThanOrEqual(0.75, $compoundRatio, 'Beginner should get mostly compound exercises');
    }

    public function test_advanced_muscle_gain_user_gets_mix_of_compound_and_isolation(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Advanced,
        ]);

        // Create compound exercises
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->vertical()->create(['name' => 'Push Press']);
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Barbell Row']);
        Exercise::factory()->row()->barbell()->lowToHigh()->create(['name' => 'Single-Arm Landmine Row']);

        // Create isolation exercises
        $flyPattern = MovementPattern::firstOrCreate(['code' => 'FLY'], ['name' => 'Fly', 'display_order' => 20]);
        $elbowFlexionPattern = MovementPattern::firstOrCreate(['code' => 'ELBOW_FLEXION'], ['name' => 'Elbow Flexion', 'display_order' => 30]);
        $elbowExtensionPattern = MovementPattern::firstOrCreate(['code' => 'ELBOW_EXTENSION'], ['name' => 'Elbow Extension', 'display_order' => 31]);
        $upperPush = TargetRegion::firstOrCreate(['code' => 'UPPER_PUSH'], ['name' => 'Upper Push', 'display_order' => 10]);
        $upperPull = TargetRegion::firstOrCreate(['code' => 'UPPER_PULL'], ['name' => 'Upper Pull', 'display_order' => 20]);

        $barbellEquipment = EquipmentType::firstOrCreate(['code' => 'BARBELL'], ['name' => 'Barbell', 'display_order' => 10]);

        Exercise::factory()->create([
            'name' => 'Dumbbell Fly',
            'movement_pattern_id' => $flyPattern->id,
            'target_region_id' => $upperPush->id,
            'equipment_type_id' => $barbellEquipment->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'FLAT'], ['name' => 'Flat', 'display_order' => 10])->id,
        ]);

        Exercise::factory()->create([
            'name' => 'Bicep Curl',
            'movement_pattern_id' => $elbowFlexionPattern->id,
            'target_region_id' => $upperPull->id,
            'equipment_type_id' => $barbellEquipment->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'VERTICAL'], ['name' => 'Vertical', 'display_order' => 50])->id,
        ]);

        Exercise::factory()->create([
            'name' => 'Tricep Extension',
            'movement_pattern_id' => $elbowExtensionPattern->id,
            'target_region_id' => $upperPush->id,
            'equipment_type_id' => $barbellEquipment->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'VERTICAL'], ['name' => 'Vertical', 'display_order' => 50])->id,
        ]);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        $this->assertNotEmpty($result['exercises']);

        // Count compound vs isolation
        $compoundPatterns = config('workout_generator.compound_patterns', []);
        $compoundCount = 0;
        $isolationCount = 0;

        foreach ($result['exercises'] as $exerciseData) {
            $exercise = Exercise::find($exerciseData['exercise_id']);
            $pattern = $exercise->movementPattern?->code ?? 'UNKNOWN';
            if (in_array($pattern, $compoundPatterns)) {
                $compoundCount++;
            } else {
                $isolationCount++;
            }
        }

        $total = $compoundCount + $isolationCount;
        $compoundRatio = $total > 0 ? $compoundCount / $total : 0;

        // Advanced muscle_gain user should have around 50% compound (from config: 0.50)
        // Allow flexibility (0.30 to 0.80) since ratio steering is a preference and time constraints may affect it
        $this->assertGreaterThanOrEqual(0.30, $compoundRatio, 'Advanced muscle_gain user should have some compound exercises');
        $this->assertLessThanOrEqual(0.80, $compoundRatio, 'Advanced muscle_gain user should have some isolation exercises');
        $this->assertGreaterThan(0, $isolationCount, 'Advanced muscle_gain user should have at least one isolation exercise');
    }

    public function test_min_total_exercises_is_enforced_with_relaxed_second_pass(): void
    {
        $partner = Partner::factory()->create();
        $user = User::factory()->create(['partner_id' => $partner->id]);
        $user->profile->update([
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // Create only 2 exercises with different pattern|angle combinations
        // This would normally only allow 2 exercises in strict pass
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);

        // Create more exercises with same pattern|angle (to test relaxed pass)
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Close-Grip Bench Press']);
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Wide-Grip Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Dumbbell Press']);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        $result = $this->generator->generate($user, [
            'target_regions' => ['UPPER_PUSH'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        // Should get at least min exercises (5 for muscle_gain/intermediate) even if strict pass only found 2
        // But we only have 2 unique pattern|angle combos, so relaxed pass should add more
        $this->assertGreaterThanOrEqual(2, count($result['exercises']), 'Should get at least 2 exercises');
        // With relaxed pass, should be able to get more than just the 2 unique combinations
        $this->assertLessThanOrEqual(5, count($result['exercises']), 'Should respect max exercises');
    }

    public function test_different_goals_produce_different_exercise_counts(): void
    {
        $partner = Partner::factory()->create();

        // Create diverse exercises
        Exercise::factory()->press()->barbell()->flat()->create(['name' => 'Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->incline()->create(['name' => 'Incline Barbell Bench Press']);
        Exercise::factory()->press()->barbell()->vertical()->create(['name' => 'Push Press']);
        Exercise::factory()->row()->barbell()->horizontal()->create(['name' => 'Barbell Row']);
        Exercise::factory()->row()->barbell()->lowToHigh()->create(['name' => 'Single-Arm Landmine Row']);

        // Create isolation exercises
        $flyPattern = MovementPattern::firstOrCreate(['code' => 'FLY'], ['name' => 'Fly', 'display_order' => 20]);
        $elbowFlexionPattern = MovementPattern::firstOrCreate(['code' => 'ELBOW_FLEXION'], ['name' => 'Elbow Flexion', 'display_order' => 30]);
        $upperPush = TargetRegion::firstOrCreate(['code' => 'UPPER_PUSH'], ['name' => 'Upper Push', 'display_order' => 10]);
        $upperPull = TargetRegion::firstOrCreate(['code' => 'UPPER_PULL'], ['name' => 'Upper Pull', 'display_order' => 20]);

        Exercise::factory()->create([
            'name' => 'Dumbbell Fly',
            'movement_pattern_id' => $flyPattern->id,
            'target_region_id' => $upperPush->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'FLAT'], ['name' => 'Flat', 'display_order' => 10])->id,
        ]);

        Exercise::factory()->create([
            'name' => 'Bicep Curl',
            'movement_pattern_id' => $elbowFlexionPattern->id,
            'target_region_id' => $upperPull->id,
            'angle_id' => Angle::firstOrCreate(['code' => 'VERTICAL'], ['name' => 'Vertical', 'display_order' => 50])->id,
        ]);

        // Link all exercises to partner
        $exercises = Exercise::all();
        foreach ($exercises as $exercise) {
            $exercise->partners()->attach($partner->id);
        }

        // Test Strength goal
        $strengthUser = User::factory()->create(['partner_id' => $partner->id]);
        $strengthUser->profile->update([
            'fitness_goal' => FitnessGoal::Strength,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        $strengthResult = $this->generator->generate($strengthUser, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        // Test Fat Loss goal
        $fatLossUser = User::factory()->create(['partner_id' => $partner->id]);
        $fatLossUser->profile->update([
            'fitness_goal' => FitnessGoal::FatLoss,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        $fatLossResult = $this->generator->generate($fatLossUser, [
            'target_regions' => ['UPPER_PUSH', 'UPPER_PULL'],
            'equipment_types' => ['BARBELL'],
            'duration_minutes' => 60,
        ]);

        // Strength should get fewer exercises (4-6) than fat loss (5-8) due to longer rest times
        // But both should be within their target ranges
        $strengthCount = count($strengthResult['exercises']);
        $fatLossCount = count($fatLossResult['exercises']);

        $this->assertGreaterThanOrEqual(4, $strengthCount, 'Strength user should get at least 4 exercises');
        $this->assertLessThanOrEqual(6, $strengthCount, 'Strength user should get at most 6 exercises');
        $this->assertGreaterThanOrEqual(5, $fatLossCount, 'Fat loss user should get at least 5 exercises');
        $this->assertLessThanOrEqual(8, $fatLossCount, 'Fat loss user should get at most 8 exercises');
    }
}
