<?php

namespace App\Http\Controllers;

use App\Models\WorkoutTemplate;
use Illuminate\Http\Request;

class WorkoutPlannerController extends Controller
{
    /**
     * Show weekly workout planner
     */
    public function index()
    {
        // Get all templates for the user
        $templates = WorkoutTemplate::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        // Get templates assigned to each day (0-6 = Mon-Sun)
        $weeklyPlan = [];
        for ($i = 0; $i < 7; $i++) {
            $weeklyPlan[$i] = WorkoutTemplate::where('user_id', auth()->id())
                ->where('day_of_week', $i)
                ->first();
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('planner.workouts', compact('templates', 'weeklyPlan', 'days'));
    }

    /**
     * Assign template to a day
     */
    public function assign(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:workout_templates,id',
            'day_of_week' => 'required|integer|min:0|max:6',
        ]);

        $template = WorkoutTemplate::findOrFail($request->template_id);

        // Authorization check
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        // Remove any existing template for this day
        WorkoutTemplate::where('user_id', auth()->id())
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $template->id)
            ->update(['day_of_week' => null]);

        // Assign template to day
        $template->update(['day_of_week' => $request->day_of_week]);

        return redirect()->route('planner.workouts')
            ->with('success', 'Workout assigned successfully!');
    }

    /**
     * Unassign template from a day
     */
    public function unassign(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:workout_templates,id',
        ]);

        $template = WorkoutTemplate::findOrFail($request->template_id);

        // Authorization check
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $template->update(['day_of_week' => null]);

        return redirect()->route('planner.workouts')
            ->with('success', 'Workout unassigned successfully!');
    }
}
