<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkoutTemplateRequest;
use App\Http\Requests\UpdateWorkoutTemplateRequest;
use App\Models\Category;
use App\Models\Exercise;
use App\Models\WorkoutTemplate;
use App\Models\WorkoutTemplateExercise;
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

        // Get categories with their exercises (global + user's own)
        $exercises = Category::orderBy('display_order')
            ->with(['exercises' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('user_id')
                        ->orWhere('user_id', auth()->id());
                })
                    ->orderBy('name');
            }])
            ->get()
            ->filter(function ($category) {
                return $category->exercises->isNotEmpty();
            })
            ->mapWithKeys(function ($category) {
                return [$category->name => $category->exercises];
            });

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
    public function updateOrder(Request $request, WorkoutTemplate $workoutTemplate): \Illuminate\Http\JsonResponse
    {
        // Authorization check
        if ($workoutTemplate->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'order' => 'required|array',
                'order.*' => 'required|integer',
            ]);

            // Verify all IDs belong to this template
            $templateExerciseIds = $workoutTemplate->workoutTemplateExercises->pluck('id')->toArray();
            $requestIds = $validated['order'];

            $invalidIds = array_diff($requestIds, $templateExerciseIds);
            if (! empty($invalidIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid exercise IDs provided',
                ], 422);
            }

            // Update the order
            foreach ($validated['order'] as $index => $id) {
                WorkoutTemplateExercise::where('id', $id)
                    ->where('workout_template_id', $workoutTemplate->id)
                    ->update(['order' => $index]);
            }

            return response()->json(['success' => true, 'message' => 'Order updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
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
            'video_url' => 'nullable|url',
            'target_sets' => 'nullable|integer|min:1',
            'target_reps' => 'nullable|integer|min:1',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ]);

        // Update template exercise (sets, reps, weight, rest)
        $exercise->update($request->only(['target_sets', 'target_reps', 'target_weight', 'rest_seconds']));

        // Update the exercise video URL if provided
        if ($request->has('video_url')) {
            $exercise->exercise->update(['video_url' => $request->video_url]);
        }

        return redirect()->route('workout-templates.edit', $workoutTemplate)
            ->with('success', 'Exercise updated successfully!');
    }
}
