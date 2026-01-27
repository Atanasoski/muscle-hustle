<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutTemplateRequest;
use App\Http\Requests\UpdateWorkoutTemplateRequest;
use App\Models\Partner;
use App\Models\Plan;
use App\Models\WorkoutTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkoutTemplateController extends Controller
{
    /**
     * Show the form for creating a new workout template for a plan.
     */
    public function create(Request $request, Plan $plan): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can create workout templates.');
        }

        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('workout-templates.create', compact('plan', 'partner'));
    }

    /**
     * Store a newly created workout template in storage.
     */
    public function store(StoreWorkoutTemplateRequest $request, Plan $plan): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $plan->load('user');
        if ($plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $workoutTemplate = WorkoutTemplate::create([
            'plan_id' => $plan->id,
            'name' => $request->name,
            'description' => $request->description,
            'day_of_week' => $request->day_of_week,
        ]);

        return redirect()->route('workouts.show', $workoutTemplate)
            ->with('success', 'Workout template created successfully!');
    }

    /**
     * Display the specified workout template.
     */
    public function show(Request $request, WorkoutTemplate $workoutTemplate): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can view workout templates.');
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

        return view('workout-templates.show', compact('workoutTemplate', 'partner'));
    }

    /**
     * Show the form for editing the specified workout template.
     */
    public function edit(Request $request, WorkoutTemplate $workoutTemplate): View
    {
        $currentUser = $request->user();

        // Authorization check
        if (! $currentUser->hasRole('partner_admin')) {
            abort(403, 'Only partner administrators can edit workout templates.');
        }

        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $partner = Partner::with('identity')->findOrFail($currentUser->partner_id);

        return view('workout-templates.edit', compact('workoutTemplate', 'partner'));
    }

    /**
     * Update the specified workout template in storage.
     */
    public function update(UpdateWorkoutTemplateRequest $request, WorkoutTemplate $workoutTemplate): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $workoutTemplate->update($request->validated());

        return redirect()->route('workouts.show', $workoutTemplate)
            ->with('success', 'Workout template updated successfully!');
    }

    /**
     * Remove the specified workout template from storage.
     */
    public function destroy(Request $request, WorkoutTemplate $workoutTemplate): RedirectResponse
    {
        $currentUser = $request->user();

        // Authorization check
        $workoutTemplate->load('plan.user');
        if ($workoutTemplate->plan->user->partner_id !== $currentUser->partner_id) {
            abort(403, 'Unauthorized.');
        }

        $planId = $workoutTemplate->plan_id;
        $workoutTemplate->delete();

        return redirect()->route('plans.show', $planId)
            ->with('success', 'Workout template deleted successfully!');
    }
}
