<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateAISessionRequest;
use App\Http\Resources\Api\AIGeneratedSessionResource;
use App\Models\User;
use App\Services\AI\WorkoutGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AIWorkoutController extends Controller
{
    public function __construct(
        private WorkoutGenerationService $workoutGenerationService
    ) {}

    /**
     * Generate a personalized workout session
     */
    public function generateSession(GenerateAISessionRequest $request): JsonResponse
    {
        try {
            // $user = Auth::user();
            $user = User::find(1);

            $preferences = [
                'focus_muscle_groups' => $request->input('focus_muscle_groups'),
                'target_regions' => $request->input('target_regions'),
                'equipment_types' => $request->input('equipment_types'),
                'movement_patterns' => $request->input('movement_patterns'),
                'angles' => $request->input('angles'),
                'duration_minutes' => $request->input('duration_minutes'),
                'difficulty' => $request->input('difficulty'),
            ];

            // Remove null values
            $preferences = array_filter($preferences, fn ($value) => $value !== null);

            $session = $this->workoutGenerationService->generateSession($user, $preferences);

            return response()->json([
                'data' => new AIGeneratedSessionResource($session),
                'message' => 'Workout session generated successfully',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Workout generation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = $e->getMessage();

            // Provide user-friendly error messages
            if (str_contains($message, 'profile is required')) {
                return response()->json([
                    'message' => 'Please complete your profile before generating workouts.',
                ], 422);
            }

            if (str_contains($message, 'Fitness goal is required')) {
                return response()->json([
                    'message' => 'Please set your fitness goal in your profile.',
                ], 422);
            }

            if (str_contains($message, 'Training experience is required')) {
                return response()->json([
                    'message' => 'Please set your training experience level in your profile.',
                ], 422);
            }

            if (str_contains($message, 'No exercises available')) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return response()->json([
                'message' => 'Failed to generate workout session. Please try again later.',
            ], 500);
        }
    }
}
