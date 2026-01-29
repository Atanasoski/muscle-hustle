<?php

namespace Tests\Unit\Services;

use App\Enums\Gender;
use App\Enums\TrainingExperience;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MovementPattern;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\WorkoutGenerator\ProgressionCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressionCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProgressionCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProgressionCalculatorService;
    }

    public function test_returns_zero_weight_when_user_has_no_profile(): void
    {
        $user = User::factory()->create();
        // Delete any auto-created profile
        $user->profile()->delete();

        $exercise = $this->createExercise('SQUAT', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        $this->assertEquals(0, $targets['target_weight']);
    }

    public function test_returns_zero_weight_when_profile_has_no_body_weight(): void
    {
        $user = User::factory()->create();
        // Delete any auto-created profile and create one without weight
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => null,
        ]);

        $exercise = $this->createExercise('SQUAT', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        $this->assertEquals(0, $targets['target_weight']);
    }

    public function test_estimates_squat_weight_for_beginner_male(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80, // 80kg body weight
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('SQUAT', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 80kg * 1.0 (squat base) * 1.0 (male) * 0.6 (beginner) = 48kg
        // Barbell rounds to 2.5kg = 47.5kg
        $this->assertEquals(47.5, $targets['target_weight']);
    }

    public function test_estimates_squat_weight_for_intermediate_male(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('SQUAT', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 80kg * 1.0 * 1.0 * 1.0 = 80kg
        $this->assertEquals(80.0, $targets['target_weight']);
    }

    public function test_estimates_squat_weight_for_advanced_female(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 60,
            'gender' => Gender::Female,
        ]);

        $exercise = $this->createExercise('SQUAT', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Advanced);

        // Expected: 60kg * 1.0 (squat) * 0.65 (female) * 1.3 (advanced) = 50.7kg
        // Barbell rounds to 2.5kg = 50kg
        $this->assertEquals(50.0, $targets['target_weight']);
    }

    public function test_estimates_bench_press_weight_for_beginner_male(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('PRESS', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 80kg * 0.65 (press) * 1.0 (male) * 0.6 (beginner) = 31.2kg
        // Barbell rounds to 2.5kg = 30kg
        $this->assertEquals(30.0, $targets['target_weight']);
    }

    public function test_estimates_bicep_curl_weight_for_intermediate_male(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('ELBOW_FLEXION', 'DUMBBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 80kg * 0.15 (bicep) * 1.0 (male) * 1.0 (intermediate) = 12kg
        // Dumbbell rounds to 2kg = 12kg
        $this->assertEquals(12.0, $targets['target_weight']);
    }

    public function test_returns_zero_for_bodyweight_exercises(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('PUSHUP', 'BODYWEIGHT');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Bodyweight exercises should return 0
        $this->assertEquals(0, $targets['target_weight']);
    }

    public function test_uses_correct_rounding_for_machine_exercises(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 77, // Chosen to test rounding
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('LEG_PRESS', 'MACHINE');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 77 * 1.0 * 1.0 * 0.6 = 46.2kg
        // Machine rounds to 5kg = 45kg
        $this->assertEquals(45.0, $targets['target_weight']);
    }

    public function test_uses_correct_rounding_for_barbell_exercises(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 83, // Chosen to test rounding
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('ROW', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 83 * 0.55 * 1.0 * 0.6 = 27.39kg
        // Barbell rounds to 2.5kg = 27.5kg
        $this->assertEquals(27.5, $targets['target_weight']);
    }

    public function test_estimates_correctly_for_gender_other(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 70,
            'gender' => Gender::Other,
        ]);

        $exercise = $this->createExercise('SQUAT', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 70 * 1.0 * 0.80 (other) * 0.6 = 33.6kg
        // Rounded to nearest 2.5kg (barbell) = 32.5kg
        $this->assertEquals(32.5, $targets['target_weight']);
    }

    public function test_uses_barbell_rounding_increment(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('PRESS', 'BARBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 80 * 0.65 * 1.0 * 1.0 = 52kg
        // Barbell rounds to 2.5kg increments = 52.5kg
        $this->assertEquals(52.5, $targets['target_weight']);
    }

    public function test_uses_dumbbell_rounding_increment(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('PRESS', 'DUMBBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 80 * 0.65 * 1.0 * 1.0 = 52kg
        // Dumbbell rounds to 2kg increments = 52kg
        $this->assertEquals(52.0, $targets['target_weight']);
    }

    public function test_uses_machine_rounding_increment(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('KNEE_EXTENSION', 'MACHINE');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 80 * 0.35 * 1.0 * 1.0 = 28kg
        // Machine rounds to 5kg increments = 30kg
        $this->assertEquals(30.0, $targets['target_weight']);
    }

    public function test_uses_kettlebell_rounding_increment(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('HINGE', 'KETTLEBELL');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 80 * 1.1 * 1.0 * 0.6 = 52.8kg
        // Kettlebell rounds to 4kg increments = 52kg
        $this->assertEquals(52.0, $targets['target_weight']);
    }

    public function test_uses_cable_rounding_increment(): void
    {
        $user = User::factory()->create();
        $user->profile()->delete();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'weight' => 80,
            'gender' => Gender::Male,
        ]);

        $exercise = $this->createExercise('ELBOW_FLEXION', 'CABLE');

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 80 * 0.15 * 1.0 * 1.0 = 12kg
        // Cable rounds to 2.5kg increments = 12.5kg
        $this->assertEquals(12.5, $targets['target_weight']);
    }

    public function test_progressive_overload_uses_equipment_rounding(): void
    {
        $user = User::factory()->create();
        $exercise = $this->createExercise('PRESS', 'BARBELL');

        // Create a completed workout session with set logs
        $session = \App\Models\WorkoutSession::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        // Create a set log with 50kg - progressive overload at 2.5% = 51.25kg
        // Should round to 51.25 -> nearest 2.5kg = 52.5kg for barbell
        \App\Models\SetLog::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight' => 50,
            'reps' => 8,
        ]);

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 50kg * 1.025 = 51.25kg
        // Barbell rounds to 2.5kg = 52.5kg
        $this->assertEquals(52.5, $targets['target_weight']);
    }

    public function test_progressive_overload_uses_machine_rounding(): void
    {
        $user = User::factory()->create();
        $exercise = $this->createExercise('KNEE_EXTENSION', 'MACHINE');

        // Create a completed workout session with set logs
        $session = \App\Models\WorkoutSession::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        // Create a set log with 45kg - progressive overload at 2.5% = 46.125kg
        // Should round to nearest 5kg = 45kg for machine
        \App\Models\SetLog::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight' => 45,
            'reps' => 10,
        ]);

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Intermediate);

        // Expected: 45kg * 1.025 = 46.125kg
        // Machine rounds to 5kg = 45kg
        $this->assertEquals(45.0, $targets['target_weight']);
    }

    public function test_progressive_overload_uses_dumbbell_rounding(): void
    {
        $user = User::factory()->create();
        $exercise = $this->createExercise('PRESS', 'DUMBBELL');

        // Create a completed workout session with set logs
        $session = \App\Models\WorkoutSession::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        // Create a set log with 24kg - progressive overload at 5% (beginner) = 25.2kg
        // Should round to nearest 2kg = 26kg for dumbbell
        \App\Models\SetLog::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight' => 24,
            'reps' => 10,
        ]);

        $targets = $this->service->calculateTargets($exercise, $user->fresh(), TrainingExperience::Beginner);

        // Expected: 24kg * 1.05 = 25.2kg
        // Dumbbell rounds to 2kg = 26kg
        $this->assertEquals(26.0, $targets['target_weight']);
    }

    private function createExercise(string $movementCode, string $equipmentCode = 'BARBELL'): Exercise
    {
        $movementPattern = MovementPattern::firstOrCreate(
            ['code' => $movementCode],
            ['name' => $movementCode, 'display_order' => 1]
        );

        $equipmentType = EquipmentType::firstOrCreate(
            ['code' => $equipmentCode],
            ['name' => $equipmentCode, 'display_order' => 1]
        );

        return Exercise::factory()->create([
            'movement_pattern_id' => $movementPattern->id,
            'equipment_type_id' => $equipmentType->id,
        ]);
    }
}
