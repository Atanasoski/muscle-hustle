<?php

namespace App\Http\Controllers\Api;

use App\Enums\PlanType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRoutineRequest;
use App\Http\Requests\Api\UpdateRoutineRequest;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\Api\PlanResource;
use App\Http\Resources\Api\ProgramResource;
use App\Http\Resources\Api\RoutineResource;
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
    // ROUTINES API - User-created flexible workouts
    // ===============================================

    /**
     * Display a listing of the user's routines.
     */
    public function routinesIndex(): AnonymousResourceCollection
    {
        $routines = Plan::where('user_id', auth()->id())
            ->where('type', PlanType::Routine)
            ->with(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')])
            ->latest()
            ->get();

        return RoutineResource::collection($routines);
    }

    /**
     * Store a newly created routine in storage.
     */
    public function routinesStore(StoreRoutineRequest $request): JsonResponse
    {
        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->update(['is_active' => false]);
        }

        $routine = Plan::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
            'type' => PlanType::Routine,
        ]);

        return response()->json([
            'message' => 'Routine created successfully',
            'data' => new RoutineResource($routine),
        ], 201);
    }

    /**
     * Display the specified routine.
     */
    public function routinesShow(Plan $routine): JsonResponse
    {
        // Authorization check
        if ($routine->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a routine
        if (! $routine->isRoutine()) {
            return response()->json([
                'message' => 'Not a routine',
            ], 400);
        }

        $routine->load(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')]);

        return response()->json([
            'data' => new RoutineResource($routine),
        ]);
    }

    /**
     * Update the specified routine in storage.
     */
    public function routinesUpdate(UpdateRoutineRequest $request, Plan $routine): JsonResponse
    {
        // Authorization check
        if ($routine->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a routine
        if (! $routine->isRoutine()) {
            return response()->json([
                'message' => 'Not a routine',
            ], 400);
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', auth()->id())
                ->where('id', '!=', $routine->id)
                ->update(['is_active' => false]);
        }

        $routine->update($request->validated());

        $routine->load(['workoutTemplates' => fn ($query) => $query->orderedByDayOfWeek()->with('exercises.category')]);

        return response()->json([
            'message' => 'Routine updated successfully',
            'data' => new RoutineResource($routine),
        ]);
    }

    /**
     * Remove the specified routine from storage.
     */
    public function routinesDestroy(Plan $routine): JsonResponse
    {
        // Authorization check
        if ($routine->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // Verify it's a routine
        if (! $routine->isRoutine()) {
            return response()->json([
                'message' => 'Not a routine',
            ], 400);
        }

        $routine->delete();

        return response()->json([
            'message' => 'Routine deleted successfully',
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
            ->where('type', PlanType::Program)
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
        if ($program->user_id !== auth()->id() && ! $program->isPartnerLibraryPlan()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        // If it's a library plan, verify user belongs to the partner
        if ($program->isPartnerLibraryPlan()) {
            if (auth()->user()->partner_id !== $program->partner_id) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }
        }

        // Verify it's a program
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

        // Verify it's a program
        if (! $program->isProgram()) {
            return response()->json([
                'message' => 'Not a program',
            ], 400);
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
