<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\SetLog;
use App\Models\WorkoutSession;
use App\Models\WorkoutTemplate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkoutSessionController extends Controller
{
    /**
     * Show today's workout
     */
    public function today()
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeek === 0 ? 6 : $today->dayOfWeek - 1;

        // Get today's template
        $template = WorkoutTemplate::where('user_id', auth()->id())
            ->where('day_of_week', $dayOfWeek)
            ->with(['workoutTemplateExercises.exercise.category'])
            ->first();

        // Check if there's already an active (non-completed) session for today
        $session = WorkoutSession::where('user_id', auth()->id())
            ->whereDate('performed_at', $today->toDateString())
            ->whereNull('completed_at')
            ->first();

        return view('workouts.today', compact('template', 'session'));
    }

    /**
     * Start a new workout session
     */
    public function start(Request $request)
    {
        $request->validate([
            'template_id' => 'nullable|exists:workout_templates,id',
        ]);

        $today = Carbon::now();

        // Check if an active session already exists for today
        $session = WorkoutSession::where('user_id', auth()->id())
            ->whereDate('performed_at', $today->toDateString())
            ->whereNull('completed_at')
            ->first();

        if (! $session) {
            $session = WorkoutSession::create([
                'user_id' => auth()->id(),
                'workout_template_id' => $request->template_id,
                'performed_at' => $today,
            ]);
        }

        return redirect()->route('workouts.session', $session);
    }

    /**
     * Show active workout session
     */
    public function show(WorkoutSession $session)
    {
        // Authorization check
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $session->load(['workoutTemplate.workoutTemplateExercises.exercise.category', 'setLogs']);

        // Get exercises for this session
        $exercises = $session->workoutTemplate
            ? $session->workoutTemplate->workoutTemplateExercises
            : collect();

        // Prepare exercise data with all sets pre-calculated
        $exercisesData = [];
        foreach ($exercises as $templateExercise) {
            // Get logged sets for this exercise in current session
            $loggedSets = $session->setLogs
                ->where('exercise_id', $templateExercise->exercise_id)
                ->sortBy('set_number')
                ->keyBy('set_number');

            // Find the most recent completed workout session with this exercise
            $lastSession = WorkoutSession::where('user_id', auth()->id())
                ->where('id', '!=', $session->id)
                ->whereNotNull('completed_at')
                ->whereHas('setLogs', function ($query) use ($templateExercise) {
                    $query->where('exercise_id', $templateExercise->exercise_id);
                })
                ->orderBy('completed_at', 'desc')
                ->first();

            // Get previous sets
            $previousSets = collect();
            if ($lastSession) {
                $previousSets = SetLog::where('workout_session_id', $lastSession->id)
                    ->where('exercise_id', $templateExercise->exercise_id)
                    ->orderBy('set_number')
                    ->get()
                    ->keyBy('set_number');
            }

            // Calculate current set number (next set to complete)
            $currentSetNumber = $loggedSets->count() + 1;

            // Build sets array with all data pre-calculated
            $targetSets = $templateExercise->target_sets ?? 3;
            $sets = [];

            for ($setNum = 1; $setNum <= $targetSets; $setNum++) {
                $previousSet = $previousSets->get($setNum);
                $loggedSet = $loggedSets->get($setNum);

                $sets[] = [
                    'set_number' => $setNum,
                    'is_completed' => $loggedSet !== null,
                    'is_active' => $setNum === $currentSetNumber,
                    'is_locked' => $setNum > $currentSetNumber,
                    'previous_weight' => $previousSet?->weight,
                    'previous_reps' => $previousSet?->reps,
                    'current_weight' => $loggedSet?->weight,
                    'current_reps' => $loggedSet?->reps,
                    'logged_set_id' => $loggedSet?->id,
                    'default_weight' => $previousSet?->weight ?? $templateExercise->target_weight ?? '',
                    'default_reps' => $previousSet?->reps ?? $templateExercise->target_reps ?? '',
                ];
            }

            $exercisesData[] = [
                'template_exercise' => $templateExercise,
                'sets' => $sets,
                'rest_seconds' => $templateExercise->rest_seconds ?? 90,
                'is_completed' => $loggedSets->count() >= $targetSets,
            ];
        }

        // Calculate progress
        $totalExercises = count($exercisesData);
        $completedExercises = collect($exercisesData)->filter(fn ($ex) => $ex['is_completed'])->count();
        $progressPercent = $totalExercises > 0 ? ($completedExercises / $totalExercises) * 100 : 0;

        return view('workouts.session', compact('session', 'exercisesData', 'totalExercises', 'completedExercises', 'progressPercent'));
    }

    /**
     * Log a set
     */
    public function logSet(Request $request, WorkoutSession $session)
    {
        // Authorization check
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
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
            'success' => true,
            'set_log_id' => $setLog->id,
        ]);
    }

    /**
     * Complete workout session
     */
    public function complete(Request $request, WorkoutSession $session)
    {
        // Authorization check
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        $session->update([
            'notes' => $request->notes,
            'completed_at' => Carbon::now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Workout completed! Great job! 💪');
    }

    /**
     * Cancel a workout session
     */
    public function cancel(WorkoutSession $session): \Illuminate\Http\RedirectResponse
    {
        // Authorization check
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete all set logs first
        $session->setLogs()->delete();

        // Delete the session
        $session->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Workout cancelled.');
    }

    /**
     * Delete a set log
     */
    public function deleteSet(WorkoutSession $session, SetLog $setLog)
    {
        // Authorization check
        if ($session->user_id !== auth()->id() || $setLog->workout_session_id !== $session->id) {
            abort(403);
        }

        // Simply delete the set
        $setLog->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update a set log
     */
    public function updateSet(Request $request, WorkoutSession $session, SetLog $setLog)
    {
        // Authorization check
        if ($session->user_id !== auth()->id() || $setLog->workout_session_id !== $session->id) {
            abort(403);
        }

        $request->validate([
            'weight' => 'required|numeric|min:0',
            'reps' => 'required|integer|min:0',
        ]);

        $setLog->update([
            'weight' => $request->weight,
            'reps' => $request->reps,
        ]);

        return response()->json(['success' => true]);
    }
}
