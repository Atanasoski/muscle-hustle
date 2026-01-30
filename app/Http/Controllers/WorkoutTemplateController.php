<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutTemplateRequest;
use App\Http\Requests\UpdateWorkoutTemplateRequest;
use App\Models\Partner;
use App\Models\Plan;
use App\Models\WorkoutTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $dayOfWeekOptions = $this->dayOfWeekOptions();
        $dayOfWeekValue = $request->old('day_of_week');

        return view('workout-templates.create', compact('plan', 'partner', 'dayOfWeekOptions', 'dayOfWeekValue'));
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

        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dayName = $workoutTemplate->day_of_week !== null
            ? ($dayNames[$workoutTemplate->day_of_week] ?? null)
            : null;
        $exercises = $workoutTemplate->workoutTemplateExercises->sortBy('order')->values();

        return view('workout-templates.show', compact('workoutTemplate', 'partner', 'dayName', 'exercises'));
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

        $dayOfWeekOptions = $this->dayOfWeekOptions();
        $dayOfWeekValue = $request->old('day_of_week', $workoutTemplate->day_of_week);

        return view('workout-templates.edit', compact('workoutTemplate', 'partner', 'dayOfWeekOptions', 'dayOfWeekValue'));
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

        $validated = $request->validated();
        $newDay = array_key_exists('day_of_week', $validated) ? $validated['day_of_week'] : $workoutTemplate->day_of_week;
        $oldDay = $workoutTemplate->day_of_week;

        if ($newDay !== null && $newDay !== $oldDay) {
            $existing = WorkoutTemplate::where('plan_id', $workoutTemplate->plan_id)
                ->where('day_of_week', $newDay)
                ->where('id', '!=', $workoutTemplate->id)
                ->first();

            if ($existing) {
                DB::transaction(function () use ($existing, $oldDay, $workoutTemplate, $validated): void {
                    $existing->update(['day_of_week' => $oldDay]);
                    $workoutTemplate->update($validated);
                });

                return redirect()->route('workouts.show', $workoutTemplate)
                    ->with('success', 'Workout template updated successfully!');
            }
        }

        $workoutTemplate->update($validated);

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

    /**
     * Return day-of-week options for the create/edit form (value, letter, title).
     *
     * @return array<int, array{value: int|string, letter: string, title: string}>
     */
    private function dayOfWeekOptions(): array
    {
        $dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $letters = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $options = [
            ['value' => '', 'letter' => '—', 'title' => 'Unassigned'],
        ];
        foreach ($dayNames as $index => $name) {
            $options[] = [
                'value' => $index,
                'letter' => $letters[$index],
                'title' => $name,
            ];
        }

        return $options;
    }
}
