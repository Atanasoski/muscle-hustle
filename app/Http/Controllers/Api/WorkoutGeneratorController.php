<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfirmWorkoutSessionRequest;
use App\Http\Requests\GenerateWorkoutSessionRequest;
use App\Http\Resources\Api\GeneratedWorkoutSessionResource;
use App\Http\Resources\Api\WorkoutPreviewResource;
use App\Models\User;
use App\Services\WorkoutGenerator\WorkoutGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkoutGeneratorController extends Controller
{
    public function __construct(
        private WorkoutGenerationService $workoutGenerationService
    ) {}

    /**
     * Generate a workout preview without creating a session
     * User can review and then confirm or regenerate
     */
    public function preview(GenerateWorkoutSessionRequest $request): JsonResponse
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

            $previewData = $this->workoutGenerationService->preview($user, $preferences);

            return response()->json([
                'data' => new WorkoutPreviewResource($previewData),
                'message' => 'Workout preview generated successfully',
            ]);
        } catch (\Exception $e) {
            return $this->handleGenerationError($e);
        }
    }

    /**
     * Confirm and create a workout session from preview data
     */
    public function confirm(ConfirmWorkoutSessionRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $exercises = $request->input('exercises');
            $rationale = $request->input('rationale');

            $session = $this->workoutGenerationService->createFromPreview($user, $exercises, $rationale);

            return response()->json([
                'data' => new GeneratedWorkoutSessionResource($session),
                'message' => 'Workout session created successfully',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Workout session creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $message = $e->getMessage();

            if (str_contains($message, 'Invalid exercise IDs')) {
                return response()->json([
                    'message' => $message,
                ], 422);
            }

            return response()->json([
                'message' => 'Failed to create workout session. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle generation errors with user-friendly messages
     */
    private function handleGenerationError(\Exception $e): JsonResponse
    {
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

        if (str_contains($message, 'No exercises')) {
            return response()->json([
                'message' => $message,
            ], 422);
        }

        return response()->json([
            'message' => 'Failed to generate workout. Please try again later.',
        ], 500);
    }
}
