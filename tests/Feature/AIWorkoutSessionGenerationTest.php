<?php

namespace Tests\Feature;

use App\Enums\FitnessGoal;
use App\Enums\TrainingExperience;
use App\Models\Exercise;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIWorkoutSessionGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock OpenAI API response
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'exercises' => [
                                    [
                                        'exercise_id' => 1,
                                        'order' => 1,
                                        'target_sets' => 3,
                                        'target_reps' => 10,
                                        'target_weight' => 50.0,
                                        'rest_seconds' => 90,
                                    ],
                                    [
                                        'exercise_id' => 2,
                                        'order' => 2,
                                        'target_sets' => 3,
                                        'target_reps' => 12,
                                        'target_weight' => 0,
                                        'rest_seconds' => 60,
                                    ],
                                ],
                                'rationale' => 'This workout focuses on upper body strength with progressive overload.',
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);
    }

    public function test_user_can_generate_ai_workout_session(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        Exercise::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', []);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'is_ai_generated',
                    'ai_generated_at',
                    'rationale',
                    'exercises',
                ],
                'message',
            ]);

        $this->assertTrue($response->json('data.is_ai_generated'));
        $this->assertNotNull($response->json('data.ai_generated_at'));
    }

    public function test_ai_workout_generation_requires_complete_profile(): void
    {
        $user = User::factory()->create();
        // No profile created

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', []);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please complete your profile before generating AI workouts.',
            ]);
    }

    public function test_ai_workout_generation_requires_fitness_goal(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => null,
            'training_experience' => TrainingExperience::Beginner,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', []);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please set your fitness goal in your profile.',
            ]);
    }

    public function test_ai_workout_generation_requires_training_experience(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', []);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please set your training experience level in your profile.',
            ]);
    }

    public function test_ai_workout_generation_with_preferences(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::Strength,
            'training_experience' => TrainingExperience::Advanced,
        ]);

        $exercise1 = Exercise::factory()->create();
        $exercise2 = Exercise::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', [
                'focus_muscle_groups' => ['Chest', 'Triceps'],
                'duration_minutes' => 60,
                'exclude_exercises' => [$exercise1->id],
                'equipment_available' => ['barbell', 'dumbbells'],
                'difficulty' => 'advanced',
            ]);

        $response->assertStatus(201);
        $this->assertTrue($response->json('data.is_ai_generated'));
    }

    public function test_ai_workout_generation_validation(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', [
                'duration_minutes' => 5, // Too low
                'difficulty' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_minutes', 'difficulty']);
    }

    public function test_generated_session_has_exercises(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        Exercise::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', []);

        $response->assertStatus(201);

        $session = WorkoutSession::find($response->json('data.id'));
        $this->assertNotNull($session);
        $this->assertTrue($session->is_ai_generated);
        $this->assertGreaterThan(0, $session->workoutSessionExercises->count());
    }

    public function test_ai_workout_generation_handles_api_failure_gracefully(): void
    {
        Http::fake([
            'api.openai.com/v1/chat/completions' => Http::response([], 500),
        ]);

        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        Exercise::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/ai/generate', []);

        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Failed to generate workout session. Please try again later.',
            ]);
    }
}
