<?php

namespace App\Http\Controllers;

use App\Models\WorkoutTemplate;
use App\Models\Exercise;
use App\Models\WorkoutTemplateExercise;
use App\Http\Requests\StoreWorkoutTemplateRequest;
use App\Http\Requests\UpdateWorkoutTemplateRequest;
use Illuminate\Http\Request;

class WorkoutTemplateController extends Controller
{
    /**
     * Display a listing of workout templates.
     */
    public function index()
    {
        $templates = WorkoutTemplate::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('workout-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new workout template.
     */
    public function create()
    {
        return view('workout-templates.create');
    }

    /**
     * Store a newly created workout template.
     */
    public function store(StoreWorkoutTemplateRequest $request)
    {
        $template = WorkoutTemplate::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'description' => $request->description,
            'day_of_week' => $request->day_of_week,
        ]);

        return redirect()->route('workout-templates.edit', $template)
            ->with('success', 'Workout template created successfully!');
    }

    /**
     * Show the form for editing the workout template.
     */
    public function edit(WorkoutTemplate $workoutTemplate)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            abort(403);
        }

        $workoutTemplate->load(['workoutTemplateExercises.exercise']);
        
        // Get all available exercises (global + user's own)
        $exercises = Exercise::where(function ($query) {
            $query->whereNull('user_id')
                  ->orWhere('user_id', auth()->id());
        })->orderBy('name')->get();

        return view('workout-templates.edit', compact('workoutTemplate', 'exercises'));
    }

    /**
     * Update the workout template.
     */
    public function update(UpdateWorkoutTemplateRequest $request, WorkoutTemplate $workoutTemplate)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            abort(403);
        }

        $workoutTemplate->update($request->validated());

        return redirect()->route('workout-templates.edit', $workoutTemplate)
            ->with('success', 'Workout template updated successfully!');
    }

    /**
     * Remove the workout template.
     */
    public function destroy(WorkoutTemplate $workoutTemplate)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            abort(403);
        }

        $workoutTemplate->delete();

        return redirect()->route('workout-templates.index')
            ->with('success', 'Workout template deleted successfully!');
    }

    /**
     * Add exercise to template
     */
    public function addExercise(Request $request, WorkoutTemplate $workoutTemplate)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'target_sets' => 'nullable|integer|min:1',
            'target_reps' => 'nullable|integer|min:1',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ]);

        // Get the highest order value and increment
        $maxOrder = $workoutTemplate->workoutTemplateExercises()->max('order') ?? -1;

        WorkoutTemplateExercise::create([
            'workout_template_id' => $workoutTemplate->id,
            'exercise_id' => $request->exercise_id,
            'order' => $maxOrder + 1,
            'target_sets' => $request->target_sets,
            'target_reps' => $request->target_reps,
            'target_weight' => $request->target_weight,
            'rest_seconds' => $request->rest_seconds,
        ]);

        return redirect()->route('workout-templates.edit', $workoutTemplate)
            ->with('success', 'Exercise added successfully!');
    }

    /**
     * Remove exercise from template
     */
    public function removeExercise(WorkoutTemplate $workoutTemplate, WorkoutTemplateExercise $exercise)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id() || $exercise->workout_template_id !== $workoutTemplate->id) {
            abort(403);
        }

        $exercise->delete();

        return redirect()->route('workout-templates.edit', $workoutTemplate)
            ->with('success', 'Exercise removed successfully!');
    }

    /**
     * Update exercise order (AJAX)
     */
    public function updateOrder(Request $request, WorkoutTemplate $workoutTemplate)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:workout_template_exercises,id',
        ]);

        foreach ($request->order as $index => $id) {
            WorkoutTemplateExercise::where('id', $id)
                ->where('workout_template_id', $workoutTemplate->id)
                ->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update exercise details
     */
    public function updateExercise(Request $request, WorkoutTemplate $workoutTemplate, WorkoutTemplateExercise $exercise)
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id() || $exercise->workout_template_id !== $workoutTemplate->id) {
            abort(403);
        }

        $request->validate([
            'target_sets' => 'nullable|integer|min:1',
            'target_reps' => 'nullable|integer|min:1',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ]);

        $exercise->update($request->only(['target_sets', 'target_reps', 'target_weight', 'rest_seconds']));

        return redirect()->route('workout-templates.edit', $workoutTemplate)
            ->with('success', 'Exercise updated successfully!');
    }
}
