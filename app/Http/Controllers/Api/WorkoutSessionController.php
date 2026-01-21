<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddSessionExerciseRequest;
use App\Http\Requests\LogSetRequest;
use App\Http\Requests\ReorderSessionExercisesRequest;
use App\Http\Requests\StartWorkoutSessionRequest;
use App\Http\Requests\UpdateSessionExerciseRequest;
use App\Http\Requests\UpdateSetRequest;
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

        $sessions = WorkoutSession::query()
            ->select(['id', 'user_id', 'workout_template_id', 'performed_at', 'completed_at'])
            ->where('user_id', Auth::id())
            ->with('workoutTemplate:id,name')
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
        $template = WorkoutTemplate::whereHas('plan', function ($query) {
            $query->where('user_id', Auth::id());
        })
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
    public function start(StartWorkoutSessionRequest $request): JsonResponse
    {
        $today = Carbon::now();

        // Check if an active session already exists for today
        $session = WorkoutSession::where('user_id', Auth::id())
            ->whereDate('performed_at', $today->toDateString())
            ->whereNull('completed_at')
            ->first();

        if (! $session) {
            $session = DB::transaction(function () use ($request, $today) {
                $newSession = WorkoutSession::create([
                    'user_id' => Auth::id(),
                    'workout_template_id' => $request->template_id,
                    'performed_at' => $today,
                ]);

                // Snapshot template exercises if template is provided
                if ($request->template_id) {
                    $template = WorkoutTemplate::with('workoutTemplateExercises')->find($request->template_id);

                    if ($template && $template->workoutTemplateExercises->isNotEmpty()) {
                        // Bulk insert instead of individual creates
                        $now = now();
                        $exercisesToInsert = $template->workoutTemplateExercises->map(function ($templateExercise) use ($newSession, $now) {
                            return [
                                'workout_session_id' => $newSession->id,
                                'exercise_id' => $templateExercise->exercise_id,
                                'order' => $templateExercise->order,
                                'target_sets' => $templateExercise->target_sets,
                                'target_reps' => $templateExercise->target_reps,
                                'target_weight' => $templateExercise->target_weight,
                                'rest_seconds' => $templateExercise->rest_seconds,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        })->toArray();

                        WorkoutSessionExercise::insert($exercisesToInsert);
                    }
                }

                return $newSession;
            });
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
        $this->authorize('view', $session);

        $session->load([
            'workoutSessionExercises.exercise.category',
            'setLogs' => fn ($q) => $q->orderBy('set_number'),
        ]);

        return response()->json([
            'data' => new WorkoutSessionResource($session),
        ]);
    }

    /**
     * Log a set
     */
    public function logSet(LogSetRequest $request, WorkoutSession $session): JsonResponse
    {
        $this->authorize('update', $session);

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
    public function updateSet(UpdateSetRequest $request, WorkoutSession $session, SetLog $setLog): JsonResponse
    {
        $this->authorize('update', $session);

        if ($setLog->workout_session_id !== $session->id) {
            abort(403, 'Set log does not belong to this session.');
        }

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
        $this->authorize('update', $session);

        if ($setLog->workout_session_id !== $session->id) {
            abort(403, 'Set log does not belong to this session.');
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
        $this->authorize('update', $session);

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
        $this->authorize('delete', $session);

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
    public function addExercise(AddSessionExerciseRequest $request, WorkoutSession $session): JsonResponse
    {
        $this->authorize('update', $session);

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
        $this->authorize('update', $session);

        if ($exercise->workout_session_id !== $session->id) {
            abort(403, 'Exercise does not belong to this session.');
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
    public function updateExercise(UpdateSessionExerciseRequest $request, WorkoutSession $session, WorkoutSessionExercise $exercise): JsonResponse
    {
        $this->authorize('update', $session);

        if ($exercise->workout_session_id !== $session->id) {
            abort(403, 'Exercise does not belong to this session.');
        }

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
    public function reorderExercises(ReorderSessionExercisesRequest $request, WorkoutSession $session): JsonResponse
    {
        $this->authorize('update', $session);

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
