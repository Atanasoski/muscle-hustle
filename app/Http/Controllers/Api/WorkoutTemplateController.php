<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutTemplateRequest;
use App\Http\Requests\UpdateWorkoutTemplateRequest;
use App\Http\Resources\Api\WorkoutTemplateResource;
use App\Models\WorkoutTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorkoutTemplateController extends Controller
{
    /**
     * Display a listing of workout templates.
     */
    public function index(): AnonymousResourceCollection
    {
        $templates = WorkoutTemplate::where('user_id', auth()->id())
            ->with('exercises.category')
            ->orderBy('name')
            ->get();

        return WorkoutTemplateResource::collection($templates);
    }

    /**
     * Store a newly created workout template in storage.
     */
    public function store(StoreWorkoutTemplateRequest $request): JsonResponse
    {
        $template = WorkoutTemplate::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'day_of_week' => $request->day_of_week,
        ]);

        return response()->json([
            'message' => 'Workout template created successfully',
            'data' => new WorkoutTemplateResource($template),
        ], 201);
    }

    /**
     * Display the specified workout template.
     */
    public function show(WorkoutTemplate $workoutTemplate): JsonResponse
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $workoutTemplate->load('exercises.category');

        return response()->json([
            'data' => new WorkoutTemplateResource($workoutTemplate),
        ]);
    }

    /**
     * Update the specified workout template in storage.
     */
    public function update(UpdateWorkoutTemplateRequest $request, WorkoutTemplate $workoutTemplate): JsonResponse
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $workoutTemplate->update($request->validated());

        return response()->json([
            'message' => 'Workout template updated successfully',
            'data' => new WorkoutTemplateResource($workoutTemplate->load('exercises.category')),
        ]);
    }

    /**
     * Remove the specified workout template from storage.
     */
    public function destroy(WorkoutTemplate $workoutTemplate): JsonResponse
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $workoutTemplate->delete();

        return response()->json([
            'message' => 'Workout template deleted successfully',
        ]);
    }
}
