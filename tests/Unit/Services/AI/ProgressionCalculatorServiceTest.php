<?php

namespace Tests\Unit\Services\AI;

use App\Enums\TrainingExperience;
use App\Models\Exercise;
use App\Models\SetLog;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutSession;
use App\Services\AI\ProgressionCalculatorService;
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

    public function test_estimate_one_rep_max(): void
    {
        // Epley formula: 1RM = weight × (1 + reps/30)
        // 100kg × 10 reps = 100 × (1 + 10/30) = 100 × 1.333 = 133.3kg
        $oneRM = $this->service->estimateOneRepMax(100.0, 10);
        $this->assertEqualsWithDelta(133.33, $oneRM, 0.1);

        // Single rep should return same weight
        $oneRM = $this->service->estimateOneRepMax(100.0, 1);
        $this->assertEquals(100.0, $oneRM);
    }

    public function test_apply_progressive_overload_beginner(): void
    {
        $lastPerformance = [
            'weight' => 100.0,
            'reps' => 10,
            'sets' => 3,
        ];

        $result = $this->service->applyProgressiveOverload($lastPerformance, TrainingExperience::Beginner);

        // Beginner gets 5% increase
        $this->assertEqualsWithDelta(105.0, $result['weight'], 0.1);
        $this->assertEquals(10, $result['reps']);
        $this->assertEquals(3, $result['sets']);
    }

    public function test_apply_progressive_overload_intermediate(): void
    {
        $lastPerformance = [
            'weight' => 100.0,
            'reps' => 10,
            'sets' => 3,
        ];

        $result = $this->service->applyProgressiveOverload($lastPerformance, TrainingExperience::Intermediate);

        // Intermediate gets 2.5% increase
        $this->assertEqualsWithDelta(102.5, $result['weight'], 0.1);
        $this->assertEquals(10, $result['reps']);
        $this->assertEquals(3, $result['sets']);
    }

    public function test_apply_progressive_overload_advanced(): void
    {
        $lastPerformance = [
            'weight' => 100.0,
            'reps' => 10,
            'sets' => 3,
        ];

        $result = $this->service->applyProgressiveOverload($lastPerformance, TrainingExperience::Advanced);

        // Advanced gets 2% increase
        $this->assertEqualsWithDelta(102.0, $result['weight'], 0.1);
        $this->assertEquals(10, $result['reps']);
        $this->assertEquals(3, $result['sets']);
    }

    public function test_get_last_performance(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create();

        $session = WorkoutSession::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        SetLog::factory()->create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight' => 100.0,
            'reps' => 10,
        ]);

        SetLog::factory()->create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 2,
            'weight' => 100.0,
            'reps' => 8,
        ]);

        $lastPerformance = $this->service->getLastPerformance($exercise, $user);

        $this->assertNotNull($lastPerformance);
        $this->assertEquals(100.0, $lastPerformance['weight']);
        $this->assertEquals(10, $lastPerformance['reps']); // Best set (highest weight × reps)
    }

    public function test_get_last_performance_returns_null_when_no_history(): void
    {
        $user = User::factory()->create();
        $exercise = Exercise::factory()->create();

        $lastPerformance = $this->service->getLastPerformance($exercise, $user);

        $this->assertNull($lastPerformance);
    }

    public function test_calculate_targets_with_history(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        $exercise = Exercise::factory()->create([
            'default_rest_sec' => 90,
        ]);

        $session = WorkoutSession::factory()->create([
            'user_id' => $user->id,
            'completed_at' => now(),
        ]);

        SetLog::factory()->create([
            'workout_session_id' => $session->id,
            'exercise_id' => $exercise->id,
            'set_number' => 1,
            'weight' => 100.0,
            'reps' => 10,
            'rest_seconds' => 90,
        ]);

        $targets = $this->service->calculateTargets($exercise, $user);

        $this->assertArrayHasKey('target_sets', $targets);
        $this->assertArrayHasKey('target_reps', $targets);
        $this->assertArrayHasKey('target_weight', $targets);
        $this->assertArrayHasKey('rest_seconds', $targets);
        $this->assertGreaterThan(100.0, $targets['target_weight']); // Should be increased
    }

    public function test_calculate_targets_without_history(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'training_experience' => TrainingExperience::Beginner,
        ]);

        $exercise = Exercise::factory()->create([
            'default_rest_sec' => 90,
        ]);

        $targets = $this->service->calculateTargets($exercise, $user);

        $this->assertEquals(3, $targets['target_sets']);
        $this->assertEquals(10, $targets['target_reps']);
        $this->assertEquals(0, $targets['target_weight']);
        $this->assertEquals(90, $targets['rest_seconds']);
    }
}
