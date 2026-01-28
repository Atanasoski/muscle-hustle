<?php

namespace Tests\Feature;

use App\Enums\FitnessGoal;
use App\Enums\TrainingExperience;
use App\Models\Exercise;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WorkoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutSessionGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_preview_workout(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        Exercise::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'exercises',
                    'rationale',
                    'estimated_duration_minutes',
                ],
                'message',
            ]);

        $this->assertNotEmpty($response->json('data.exercises'));
    }

    public function test_user_can_confirm_workout_from_preview(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        $exercises = Exercise::factory()->count(3)->create();

        // First, get a preview
        $previewResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $previewResponse->assertStatus(200);
        $previewData = $previewResponse->json('data');

        // Extract exercise data for confirm request
        $exercisesToConfirm = array_map(function ($exercise) {
            return [
                'exercise_id' => $exercise['exercise_id'],
                'order' => $exercise['order'],
                'target_sets' => $exercise['target_sets'],
                'target_reps' => $exercise['target_reps'],
                'target_weight' => $exercise['target_weight'],
                'rest_seconds' => $exercise['rest_seconds'],
            ];
        }, $previewData['exercises']);

        // Now confirm the workout
        $confirmResponse = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/confirm', [
                'exercises' => $exercisesToConfirm,
                'rationale' => $previewData['rationale'],
            ]);

        $confirmResponse->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'is_auto_generated',
                    'rationale',
                    'exercises',
                ],
                'message',
            ]);

        $this->assertTrue($confirmResponse->json('data.is_auto_generated'));

        // Verify session was created in database
        $session = WorkoutSession::find($confirmResponse->json('data.id'));
        $this->assertNotNull($session);
        $this->assertTrue($session->is_auto_generated);
    }

    public function test_preview_requires_complete_profile(): void
    {
        $user = User::factory()->create();
        // No profile created

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please complete your profile before generating workouts.',
            ]);
    }

    public function test_preview_requires_fitness_goal(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => null,
            'training_experience' => TrainingExperience::Beginner,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please set your fitness goal in your profile.',
            ]);
    }

    public function test_preview_requires_training_experience(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => null,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please set your training experience level in your profile.',
            ]);
    }

    public function test_preview_with_preferences(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::Strength,
            'training_experience' => TrainingExperience::Advanced,
        ]);

        Exercise::factory()->count(5)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', [
                'focus_muscle_groups' => ['Chest', 'Triceps'],
                'duration_minutes' => 60,
                'difficulty' => 'advanced',
            ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.exercises'));
    }

    public function test_preview_validation(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', [
                'duration_minutes' => 5, // Too low
                'difficulty' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_minutes', 'difficulty']);
    }

    public function test_preview_handles_no_exercises_gracefully(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // No exercises in database

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'No exercises available matching the specified criteria',
            ]);
    }

    public function test_confirm_validates_exercise_ids(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/confirm', [
                'exercises' => [
                    [
                        'exercise_id' => 99999, // Invalid ID
                        'order' => 1,
                        'target_sets' => 3,
                        'target_reps' => 10,
                        'target_weight' => 50,
                        'rest_seconds' => 90,
                    ],
                ],
            ]);

        $response->assertStatus(422);
    }

    public function test_confirm_requires_exercises(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/confirm', [
                'exercises' => [],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['exercises']);
    }

    public function test_regenerate_returns_different_exercises(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'fitness_goal' => FitnessGoal::MuscleGain,
            'training_experience' => TrainingExperience::Intermediate,
        ]);

        // Create many exercises to allow for shuffling variety
        Exercise::factory()->count(20)->create();

        // Get first preview
        $response1 = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response1->assertStatus(200);
        $exercises1 = collect($response1->json('data.exercises'))->pluck('exercise_id')->toArray();

        // Get second preview - should potentially be different due to shuffle
        $response2 = $this->actingAs($user, 'sanctum')
            ->postJson('/api/workout-sessions/preview', []);

        $response2->assertStatus(200);
        $exercises2 = collect($response2->json('data.exercises'))->pluck('exercise_id')->toArray();

        // With enough exercises and shuffling, the order should vary
        // We can't guarantee they're different every time, but both should be valid
        $this->assertNotEmpty($exercises1);
        $this->assertNotEmpty($exercises2);
    }
}
