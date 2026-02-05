<?php

namespace App\Http\Controllers\Api;

use App\Enums\PlanType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomPlanRequest;
use App\Http\Requests\Api\UpdateCustomPlanRequest;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\Api\CustomPlanResource;
use App\Http\Resources\Api\PlanResource;
use App\Http\Resources\Api\ProgramResource;
use App\Http\Resources\Api\WorkoutTemplateResource;
use App\Models\Plan;
use App\Services\PlanCloningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->latest()
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

    // ===============================================
    // CUSTOM PLANS API - User-created flexible workouts
    // ===============================================

    /**
     * Display a listing of the user's custom plans.
     */
    public function customPlansIndex(): AnonymousResourceCollection
    {
        $customPlans = Plan::where('user_id', auth()->id())
            ->where('type', PlanType::Custom)
            ->with(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')])
            ->latest()
            ->get();

        return CustomPlanResource::collection($customPlans);
    }

    /**
     * Store a newly created custom plan in storage.
     */
    public function customPlansStore(StoreCustomPlanRequest $request): JsonResponse
    {
        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->update(['is_active' => false]);
        }

        $customPlan = Plan::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
            'type' => PlanType::Custom,
        ]);

        return response()->json([
            'message' => 'Custom plan created successfully',
            'data' => new CustomPlanResource($customPlan),
        ], 201);
    }

    /**
     * Display the custom plan.
     */
    public function customPlansShow(Plan $customPlan): JsonResponse
    {
        // Authorization check
        if ($customPlan->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a custom plan
        if (! $customPlan->isCustom()) {
            return response()->json([
                'message' => 'Not a custom plan',
            ], 400);
        }

        $customPlan->load(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')]);

        return response()->json([
            'data' => new CustomPlanResource($customPlan),
        ]);
    }

    /**
     * Update the specified custom plan in storage.
     */
    public function customPlansUpdate(UpdateCustomPlanRequest $request, Plan $customPlan): JsonResponse
    {
        // Authorization check
        if ($customPlan->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a custom plan
        if (! $customPlan->isCustom()) {
            return response()->json([
                'message' => 'Not a custom plan',
            ], 400);
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->where('id', '!=', $customPlan->id)
                ->update(['is_active' => false]);
        }

        $customPlan->update($request->validated());

        $customPlan->load(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')]);

        return response()->json([
            'message' => 'Custom plan updated successfully',
            'data' => new CustomPlanResource($customPlan),
        ]);
    }

    /**
     * Remove the specified custom plan from storage.
     */
    public function customPlansDestroy(Plan $customPlan): JsonResponse
    {
        // Authorization check
        if ($customPlan->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a custom plan
        if (! $customPlan->isCustom()) {
            return response()->json([
                'message' => 'Not a custom plan',
            ], 400);
        }

        $customPlan->delete();

        return response()->json([
            'message' => 'Custom plan deleted successfully',
        ]);
    }

    // =======================================================
    // PROGRAMS API - Partner-provided sequential workouts
    // =======================================================

    /**
     * Display a listing of the user's programs (cloned from library).
     */
    public function programsIndex(): AnonymousResourceCollection
    {
        $programs = Plan::where('user_id', auth()->id())
            ->where('type', PlanType::Program)
            ->with(['workoutTemplates' => fn ($query) => $query->orderedByProgram()->with('exercises.category')])
            ->latest()
            ->get();

        return ProgramResource::collection($programs);
    }

    /**
     * Display a listing of partner library programs.
     */
    public function programsLibrary(): AnonymousResourceCollection
    {
        $partner = auth()->user()->partner;

        if (! $partner) {
            return ProgramResource::collection([]);
        }

        $libraryPrograms = Plan::forPartner($partner->id)
            ->where('type', PlanType::Library)
            ->with(['workoutTemplates' => fn ($query) => $query->orderedByProgram()->with('exercises.category')])
            ->latest()
            ->get();

        return ProgramResource::collection($libraryPrograms);
    }

    /**
     * Display the specified program.
     */
    public function programsShow(Plan $program): JsonResponse
    {
        // Authorization check - user's own program or partner library program
        if ($program->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        if (! $program->isProgram()) {
            return response()->json([
                'message' => 'Not a program',
            ], 400);
        }

        $program->load(['workoutTemplates' => fn ($query) => $query->orderedByProgram()->with('exercises.category')]);

        return response()->json([
            'data' => new ProgramResource($program),
        ]);
    }

    /**
     * Update the specified program (only is_active toggle allowed).
     */
    public function programsUpdate(Request $request, Plan $program): JsonResponse
    {
        // Authorization check
        if ($program->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a program
        if (! $program->isProgram()) {
            return response()->json([
                'message' => 'Not a program',
            ], 400);
        }

        // Only allow updating is_active
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->where('id', '!=', $program->id)
                ->update(['is_active' => false]);
        }

        $program->update(['is_active' => $request->is_active]);

        $program->load(['workoutTemplates' => fn ($query) => $query->orderedByProgram()->with('exercises.category')]);

        return response()->json([
            'message' => 'Program updated successfully',
            'data' => new ProgramResource($program),
        ]);
    }

    /**
     * Remove the specified program from storage.
     */
    public function programsDestroy(Plan $program): JsonResponse
    {
        // Authorization check
        if ($program->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a program
        if (! $program->isProgram()) {
            return response()->json([
                'message' => 'Not a program',
            ], 400);
        }

        $program->delete();

        return response()->json([
            'message' => 'Program deleted successfully',
        ]);
    }

    /**
     * Clone a partner library program to the user's account.
     */
    public function programsClone(Plan $program, PlanCloningService $service): JsonResponse
    {
        // Verify this is a library plan (partner-owned)
        if (! $program->isPartnerLibraryPlan()) {
            return response()->json([
                'message' => 'Only library programs can be cloned',
            ], 403);
        }

        // Verify user belongs to the same partner
        if (auth()->user()->partner_id !== $program->partner_id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $clonedPlan = $service->clone($program, auth()->user());

        return response()->json([
            'message' => 'Program cloned successfully',
            'data' => new ProgramResource($clonedPlan->load('workoutTemplates')),
        ], 201);
    }

    /**
     * Get the next workout for a program.
     */
    public function programsNextWorkout(Plan $program): JsonResponse
    {
        // Authorization check
        if ($program->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a program
        if (! $program->isProgram()) {
            return response()->json([
                'message' => 'Not a program',
            ], 400);
        }

        $nextWorkout = $program->nextWorkout(auth()->user());

        return response()->json([
            'data' => $nextWorkout ? new WorkoutTemplateResource($nextWorkout) : null,
        ]);
    }
}
