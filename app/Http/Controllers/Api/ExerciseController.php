<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Http\Resources\Api\ExerciseResource;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises (global + user's own).
     */
    public function index(): AnonymousResourceCollection
    {
        $exercises = Exercise::with('category', 'muscleGroups')
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return ExerciseResource::collection($exercises);
    }

    /**
     * Store a newly created exercise in storage.
     */
    public function store(StoreExerciseRequest $request): JsonResponse
    {
        $exercise = Exercise::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image_url' => $request->image_url,
            'default_rest_sec' => $request->default_rest_sec ?? 90,
        ]);

        return response()->json([
            'message' => 'Exercise created successfully',
            'data' => new ExerciseResource($exercise->load('category')),
        ], 201);
    }

    /**
     * Display the specified exercise.
     */
    public function show(Exercise $exercise): JsonResponse
    {
        // Authorization: can view global exercises or own exercises
        if ($exercise->user_id && $exercise->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $exercise->load('category');

        return response()->json([
            'data' => new ExerciseResource($exercise),
        ]);
    }

    /**
     * Update the specified exercise in storage.
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        // Authorization: can only edit own exercises (not global ones)
        if (! $exercise->user_id || $exercise->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only edit your own exercises.',
            ], 403);
        }

        $exercise->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image_url' => $request->image_url,
            'default_rest_sec' => $request->default_rest_sec,
        ]);

        return response()->json([
            'message' => 'Exercise updated successfully',
            'data' => new ExerciseResource($exercise->load('category')),
        ]);
    }

    /**
     * Remove the specified exercise from storage.
     */
    public function destroy(Exercise $exercise): JsonResponse
    {
        // Authorization: can only delete own exercises (not global ones)
        if (! $exercise->user_id || $exercise->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own exercises.',
            ], 403);
        }

        $exercise->delete();

        return response()->json([
            'message' => 'Exercise deleted successfully',
        ]);
    }
}
