<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WorkoutTemplateResource;
use App\Models\WorkoutTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutPlannerController extends Controller
{
    /**
     * Get weekly workout planner view
     */
    public function index(): JsonResponse
    {
        // Get all templates for the user through plans
        $templates = WorkoutTemplate::whereHas('plan', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with('exercises.category')
            ->orderBy('name')
            ->get();

        // Get templates assigned to each day (0-6 = Mon-Sun)
        $weeklyPlan = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        for ($i = 0; $i < 7; $i++) {
            $assignedTemplate = WorkoutTemplate::whereHas('plan', function ($query) {
                $query->where('user_id', Auth::id());
            })
                ->where('day_of_week', $i)
                ->with('exercises.category')
                ->first();

            $weeklyPlan[] = [
                'day_of_week' => $i,
                'day_name' => $days[$i],
                'template' => $assignedTemplate ? new WorkoutTemplateResource($assignedTemplate) : null,
            ];
        }

        return response()->json([
            'data' => [
                'weekly_plan' => $weeklyPlan,
                'available_templates' => WorkoutTemplateResource::collection($templates),
            ],
        ]);
    }

    /**
     * Assign template to a day
     */
    public function assign(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:workout_templates,id',
            'day_of_week' => 'required|integer|min:0|max:6',
        ]);

        $template = WorkoutTemplate::with('plan')->findOrFail($request->template_id);

        // Authorization check
        if ($template->plan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Remove any existing template for this day
        WorkoutTemplate::whereHas('plan', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $template->id)
            ->update(['day_of_week' => null]);

        // Assign template to day
        $template->update(['day_of_week' => $request->day_of_week]);

        $template->load('exercises.category');

        return response()->json([
            'message' => 'Workout assigned successfully',
            'data' => new WorkoutTemplateResource($template),
        ]);
    }

    /**
     * Unassign template from a day
     */
    public function unassign(Request $request): JsonResponse
    {
        $request->validate([
            'template_id' => 'required|exists:workout_templates,id',
        ]);

        $template = WorkoutTemplate::with('plan')->findOrFail($request->template_id);

        // Authorization check
        if ($template->plan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $template->update(['day_of_week' => null]);

        $template->load('exercises.category');

        return response()->json([
            'message' => 'Workout unassigned successfully',
            'data' => new WorkoutTemplateResource($template),
        ]);
    }
}
