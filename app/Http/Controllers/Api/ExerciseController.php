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
     * Display a listing of exercises.
     */
    public function index(): AnonymousResourceCollection
    {
        $exercises = Exercise::with('category', 'muscleGroups')
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
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image' => $request->image,
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
        $exercise->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'image' => $request->image,
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
        $exercise->delete();

        return response()->json([
            'message' => 'Exercise deleted successfully',
        ]);
    }
}
