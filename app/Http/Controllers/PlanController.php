<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use App\Models\Partner;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    /**
     * Display a listing of the user's plans (user flow: plan for a specific user).
     */
    public function userPlanIndex(Request $request, User $user): View
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can view plans.');
        }

        if ($user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $plans = Plan::query()
            ->where('user_id', $user->id)
            ->withCount('workoutTemplates')
            ->orderByDesc('is_active')
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('plans.users.index', compact('user', 'partner', 'plans'));
    }

    /**
     * Show the form for creating a new plan for a user (user flow).
     */
    public function userPlanCreate(Request $request, User $user): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can create plans.');
        }

        if ($user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('plans.users.create', compact('user', 'partner'));
    }

    /**
     * Store a newly created plan in storage (user flow).
     */
    public function userPlanStore(StorePlanRequest $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        if ($user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', $user->id)
                ->update(['is_active' => false]);
        }

        $plan = Plan::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? false,
            'type' => $request->type,
            'duration_weeks' => $request->duration_weeks,
        ]);

        return redirect()->route('plans.show', $plan)
            ->with('success', 'Plan created successfully!');
    }

    /**
     * Display the specified plan (user flow: plan for a specific user).
     */
    public function userPlanShow(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can view plans.');
        }

        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $plan->load([
            'workoutTemplates' => function ($query) {
                $query->withCount('workoutTemplateExercises')
                    ->orderedByDayOfWeek();
            },
        ]);

        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // Prepare exercise data for add exercise modal
        $workoutExerciseData = [];
        foreach ($plan->workoutTemplates as $workout) {
            // Get current exercise IDs in this workout template
            $currentExerciseIds = $workout->workoutTemplateExercises()->pluck('exercise_id')->toArray();

            // Get exercises available for this partner (excluding already added ones)
            $exercises = Exercise::whereHas('partners', function ($q) use ($partner) {
                $q->where('partners.id', $partner->id);
            })
                ->whereNotIn('id', $currentExerciseIds)
                ->with(['muscleGroups', 'primaryMuscleGroups', 'equipmentType'])
                ->orderBy('name')
                ->get()
                ->map(function ($exercise) {
                    return [
                        'id' => $exercise->id,
                        'name' => $exercise->name,
                        'equipment_type_id' => $exercise->equipment_type_id,
                        'equipment_type_name' => $exercise->equipmentType?->name ?? 'Unknown',
                        'muscle_groups' => $exercise->muscleGroups->map(fn ($mg) => [
                            'id' => $mg->id,
                            'name' => $mg->name,
                        ])->values()->toArray(),
                        'primary_muscle_group_ids' => $exercise->primaryMuscleGroups->pluck('id')->values()->toArray(),
                    ];
                })
                ->values();

            $workoutExerciseData[$workout->id] = $exercises;
        }

        // Get all equipment types and muscle groups for filters
        $equipmentTypes = EquipmentType::orderBy('display_order')
            ->get(['id', 'name'])
            ->map(fn ($et) => ['id' => $et->id, 'name' => $et->name])
            ->values();

        $muscleGroups = MuscleGroup::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($mg) => ['id' => $mg->id, 'name' => $mg->name])
            ->values();

        return view('plans.users.show', compact('plan', 'partner', 'dayNames', 'workoutExerciseData', 'equipmentTypes', 'muscleGroups'));
    }

    /**
     * Show the form for editing the specified plan (user flow).
     */
    public function userPlanEdit(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can edit plans.');
        }

        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('plans.users.edit', compact('plan', 'partner'));
    }

    /**
     * Update the specified plan in storage (user flow).
     */
    public function userPlanUpdate(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        // Deactivate all other plans if this one is being set as active
        if ($request->is_active) {
            Plan::where('user_id', $plan->user_id)
                ->where('id', '!=', $plan->id)
                ->update(['is_active' => false]);
        }

        $plan->update($request->validated());

        return redirect()->route('plans.index', $plan->user)
            ->with('success', 'Plan updated successfully!');
    }

    /**
     * Remove the specified plan from storage (user flow).
     */
    public function userPlanDestroy(Request $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $user = $plan->user;
        $plan->delete();

        return redirect()->route('plans.index', $user)
            ->with('success', 'Plan deleted successfully!');
    }

    // ===============================================
    // PARTNER LIBRARY PROGRAMS (CRUD)
    // ===============================================

    /**
     * Display a listing of partner library programs.
     */
    public function index(Request $request): View
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can manage programs.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $plans = Plan::query()
            ->where('partner_id', $partner->id)
            ->whereNull('user_id')
            ->withCount('workoutTemplates')
            ->latest()
            ->paginate(15);

        return view('plans.index', compact('partner', 'plans'));
    }

    /**
     * Show the form for creating a new program (partner library).
     */
    public function create(Request $request): View
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin')) {
            abort(403);
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('plans.create', compact('partner'));
    }

    /**
     * Store a newly created program in storage (partner library).
     */
    public function store(StorePlanRequest $request): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin')) {
            abort(403);
        }

        $plan = Plan::create([
            'partner_id' => $currentUser->partner_id,
            'user_id' => null,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'duration_weeks' => $request->duration_weeks,
            'is_active' => true,
        ]);

        return redirect()
            ->route('partner.programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified program (partner library).
     */
    public function show(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin') ||
            $plan->partner_id !== $currentUser->partner_id) {
            abort(403);
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $plan->load([
            'workoutTemplates' => function ($query) {
                $query->withCount('workoutTemplateExercises')
                    ->orderedByProgram();
            },
        ]);

        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // Prepare exercise data for add exercise modal
        $workoutExerciseData = [];
        foreach ($plan->workoutTemplates as $workout) {
            // Get current exercise IDs in this workout template
            $currentExerciseIds = $workout->workoutTemplateExercises()->pluck('exercise_id')->toArray();

            // Get exercises available for this partner (excluding already added ones)
            $exercises = Exercise::whereHas('partners', function ($q) use ($partner) {
                $q->where('partners.id', $partner->id);
            })
                ->whereNotIn('id', $currentExerciseIds)
                ->with(['muscleGroups', 'primaryMuscleGroups', 'equipmentType'])
                ->orderBy('name')
                ->get()
                ->map(function ($exercise) {
                    return [
                        'id' => $exercise->id,
                        'name' => $exercise->name,
                        'equipment_type_id' => $exercise->equipment_type_id,
                        'equipment_type_name' => $exercise->equipmentType?->name ?? 'Unknown',
                        'muscle_groups' => $exercise->muscleGroups->map(fn ($mg) => [
                            'id' => $mg->id,
                            'name' => $mg->name,
                        ])->values()->toArray(),
                        'primary_muscle_group_ids' => $exercise->primaryMuscleGroups->pluck('id')->values()->toArray(),
                    ];
                })
                ->values();

            $workoutExerciseData[$workout->id] = $exercises;
        }

        // Get all equipment types and muscle groups for filters
        $equipmentTypes = EquipmentType::orderBy('display_order')
            ->get(['id', 'name'])
            ->map(fn ($et) => ['id' => $et->id, 'name' => $et->name])
            ->values();

        $muscleGroups = MuscleGroup::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($mg) => ['id' => $mg->id, 'name' => $mg->name])
            ->values();

        return view('plans.show', compact('plan', 'partner', 'dayNames', 'workoutExerciseData', 'equipmentTypes', 'muscleGroups'));
    }

    /**
     * Show the form for editing the specified program (partner library).
     */
    public function edit(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin') ||
            $plan->partner_id !== $currentUser->partner_id) {
            abort(403);
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('plans.edit', compact('plan', 'partner'));
    }

    /**
     * Update the specified program in storage (partner library).
     */
    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin') ||
            $plan->partner_id !== $currentUser->partner_id) {
            abort(403);
        }

        $plan->update($request->validated());

        return redirect()
            ->route('partner.programs.show', $plan)
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified program from storage (partner library).
     */
    public function destroy(Request $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('partner_admin') ||
            $plan->partner_id !== $currentUser->partner_id) {
            abort(403);
        }

        $plan->delete();

        return redirect()
            ->route('partner.programs.index')
            ->with('success', 'Program deleted successfully.');
    }
}
