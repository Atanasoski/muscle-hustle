<?php

namespace App\Http\Controllers;

use App\Enums\CategoryType;
use App\Http\Requests\StoreWorkoutTemplateExerciseRequest;
use App\Http\Requests\UpdateWorkoutTemplateExerciseRequest;
use App\Models\Category;
use App\Models\Exercise;
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

        // Get exercises available for this partner
        $categories = Category::where('type', CategoryType::Workout)
            ->with(['exercises' => function ($query) use ($partner) {
                $query->whereHas('partners', function ($q) use ($partner) {
                    $q->where('partners.id', $partner->id);
                })
                    ->with('muscleGroups')
                    ->orderBy('name');
            }])
            ->orderBy('display_order')
            ->get();

        // Get current exercise IDs in this workout template
        $currentExerciseIds = $workoutTemplate->workoutTemplateExercises->pluck('exercise_id')->toArray();

        // Get max order for auto-increment
        $maxOrder = $workoutTemplate->workoutTemplateExercises()->max('order') ?? -1;

        return view('workout-template-exercises.create', compact('workoutTemplate', 'partner', 'categories', 'currentExerciseIds', 'maxOrder'));
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
            'target_sets' => $request->target_sets,
            'target_reps' => $request->target_reps,
            'target_weight' => $request->target_weight,
            'rest_seconds' => $request->rest_seconds,
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

        $workoutTemplateExercise->update($request->validated());

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
