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
            ->with(['workoutTemplateExercises.exercise'])
            ->first();

        // Check if there's already a session for today
        $session = WorkoutSession::where('user_id', auth()->id())
            ->whereDate('performed_at', $today->toDateString())
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

        // Check if session already exists
        $session = WorkoutSession::where('user_id', auth()->id())
            ->whereDate('performed_at', $today->toDateString())
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

        $session->load(['workoutTemplate.workoutTemplateExercises.exercise', 'setLogs']);

        // Get exercises for this session
        $exercises = $session->workoutTemplate
            ? $session->workoutTemplate->workoutTemplateExercises
            : collect();

        // Get last workout data for each exercise
        $lastWorkouts = [];
        foreach ($exercises as $templateExercise) {
            $lastSet = SetLog::whereHas('workoutSession', function ($query) use ($session) {
                $query->where('user_id', auth()->id())
                    ->where('id', '!=', $session->id);
            })
                ->where('exercise_id', $templateExercise->exercise_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $lastWorkouts[$templateExercise->exercise_id] = $lastSet;
        }

        return view('workouts.session', compact('session', 'exercises', 'lastWorkouts'));
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

        SetLog::create([
            'workout_session_id' => $session->id,
            'exercise_id' => $request->exercise_id,
            'set_number' => $request->set_number,
            'weight' => $request->weight,
            'reps' => $request->reps,
            'rest_seconds' => $request->rest_seconds,
        ]);

        return response()->json(['success' => true]);
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

        $setLog->delete();

        return response()->json(['success' => true]);
    }
}
