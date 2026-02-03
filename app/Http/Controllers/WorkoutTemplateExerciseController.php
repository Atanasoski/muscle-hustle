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

        return redirect()->route('workouts.show', $workoutTemplate)
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

        return redirect()->route('workouts.show', $workoutTemplate)
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

        return redirect()->route('workouts.show', $workoutTemplate)
            ->with('success', 'Exercise removed successfully!');
    }
}
