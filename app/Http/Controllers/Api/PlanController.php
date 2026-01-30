<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\Api\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PlanController extends Controller
{
    /**
     * Display a listing of plans.
     */
    public function index(): AnonymousResourceCollection
    {
        $plans = Plan::where('user_id', auth()->id())
            ->with(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')])
            ->orderBy('created_at', 'desc')
            ->get();

        return PlanResource::collection($plans);
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(StorePlanRequest $request): JsonResponse
    {
        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->update(['is_active' => false]);
        }

        $plan = Plan::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
        ]);

        return response()->json([
            'message' => 'Plan created successfully',
            'data' => new PlanResource($plan),
        ], 201);
    }

    /**
     * Display the specified plan.
     */
    public function show(Plan $plan): JsonResponse
    {
        // Authorization check
        if ($plan->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $plan->load(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')]);

        return response()->json([
            'data' => new PlanResource($plan),
        ]);
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        // Authorization check
        if ($plan->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->where('id', '!=', $plan->id)
                ->update(['is_active' => false]);
        }

        $plan->update($request->validated());

        $plan->load(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')]);

        return response()->json([
            'message' => 'Plan updated successfully',
            'data' => new PlanResource($plan),
        ]);
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan): JsonResponse
    {
        // Authorization check
        if ($plan->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $plan->delete();

        return response()->json([
            'message' => 'Plan deleted successfully',
        ]);
    }
}
