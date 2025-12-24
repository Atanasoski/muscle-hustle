<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutSessionCalendarRequest;
use App\Http\Resources\Api\SetLogResource;
use App\Http\Resources\Api\WorkoutSessionCalendarResource;
use App\Http\Resources\Api\WorkoutSessionExerciseResource;
use App\Http\Resources\Api\WorkoutSessionResource;
use App\Models\Exercise;
use App\Models\SetLog;
use App\Models\WorkoutSession;
use App\Models\WorkoutSessionExercise;
use App\Models\WorkoutTemplate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkoutSessionController extends Controller
{
    /**
     * Display workout sessions for the calendar view within a date range.
     */
    public function calendar(WorkoutSessionCalendarRequest $request): JsonResponse
    {
        $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();

        $sessions = WorkoutSession::where('user_id', Auth::id())
            ->with('workoutTemplate')
            ->whereBetween('performed_at', [$startDate, $endDate])
            ->orderBy('performed_at')
            ->get();

        return response()->json([
            'data' => [
                'sessions' => WorkoutSessionCalendarResource::collection($sessions),
                'date_range' => [
                    'start' => $request->start_date,
                    'end' => $request->end_date,
                ],
            ],
        ]);
    }

    /**
     * Get today's workout template and session
     */
    public function today(): JsonResponse
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek === 0 ? 6 : $today->dayOfWeek - 1;

        // Get today's template
        $template = WorkoutTemplate::where('user_id', Auth::id())
            ->where('day_of_week', $dayOfWeek)
            ->with(['workoutTemplateExercises.exercise.category'])
            ->first();

        // Check if there's already an active session for today
        $session = WorkoutSession::where('user_id', Auth::id())
            ->whereDate('performed_at', $today->toDateString())
            ->whereNull('completed_at')
            ->with(['workoutSessionExercises.exercise.category'])
            ->first();

        return response()->json([
            'data' => [
                'template' => $template,
                'session' => $session ? new WorkoutSessionResource($session) : null,
            ],
        ]);
    }

    /**
     * Start a new workout session
     */
    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'nullable|exists:workout_templates,id',
        ]);

        $today = Carbon::now();

        // Check if an active session already exists for today
        $session = WorkoutSession::where('user_id', Auth::id())
            ->whereDate('performed_at', $today->toDateString())
            ->whereNull('completed_at')
            ->first();

        if (! $session) {
            $session = WorkoutSession::create([
                'user_id' => Auth::id(),
                'workout_template_id' => $request->template_id,
                'performed_at' => $today,
            ]);

            // Snapshot template exercises if template is provided
            if ($request->template_id) {
                $template = WorkoutTemplate::with('workoutTemplateExercises')->find($request->template_id);

                if ($template) {
                    foreach ($template->workoutTemplateExercises as $templateExercise) {
                        $session->workoutSessionExercises()->create([
                            'exercise_id' => $templateExercise->exercise_id,
                            'order' => $templateExercise->order,
                            'target_sets' => $templateExercise->target_sets,
                            'target_reps' => $templateExercise->target_reps,
                            'target_weight' => $templateExercise->target_weight,
                            'rest_seconds' => $templateExercise->rest_seconds,
                        ]);
                    }
                }
            }
        }

        $session->load(['workoutSessionExercises.exercise.category', 'setLogs']);

        return response()->json([
            'data' => new WorkoutSessionResource($session),
            'message' => 'Workout session started successfully',
        ], 201);
    }

    /**
     * Show active workout session with exercises and set logs
     */
    public function show(WorkoutSession $session): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $session->load(['workoutSessionExercises.exercise.category', 'setLogs']);

        // Prepare exercise data with set information
        $exercisesData = [];
        foreach ($session->workoutSessionExercises as $sessionExercise) {
            // Get logged sets for this exercise in current session
            $loggedSets = $session->setLogs
                ->where('exercise_id', $sessionExercise->exercise_id)
                ->sortBy('set_number')
                ->values();

            // Find the most recent completed workout session with this exercise
            $lastSession = WorkoutSession::where('user_id', Auth::id())
                ->where('id', '!=', $session->id)
                ->whereNotNull('completed_at')
                ->whereHas('setLogs', function ($query) use ($sessionExercise) {
                    $query->where('exercise_id', $sessionExercise->exercise_id);
                })
                ->orderBy('completed_at', 'desc')
                ->first();

            // Get previous sets
            $previousSets = collect();
            if ($lastSession) {
                $previousSets = SetLog::where('workout_session_id', $lastSession->id)
                    ->where('exercise_id', $sessionExercise->exercise_id)
                    ->orderBy('set_number')
                    ->get();
            }

            $exercisesData[] = [
                'session_exercise' => new WorkoutSessionExerciseResource($sessionExercise),
                'logged_sets' => SetLogResource::collection($loggedSets),
                'previous_sets' => SetLogResource::collection($previousSets),
                'is_completed' => $loggedSets->count() >= ($sessionExercise->target_sets ?? 3),
            ];
        }

        // Calculate progress
        $totalExercises = count($exercisesData);
        $completedExercises = collect($exercisesData)->filter(fn ($ex) => $ex['is_completed'])->count();
        $progressPercent = $totalExercises > 0 ? ($completedExercises / $totalExercises) * 100 : 0;

        return response()->json([
            'data' => [
                'session' => new WorkoutSessionResource($session),
                'exercises' => $exercisesData,
                'progress' => [
                    'total_exercises' => $totalExercises,
                    'completed_exercises' => $completedExercises,
                    'progress_percent' => round($progressPercent, 2),
                ],
            ],
        ]);
    }

    /**
     * Log a set
     */
    public function logSet(Request $request, WorkoutSession $session): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'exercise_id' => 'required|exists:workout_exercises,id',
            'set_number' => 'required|integer|min:1',
            'weight' => 'required|numeric|min:0',
            'reps' => 'required|integer|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ]);

        $setLog = SetLog::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $request->exercise_id,
            'set_number' => $request->set_number,
            'weight' => $request->weight,
            'reps' => $request->reps,
            'rest_seconds' => $request->rest_seconds,
        ]);

        return response()->json([
            'data' => new SetLogResource($setLog),
            'message' => 'Set logged successfully',
        ], 201);
    }

    /**
     * Update a set log
     */
    public function updateSet(Request $request, WorkoutSession $session, SetLog $setLog): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id() || $setLog->workout_session_id !== $session->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'weight' => 'required|numeric|min:0',
            'reps' => 'required|integer|min:0',
        ]);

        $setLog->update([
            'weight' => $request->weight,
            'reps' => $request->reps,
        ]);

        return response()->json([
            'data' => new SetLogResource($setLog),
            'message' => 'Set updated successfully',
        ]);
    }

    /**
     * Delete a set log
     */
    public function deleteSet(WorkoutSession $session, SetLog $setLog): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id() || $setLog->workout_session_id !== $session->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Verify this is the last set for this exercise
        $lastSet = SetLog::where('workout_session_id', $session->id)
            ->where('exercise_id', $setLog->exercise_id)
            ->orderBy('set_number', 'desc')
            ->first();

        if (! $lastSet || $lastSet->id !== $setLog->id) {
            return response()->json([
                'message' => 'Only the last set can be deleted.',
            ], 422);
        }

        $setLog->delete();

        return response()->json([
            'message' => 'Set deleted successfully',
        ]);
    }

    /**
     * Complete workout session
     */
    public function complete(Request $request, WorkoutSession $session): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $session->update([
            'notes' => $request->notes,
            'completed_at' => Carbon::now(),
        ]);

        return response()->json([
            'data' => new WorkoutSessionResource($session),
            'message' => 'Workout completed! Great job! 💪',
        ]);
    }

    /**
     * Cancel a workout session
     */
    public function cancel(WorkoutSession $session): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete all set logs and session exercises
        $session->setLogs()->delete();
        $session->workoutSessionExercises()->delete();

        // Delete the session
        $session->delete();

        return response()->json([
            'message' => 'Workout cancelled successfully',
        ]);
    }

    /**
     * Add an exercise to the session
     */
    public function addExercise(Request $request, WorkoutSession $session): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'exercise_id' => 'required|exists:workout_exercises,id',
            'order' => 'nullable|integer|min:0',
            'target_sets' => 'nullable|integer|min:1',
            'target_reps' => 'nullable|integer|min:1',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ]);

        // Get the exercise to retrieve default values
        $exercise = Exercise::find($request->exercise_id);

        // If no order is specified, add to the end
        $order = $request->order ?? $session->workoutSessionExercises()->max('order') + 1;

        $sessionExercise = $session->workoutSessionExercises()->create([
            'exercise_id' => $request->exercise_id,
            'order' => $order,
            'target_sets' => $request->target_sets ?? 3,
            'target_reps' => $request->target_reps ?? 10,
            'target_weight' => $request->target_weight ?? 0,
            'rest_seconds' => $request->rest_seconds ?? $exercise->default_rest_sec ?? 90,
        ]);

        $sessionExercise->load('exercise.category');

        return response()->json([
            'data' => new WorkoutSessionExerciseResource($sessionExercise),
            'message' => 'Exercise added to session successfully',
        ], 201);
    }

    /**
     * Remove an exercise from the session
     */
    public function removeExercise(WorkoutSession $session, WorkoutSessionExercise $exercise): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id() || $exercise->workout_session_id !== $session->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete associated set logs
        SetLog::where('workout_session_id', $session->id)
            ->where('exercise_id', $exercise->exercise_id)
            ->delete();

        // Delete the exercise
        $exercise->delete();

        return response()->json([
            'message' => 'Exercise removed from session successfully',
        ]);
    }

    /**
     * Update exercise targets in the session
     */
    public function updateExercise(Request $request, WorkoutSession $session, WorkoutSessionExercise $exercise): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id() || $exercise->workout_session_id !== $session->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'order' => 'nullable|integer|min:0',
            'target_sets' => 'nullable|integer|min:1',
            'target_reps' => 'nullable|integer|min:1',
            'target_weight' => 'nullable|numeric|min:0',
            'rest_seconds' => 'nullable|integer|min:0',
        ]);

        $exercise->update($request->only([
            'order',
            'target_sets',
            'target_reps',
            'target_weight',
            'rest_seconds',
        ]));

        $exercise->load('exercise.category');

        return response()->json([
            'data' => new WorkoutSessionExerciseResource($exercise),
            'message' => 'Exercise updated successfully',
        ]);
    }

    /**
     * Reorder exercises in the session
     */
    public function reorderExercises(Request $request, WorkoutSession $session): JsonResponse
    {
        // Authorization check
        if ($session->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'exercise_ids' => 'required|array',
            'exercise_ids.*' => 'required|exists:workout_session_exercises,id',
        ]);

        DB::transaction(function () use ($request, $session) {
            foreach ($request->exercise_ids as $order => $exerciseId) {
                WorkoutSessionExercise::where('id', $exerciseId)
                    ->where('workout_session_id', $session->id)
                    ->update(['order' => $order]);
            }
        });

        $session->load('workoutSessionExercises.exercise.category');

        return response()->json([
            'data' => WorkoutSessionExerciseResource::collection($session->workoutSessionExercises),
            'message' => 'Exercises reordered successfully',
        ]);
    }
}
