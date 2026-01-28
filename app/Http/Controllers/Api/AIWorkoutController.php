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
     * Generate an AI-powered workout session
     */
    public function generateSession(GenerateAISessionRequest $request): JsonResponse
    {
        try {
            // $user = Auth::user();
            $user = User::find(1);

            $preferences = [
                'focus_muscle_groups' => $request->input('focus_muscle_groups'),
                'duration_minutes' => $request->input('duration_minutes'),
                'preferred_categories' => $request->input('preferred_categories'),
                'difficulty' => $request->input('difficulty'),
            ];

            // Remove null values
            $preferences = array_filter($preferences, fn ($value) => $value !== null);

            $session = $this->workoutGenerationService->generateSession($user, $preferences);

            return response()->json([
                'data' => new AIGeneratedSessionResource($session),
                'message' => 'AI workout session generated successfully',
            ], 201);
        } catch (\Exception $e) {
            Log::error('AI workout generation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = $e->getMessage();

            // Provide user-friendly error messages
            if (str_contains($message, 'profile is required')) {
                return response()->json([
                    'message' => 'Please complete your profile before generating AI workouts.',
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

            return response()->json([
                'message' => 'Failed to generate workout session. Please try again later.',
            ], 500);
        }
    }
}
