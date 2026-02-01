<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutTemplateExerciseRequest;
use App\Http\Requests\UpdateWorkoutTemplateExerciseRequest;
use App\Models\EquipmentType;
use App\Models\Exercise;
use App\Models\MuscleGroup;
use App\Models\Partner;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkoutTemplateExerciseController extends Controller
{
    /**
     * Display a listing of exercises in the workout template.
     */
    public function index(Request $request, WorkoutTemplate $workoutTemplate): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can view workout exercises.');
        }

        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $workoutTemplate->load([
            'workoutTemplateExercises.exercise.category',
            'workoutTemplateExercises.exercise.muscleGroups',
        ]);

        return view('workout-template-exercises.index', compact('workoutTemplate', 'partner'));
    }

    /**
     * Show the form for creating a new exercise in the workout template.
     */
    public function create(Request $request, WorkoutTemplate $workoutTemplate): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can add exercises.');
        }

        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        // Get current exercise IDs in this workout template
        $currentExerciseIds = $workoutTemplate->workoutTemplateExercises->pluck('exercise_id')->toArray();

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

        // Get all equipment types for filter chips (as plain arrays for JS)
        $equipmentTypes = EquipmentType::orderBy('display_order')
            ->get(['id', 'name'])
            ->map(fn ($et) => ['id' => $et->id, 'name' => $et->name])
            ->values();

        // Get all muscle groups for filter chips (as plain arrays for JS)
        $muscleGroups = MuscleGroup::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($mg) => ['id' => $mg->id, 'name' => $mg->name])
            ->values();

        // Get max order for auto-increment
        $maxOrder = $workoutTemplate->workoutTemplateExercises()->max('order') ?? -1;

        return view('workout-template-exercises.create', compact(
            'workoutTemplate',
            'partner',
            'exercises',
            'equipmentTypes',
            'muscleGroups',
            'maxOrder'
        ));
    }

    /**
     * Store a newly created exercise in the workout template.
     */
    public function store(StoreWorkoutTemplateExerciseRequest $request, WorkoutTemplate $workoutTemplate): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        // Get the highest order value and increment
        $order = $request->order ?? ($workoutTemplate->workoutTemplateExercises()->max('order') ?? -1) + 1;

        WorkoutTemplateExercise::create([
            'workout_template_id' => $workoutTemplate->id,
            'exercise_id' => $request->exercise_id,
            'order' => $order,
            'target_sets' => $request->target_sets ?? 3,
            'target_reps' => $request->target_reps ?? 10,
            'target_weight' => $request->target_weight ?? 0,
            'rest_seconds' => $request->rest_seconds ?? 120,
        ]);

        return redirect()->route('workout-exercises.index', $workoutTemplate)
            ->with('success', 'Exercise added successfully!');
    }

    /**
     * Show the form for editing the specified exercise in the workout template.
     */
    public function edit(Request $request, WorkoutTemplate $workoutTemplate, WorkoutTemplateExercise $workoutTemplateExercise): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can edit exercises.');
        }

        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id || $workoutTemplateExercise->workout_template_id !== $workoutTemplate->id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        $workoutTemplateExercise->load('exercise');

        return view('workout-template-exercises.edit', compact('workoutTemplate', 'workoutTemplateExercise', 'partner'));
    }

    /**
     * Update the specified exercise in the workout template.
     */
    public function update(UpdateWorkoutTemplateExerciseRequest $request, WorkoutTemplate $workoutTemplate, WorkoutTemplateExercise $workoutTemplateExercise): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id || $workoutTemplateExercise->workout_template_id !== $workoutTemplate->id) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validated();
        $workoutTemplateExercise->update([
            'order' => $validated['order'] ?? $workoutTemplateExercise->order,
            'target_sets' => $validated['target_sets'] ?? 3,
            'target_reps' => $validated['target_reps'] ?? 10,
            'target_weight' => $validated['target_weight'] ?? 0,
            'rest_seconds' => $validated['rest_seconds'] ?? 120,
        ]);

        return redirect()->route('workout-exercises.index', $workoutTemplate)
            ->with('success', 'Exercise updated successfully!');
    }

    /**
     * Remove the specified exercise from the workout template.
     */
    public function destroy(Request $request, WorkoutTemplate $workoutTemplate, WorkoutTemplateExercise $workoutTemplateExercise): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id || $workoutTemplateExercise->workout_template_id !== $workoutTemplate->id) {
            abort(403, 'Unauthorized.');
        }

        $workoutTemplateExercise->delete();

        return redirect()->route('workout-exercises.index', $workoutTemplate)
            ->with('success', 'Exercise removed successfully!');
    }
}
